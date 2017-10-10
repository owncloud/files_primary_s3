<?php
/**
 * ownCloud
 *
 * @author Jörn Friedrich Dreyer <jfd@owncloud.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @copyright (C) 2014-2017 ownCloud, GmbH.
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 */

namespace OCA\Files_Primary_S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use OC\ServiceUnavailableException;
use OCP\Files\ObjectStore\IObjectStore;

require_once __DIR__ . '/../vendor/autoload.php';

class S3Storage implements IObjectStore {

	/**
	 * @var S3Client
	 */
	private $connection;

	/**
	 * @var array
	 */
	private $params;

	public function __construct($params) {

		if (!isset($params['options']) || !isset($params['bucket']) ) {
			throw new \Exception('Connection options and bucket must be configured.');
		}

		$this->params = $params;
	}

	protected function init() {
		if ($this->connection) {
			return;
		}
		$this->connection = S3Client::factory($this->params['options']);
		try {
			$this->connection->listBuckets();
		} catch(S3Exception $exception) {
			\OC::$server->getLogger()->logException($exception);
			throw new ServiceUnavailableException("No S3 ObjectStore available");
		}

		$this->connection->registerStreamWrapper();

		if ($this->params['autocreate'] && !$this->connection->doesBucketExist($this->params['bucket'])) {
			try {
				$this->connection->createBucket([
					'Bucket' => $this->params['bucket']
				]);
				// scality does not support waitUntilBucketExists()
				if ($this->connection->getApi()->hasOperation('waitUntilBucketExists')) {
					$this->connection->waitUntilBucketExists([
						'Bucket' => $this->params['bucket'],
						'waiter.interval' => 1,
						'waiter.max_attempts' => 15
					]);
				}
			} catch (S3Exception $e) {
				\OC::$server->getLogger()->logException($e, ['app' => 'objectstore']);
				throw new \Exception('Creation of bucket failed. '.$e->getMessage());
			}
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

		$uploader = new ObjectUploader($this->connection, $this->params['bucket'], $urn, $stream, 'private', $opt);
		$uploader->upload();
		if (is_resource($stream)) {
			fclose($stream);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteObject($urn) {
		$this->init();
		$this->connection->deleteObject([
			'Bucket' => $this->params['bucket'],
			'Key' => $urn,
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function readObject($urn) {
		$this->init();
		return fopen($this->getUrl($urn), 'r');
	}

	public function getUrl($urn) {
		return 's3://'.$this->params['bucket'].'/'.$urn;
	}
}
