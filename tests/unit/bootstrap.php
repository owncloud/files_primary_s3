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
	\fwrite(
		\STDERR,
		"Cannot find the server's composer autoloader (guzzle/psr7 live there).\n"
		. "Looked at: $coreAutoload\n"
		. "Set OC_CORE_AUTOLOAD to <core>/lib/composer/autoload.php.\n"
	);
	exit(1);
}
require_once $coreAutoload;

// The app has no composer autoload section; the server maps its classes at
// runtime, so map them here too.
\spl_autoload_register(static function (string $class): void {
	$prefix = 'OCA\\Files_Primary_S3\\';
	if (\strpos($class, $prefix) !== 0) {
		return;
	}
	$relative = \substr($class, \strlen($prefix));
	$file = __DIR__ . '/../../lib/' . \str_replace('\\', '/', $relative) . '.php';
	if (\is_file($file)) {
		require_once $file;
	}
});
