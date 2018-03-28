<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2017, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Files_Primary_S3\Command;

use Aws\S3\S3Client;
use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ls extends Command {

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		parent::__construct();
		$this->config = $config;
	}

	protected function configure() {
		$this
			->setName('s3:ls')
			->setDescription('List objects, buckets or versions of an object')
			->addArgument('bucket', InputArgument::OPTIONAL, 'Name of the bucket; it`s objects will be listed')
			->addArgument('object', InputArgument::OPTIONAL, 'Key of the object; it`s versions will be listed');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$client = $this->getClient();

		$bucketName = $input->getArgument('bucket');
		if ($bucketName === null) {
			$result = $client->listBuckets();
			$buckets = array_map(function($bucket) use ($client) {
				$versionStatus = $client->getBucketVersioning([
					'Bucket' => $bucket['Name'],
				]);
				$bucket['Versioning'] = $versionStatus['Status'];
				$corsConfig = $client->getBucketCors([
					'Bucket' => $bucket['Name'],
				]);
				$bucket['CORS'] = $corsConfig['CORSRules'];
				return $bucket;
			}, $result['Buckets']);
			$this->printValue($output, $buckets, ['Name', 'Versioning', 'CORS']);
		} else {
			$object = $input->getArgument('object');
			if ($object === null) {
				$result = $client->listObjects([
					'Bucket' => $bucketName,
				]);
				$this->printValue($output, $result['Contents'], ['Key', 'LastModified', 'ETag', 'Size']);
			} else {
				$result = $client->listObjectVersions([
					'Bucket' => $bucketName,
					'Prefix' => $object
				]);
				$versions = array_filter($result['Versions'], function ($version) use ($object) {
					return $version['Key'] === $object;
				});
				$this->printValue($output, $versions, ['Key', 'LastModified', 'ETag', 'Size', 'VersionId', 'IsLatest']);

				$output->writeln('Delete Markers:');
				$output->writeln('----------------------------------------');
				$markers = array_filter(isset($result['DeleteMarkers']) ? $result['DeleteMarkers'] :  [], function ($marker) use ($object) {
					return $marker['Key'] === $object;
				});
				$this->printValue($output, $markers, ['Key', 'LastModified', 'VersionId', 'IsLatest']);
			}
		}

	}

	private function getClient() {
		$cfg = $this->config->getSystemValue('objectstore');
		return S3Client::factory($cfg['arguments']['options']);
	}

	/**
	 * @param OutputInterface $output
	 * @param array $results
	 * @param array $keys
	 * @internal param $bucket
	 */
	protected function printValue(OutputInterface $output, array $results, array $keys) {
		foreach ($results as $result) {
			foreach ($keys as $key) {
				$value = isset($result[$key]) ? json_encode($result[$key]) : '---';
				$output->writeln("$key: $value");
			}
			$output->writeln('----------------------------------------');
		}
	}
}
