<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
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

namespace OCA\Files_Primary_S3\Command;

use Aws\S3\S3Client;
use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

require_once __DIR__ . '/../../vendor/autoload.php';

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
			->addArgument('bucket', InputArgument::REQUIRED, 'Name of the bucket to be created')
			->addOption('update-configuration', null, InputOption::VALUE_NONE, 'If the bucket exists the configuration will be updated')
			->addOption('accept-warning', null, InputOption::VALUE_NONE, 'No warning about the usage of this command will be displayed');
	}

	/**
	 * Executes the current command.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		if (!$input->getOption('accept-warning')) {
			$helper = new QuestionHelper();
			$q = <<<EOS
<question>This command is mainly for development purposes. 
Please consult the documentation of your S3 system to learn how to properly create a new bucket.
For required settings from the ownCloud perspective please consult the ownCloud documentation.
If you still want to use this command please confirm the usage by entering: yes
</question>
EOS;
			if (!$helper->ask($input, $output, new ConfirmationQuestion($q, false))) {
				return 1;
			}
		}

		/** @var string $bucketName */
		$bucketName = $input->getArgument('bucket');
		$client = $this->getClient();
		$result = $client->doesBucketExist($bucketName);
		if ($result) {
			$output->writeln("Bucket already exists: $bucketName");
			if (!$input->getOption('update-configuration')) {
				return 1;
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
		return 0;
	}

	private function getClient() {
		$cfg = $this->config->getSystemValue('objectstore_multibucket', null);
		$cfg = $this->config->getSystemValue('objectstore', $cfg);
		if ($cfg === null) {
			throw new \InvalidArgumentException('No object store is configured.');
		}
		/* @phan-suppress-next-line PhanDeprecatedFunction */
		return S3Client::factory($cfg['arguments']['options']);
	}
}
