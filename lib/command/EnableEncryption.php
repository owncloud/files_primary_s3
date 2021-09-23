<?php
/**
 * @author Jannik Stehle <jstehle@owncloud.com>
 * @author Jan Ackermann <jackermann@owncloud.com>
 *
 * @copyright Copyright (c) 2021, ownCloud GmbH
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

use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Overwrite the encryption-enable-command if an object storage
 * is configured. Is this the case, enabling encryption is not
 * possible.
 */
class EnableEncryption extends Command {

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		parent::__construct();
		$this->config = $config;
	}

	protected function configure() {
		$this
			->setName('encryption:enable')
			->setDescription('Enable encryption.');
	}

	/**
	 * Check if an object storage is configured.
	 * @return bool
	 */
	public function isEnabled() {
		$objectstore = $this->config->getSystemValue('objectstore', null);
		return isset($objectstore);
	}

	/**
	 * Executes the current command.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('<error>Storage encryption is not compatible with S3 Object Storage.</error>');
	}
}
