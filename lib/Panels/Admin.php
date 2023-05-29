<?php
/**
 * @author Jannik Stehle <jstehle@owncloud.com>
 * @author Jan Ackermann <jackermann@owncloud.com>
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
namespace OCA\Files_Primary_S3\Panels;

use OCP\Settings\ISettings;
use OCP\Template;
use OCP\IConfig;

class Admin implements ISettings {
	/** @var IConfig */
	protected $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	public function getPriority() {
		return 0;
	}

	public function getSectionID() {
		return 'encryption';
	}

	public function getPanel() {
		$objectstore = $this->config->getSystemValue('objectstore', null);
		if ($objectstore) {
			$tmpl = new Template('files_primary_s3', 'settings');
			return $tmpl;
		}

		return null;
	}
}
