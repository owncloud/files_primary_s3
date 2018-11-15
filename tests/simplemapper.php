<?php
namespace OCA\Files_Primary_S3\Tests;

use OCP\IUser;

/**
 * Class Mapper
 *
 * @package OC\Files\ObjectStore
 *
 * Map a user to a bucket.
 */
class SimpleMapper {
	/** @var IUser */
	private $user;

	/**
	 * Mapper constructor.
	 *
	 * @param IUser $user
	 */
	public function __construct(IUser $user) {
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getBucket() {
		$hash = \md5($this->user->getUID());
		return (string)\floor(\ord(\substr($hash, 0, 1))/26);
	}
}
