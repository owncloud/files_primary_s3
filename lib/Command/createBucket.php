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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class createBucket extends Command {

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		parent::__construct();
		$this->config = $config;
	}

	protected function configure() {
		$this
			->setName('s3:create-bucket')
			->setDescription('Create a bucket as necessary to be used')
			->addArgument('bucket', InputArgument::REQUIRED, 'Name of the bucket; it`s objects will be listed')
			->addOption('update-configuration', null, InputOption::VALUE_NONE, 'If the bucket exists the configuration will be updated');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$client = $this->getClient();

		$bucketName = $input->getArgument('bucket');
		$result = $client->doesBucketExist($bucketName);
		if ($result) {
			$output->writeln("Bucket does already exist: $bucketName");
			if (!$input->getOption('update-configuration')) {
				return;
			}
		} else {
			$output->writeln("Creating bucket <$bucketName> ...");
			$client->createBucket([
				'Bucket' => $bucketName
			]);
			// scality does not support waitUntilBucketExists()
			if ($client->getApi()->hasOperation('waitUntilBucketExists')) {
				$client->waitUntil('BucketExists', [
					'Bucket' => $bucketName,
					'waiter.interval' => 1,
					'waiter.max_attempts' => 15
				]);
			}
		}

		// enabled versioning on the bucket
		$output->writeln("Enabling versioning on bucket <$bucketName> ...");
		$client->putBucketVersioning([
			'Bucket' => $bucketName,
			'VersioningConfiguration' => [
				'Status' => 'Enabled',
				'MFADelete' => 'Disabled']
		]);

		// set cors
		$output->writeln("Setting up CORS rules on bucket <$bucketName> ...");
		$client->putBucketCors([
			'Bucket' => $bucketName,
			'CORSConfiguration' => [
				'CORSRules' => [
					[
						'AllowedMethods' => ['GET'],
						'AllowedOrigins' => ['*'],
						'AllowedHeaders' => ['*'],
						'MaxAgeSeconds' => 60
					]]]]);
	}

	private function getClient() {
		$cfg = $this->config->getSystemValue('objectstore');
		return S3Client::factory($cfg['arguments']['options']);
	}
}
