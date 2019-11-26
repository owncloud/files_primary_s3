<?php
namespace OCA\Files_Primary_S3\Tests;

use OCP\IUser;

/**
 * Class Mapper
 * NOTE: This class is only intended for testing. It doesn't have any persistence,
 * so the same user might end up in different buckets if you can't ensure the same
 * order in all the requests
 *
 * @package OC\Files\ObjectStore
 *
 * Map a user to a bucket.
 */
class SimpleMapperEnsuredMultibucket {
	private static $knownUserList = [];
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
		$uid = $this->user->getUID();
		$searchIndex = \array_search($uid, self::$knownUserList, true);
		if ($searchIndex === false) {
			// missing uid -> add it to the list
			self::$knownUserList[] = $uid;
			$searchIndex = \count(self::$knownUserList) - 1;
		}
		return (string)(($searchIndex % 10) + 1);
	}
}
