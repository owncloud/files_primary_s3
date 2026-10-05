<?php
/**
 * Bootstrap for the app's unit tests.
 *
 * The app declares `replace` for guzzlehttp/guzzle and guzzlehttp/psr7, so those
 * are NOT in its own vendor tree — at runtime they come from the ownCloud server
 * that hosts the app. Tests therefore need a core checkout for them. When the app
 * sits in `<core>/apps/files_primary_s3` (the layout the Makefile already assumes)
 * it is found automatically; otherwise point OC_CORE_AUTOLOAD at
 * `<core>/lib/composer/autoload.php`.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$coreAutoload = \getenv('OC_CORE_AUTOLOAD')
	?: __DIR__ . '/../../../../lib/composer/autoload.php';
if (!\is_file($coreAutoload)) {
	// Must not exit(): core's tests/apps.php require_once's this file while
	// collecting tests, so exiting here would abort the server's whole unit-test
	// run with no output instead of failing this app's tests.
	throw new \RuntimeException(
		"Cannot find the server's composer autoloader (guzzle/psr7 live there). "
		. "Looked at: $coreAutoload - set OC_CORE_AUTOLOAD to "
		. '<core>/lib/composer/autoload.php.'
	);
}
require_once $coreAutoload;

// The app has no composer autoload section; the server maps its classes at
// runtime, so map them here too.
\spl_autoload_register(static function (string $class): void {
	$prefix = 'OCA\\Files_Primary_S3\\';
	if (\strpos($class, $prefix) !== 0) {
		return;
	}
	$relative = \str_replace('\\', '/', \substr($class, \strlen($prefix)));
	// Class and file names do not match case here - S3Storage lives in
	// lib/s3storage.php, Command\s3List in lib/command/s3list.php - and the
	// server's own autoloader tries the lower-cased path too.
	foreach ([$relative, \strtolower($relative)] as $candidate) {
		$file = __DIR__ . '/../../lib/' . $candidate . '.php';
		if (\is_file($file)) {
			require_once $file;
			return;
		}
	}
});
