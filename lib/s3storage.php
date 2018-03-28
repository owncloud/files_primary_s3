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

use Aws\Handler\GuzzleV5\GuzzleHandler;
use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Ring\Client\StreamHandler;
use OC\ServiceUnavailableException;
use OCP\Files\ObjectStore\IObjectStore;
use OCP\Files\ObjectStore\IVersionedObjectStorage;

require_once __DIR__ . '/../vendor/autoload.php';

class S3Storage implements IObjectStore, IVersionedObjectStorage {

	/**
	 * @var S3Client
	 */
	private $connection;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * S3Storage constructor.
	 *
	 * @param $params
	 * @throws \Exception
	 */
	public function __construct($params) {
		if (!isset($params['options']) || !isset($params['bucket'])) {
			throw new \Exception('Connection options and bucket must be configured.');
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
		$emitter->on('before', function (BeforeEvent $event) {
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
			$request->setHeader('Content-Length', 0);
		});
		$h = new GuzzleHandler($client);
		$config['http_handler'] = $h;
		$this->connection = S3Client::factory($config);
		try {
			$this->connection->listBuckets();
		} catch (S3Exception $exception) {
			\OC::$server->getLogger()->logException($exception);
			throw new ServiceUnavailableException("No S3 ObjectStore available");
		}

		// TODO: update aws sdk once https://github.com/aws/aws-sdk-php/pull/1424 is merged
//		$this->connection->registerStreamWrapper();
		StreamWrapper::register($this->connection);

		if (!$this->connection->doesBucketExist($this->getBucket())) {
			throw new \Exception('Bucket does not exist.');
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
		$uploader->upload();
		if (\is_resource($stream)) {
			\fclose($stream);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteObject($urn) {
		$this->init();
		$this->connection->deleteObject([
			'Bucket' => $this->getBucket(),
			'Key' => $urn,
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function readObject($urn) {
		$this->init();
		return \fopen($this->getUrl($urn), 'r');
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
	 * @since 10.0.5
	 */
	public function getVersions($urn) {
		$this->init();
		$list = $this->connection->listObjectVersions([
			'Bucket' => $this->getBucket(),
			'Prefix'    => $urn
		]);
		$versions = \array_filter($list['Versions'], function ($v) use ($urn) {
			return ($v['Key'] === $urn) && $v['IsLatest'] !== true;
		});
		return \array_map(function ($version) {
			return [
				'version' => $version['VersionId'],
				'timestamp' => $version['LastModified']->getTimestamp(),
				'oid' => $version['Key'],
				'etag' => $version['ETag'],
				'size' => $version['Size'],
			];
		}, $versions);
	}

	/**
	 * Get one explicit version for the given file
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return array
	 * @since 10.0.5
	 */
	public function getVersion($urn, $versionId) {
		$this->init();
		$list = $this->connection->listObjectVersions([
			'Bucket' => $this->getBucket(),
			'Prefix' => $urn,
			'VersionIdMarker' => $versionId
		]);
		$versions = \array_filter($list['Versions'], function ($v) use ($urn, $versionId) {
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
	}

	/**
	 * Get the content of a given version of a given file as stream resource
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return resource
	 * @since 10.0.5
	 */
	public function getContentOfVersion($urn, $versionId) {
		$this->init();
		return \fopen($this->getUrl($urn, $versionId), 'r');
	}

	/**
	 * Restore the given version of a given file
	 *
	 * @param string $urn the unified resource name used to identify the object
	 * @param string $versionId
	 * @return boolean
	 * @since 10.0.5
	 */
	public function restoreVersion($urn, $versionId) {
		$this->init();
		$this->connection->copyObject([
			'Bucket' => $this->getBucket(),
			'Key' => $urn,
			'CopySource' => "/{$this->getBucket()}/".\rawurlencode($urn)."?versionId=$versionId"
		]);

		return true;
	}

	/**
	 * Tells the storage to explicitly create a version of a given file
	 *
	 * @return boolean
	 * @since 10.0.5
	 */
	public function saveVersion($internalPath) {
		// There is no need in any explicit operations.
		// In a versioned bucket the versions are created automatically
		return true;
	}
}
