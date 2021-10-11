<?php
/**
 *
 * @copyright Copyright (c) 2021, ownCloud GmbH
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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @var Symfony\Component\Console\Application $application */
$encryptionEnableCmd = $application->get('encryption:enable'); // @phan-suppress-current-line PhanUndeclaredGlobalVariable

$objectstore = \OC::$server->getConfig()->getSystemValue('objectstore', null);

if (isset($objectstore)) {
	$encryptionEnableCmd->setCode(function (InputInterface $input, OutputInterface $output) {
		$output->writeln('<error>Storage encryption is not compatible with S3 Object Storage.</error>');
		return 0;
	});
}
