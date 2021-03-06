<?php
/**
 * ownCloud
 *
 * @author Jörn Friedrich Dreyer <jfd@owncloud.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright (C) 2014-2017 ownCloud, GmbH.
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Files_Primary_S3;

use Aws\Exception\AwsException;
use Aws\Exception\MultipartUploadException;
use Aws\Handler\GuzzleV5\GuzzleHandler;
use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Ring\Client\StreamHandler;
use OC\ServiceUnavailableException;
use OCP\Files\ObjectStore\IObjectStore;
use OCP\Files\ObjectStore\IVersionedObjectStorage;
use OCP\Files\ObjectStore\ObjectStoreOperationException;
use OCP\Files\ObjectStore\ObjectStoreWriteException;

require_once __DIR__ . '/../vendor/autoload.php';

class S3Storage implements IObjectStore, IVersionedObjectStorage {

	/**
	 * @var S3Client|null
	 */
	private $connection;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * S3Storage constructor.
	 *
	 * @param array $params
	 * @throws \Exception
	 */
	public function __construct($params) {
		if (!isset($params['options'], $params['bucket'])) {
			throw new \Exception($this->t('Connection options and bucket must be configured.'));
		}

		$this->params = $params;
	}

	protected function init() {
		if ($this->connection) {
			return;
		}
		$config = $this->params['options'];
		$client = new \GuzzleHttp\Client(['handler' => new StreamHandler()]);
		$emitter = $client->getEmitter();
		$emitter->on('before', static function (BeforeEvent $event) {
			$request = $event->getRequest();
			if ($request->getMethod() !== 'PUT') {
				return;
			}
			$body = $request->getBody();
			if ($body !== null && $body->getSize() !== 0) {
				return;
			}
			if ($request->hasHeader('Content-Length')) {
				return;
			}
			// force content length header on empty body
			$request->setHeader('Content-Length', '0');
		});
		$h = new GuzzleHandler($client);
		$config['http_handler'] = $h;
		/* @phan-suppress-next-line PhanDeprecatedFunction */
		$this->connection = S3Client::factory($config);
		try {
			$this->connection->listBuckets();
		} catch (S3Exception $exception) {
			\OC::$server->getLogger()->logException($exception);
			$message = $this->t('No S3 ObjectStore available');
			throw new ServiceUnavailableException($message);
		}

		// TODO: update aws sdk once https://github.com/aws/aws-sdk-php/pull/1424 is merged
//		$this->connection->registerStreamWrapper();
		StreamWrapper::register($this->connection);

		if (!$this->connection->doesBucketExist($this->getBucket())) {
			throw new \Exception($this->t('Bucket <%s> does not exist.', [$this->getBucket()]));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStorageId() {
		return $this->params['bucket'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeObject($urn, $stream) {
		$this->init();

		$opt = [];
		if (isset($this->params['serversideencryption'])) {
			$opt['ServerSideEncryption'] = $this->params['serversideencryption'];
		}
		if (isset($this->params['part_size'])) {
			$opt['part_size'] = $this->params['part_size'];
		}

		$uploader = new ObjectUploader($this->connection, $this->getBucket(), $urn, $stream, 'private', $opt);

		try {
			$uploader->upload();
		} catch (AwsException $e) {
			/**
			 * If the error is from AwsException then just wrap the aws error message
			 * to our exception.
			 */
			throw new ObjectStoreWriteException($e->getAwsErrorMessage(), $e->getStatusCode(), $e);
		} catch (MultipartUploadException $e) {
			/**
			 * There can be multiple parts that might have failed to upload. So it would be
			 * better to have a custom message here. The getMessage throws all the parts which
			 * are failed.
			 */
			\OC::$server->getLogger()->logException($e);
			$message = $this->t('Upload failed. Please ask you administrator to have a look at the log files for more details.');
			throw new ObjectStoreWriteException($message, $e->getCode(), $e);
		}

		if (\is_resource($stream)) {
			\fclose($stream);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteObject($urn) {
		$this->init();
		try {
			$this->connection->deleteObject([
				'Bucket' => $this->getBucket(),
				'Key' => $urn,
			]);
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function readObject($urn) {
		$this->init();
		try {
			return \fopen($this->getUrl($urn), 'r');
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	public function getUrl($urn, $versionId = null) {
		$v = ($versionId !== null) ?  "?versionId=$versionId": '';
		return 's3://'.$this->getBucket().'/'.$urn.$v;
	}

	private function getBucket() {
		return $this->params['bucket'];
	}

	/**
	 * List all versions for the given file
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @return array
	 * @throws ObjectStoreOperationException
	 * @since 10.0.5
	 */
	public function getVersions($urn) {
		$this->init();
		try {
			$list = $this->connection->listObjectVersions([
				'Bucket' => $this->getBucket(),
				'Prefix' => $urn
			]);
			$versions = \array_filter($list['Versions'], static function ($v) use ($urn) {
				return ($v['Key'] === $urn) && $v['IsLatest'] !== true;
			});
			return \array_map(static function ($version) {
				return [
					'version' => $version['VersionId'],
					'timestamp' => $version['LastModified']->getTimestamp(),
					'oid' => $version['Key'],
					'etag' => $version['ETag'],
					'size' => $version['Size'],
				];
			}, $versions);
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	/**
	 * Get one explicit version for the given file
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return array
	 * @throws ObjectStoreOperationException
	 * @since 10.0.5
	 */
	public function getVersion($urn, $versionId) {
		$this->init();
		try {
			$list = $this->connection->listObjectVersions([
				'Bucket' => $this->getBucket(),
				'Prefix' => $urn,
				'VersionIdMarker' => $versionId
			]);
			$versions = \array_filter($list['Versions'], static function ($v) use ($urn, $versionId) {
				return ($v['Key'] === $urn) && $v['VersionId'] === $versionId;
			});
			$version = \array_values($versions)[0];
			return [
				'version' => $version['VersionId'],
				'timestamp' => $version['LastModified']->getTimestamp(),
				'oid' => $version['Key'],
				'etag' => $version['ETag'],
				'size' => $version['Size'],
			];
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	/**
	 * Get the content of a given version of a given file as stream resource
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return resource
	 * @throws ObjectStoreOperationException
	 * @throws ServiceUnavailableException
	 * @since 10.0.5
	 */
	public function getContentOfVersion($urn, $versionId) {
		$this->init();
		try {
			return \fopen($this->getUrl($urn, $versionId), 'r');
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	/**
	 * Restore the given version of a given file
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return boolean
	 * @throws ObjectStoreOperationException
	 * @throws ServiceUnavailableException
	 * @since 10.0.5
	 */
	public function restoreVersion($urn, $versionId) {
		$this->init();
		try {
			$this->connection->copyObject([
				'Bucket' => $this->getBucket(),
				'Key' => $urn,
				'CopySource' => "/{$this->getBucket()}/" . \rawurlencode($urn) . "?versionId=$versionId"
			]);

			return true;
		} catch (AwsException $ex) {
			throw new ObjectStoreOperationException($ex->getAwsErrorMessage(), $ex->getStatusCode(), $ex);
		}
	}

	/**
	 * Tells the storage to explicitly create a version of a given file
	 *
	 * @param string $internalPath
	 * @return bool
	 * @since 10.0.5
	 */
	public function saveVersion($internalPath): bool {
		// There is no need in any explicit operations.
		// In a versioned bucket the versions are created automatically
		return true;
	}

	private function t(string $text, array $parameters = []) {
		return \OC::$server->getL10N('files_primary_s3')
			->t($text, $parameters);
	}
}
