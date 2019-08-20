<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    // The prefix configuration. If a non null value will be used, a random prefix will be generated.
    'prefix' => null,

    // By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
    // directory. You can however define which files should be scoped by defining a collection of Finders in the
    // following configuration key.
    //
    // For more see: https://github.com/humbug/php-scoper#finders-and-paths
    'finders' => [
        Finder::create()->files()->in('lib'),
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),
        Finder::create()->append([
            'composer.json',
        ]),
    ],

    // Whitelists a list of files. Unlike the other whitelist related features, this one is about completely leaving
    // a file untouched.
    // Paths are relative to the configuration file unless if they are already absolute
    'files-whitelist' => [],

    // When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
    // original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
    // support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
    // heart contents.
    //
    // For more see: https://github.com/humbug/php-scoper#patchers
    'patchers' => [
        // The patcher ensures that files belonging to the app will not be prefixed
        function (string $filePath, string $prefix, string $content): string {
            if (false === (\strpos($filePath, 'files_primary_s3/lib'))) {
                return $content;
            }
            return \preg_replace('/namespace '.$prefix.'\\\\(.*)/', 'namespace $1', $content);
        },
        // This should address gmdate constant being scoped in aws signature
        // also see https://github.com/humbug/php-scoper/issues/301
        function (string $filePath, string $prefix, string $content): string {
            if (false === (\strpos($filePath, 'vendor/aws/aws-sdk-php/src/Signature/SignatureV4.php'))) {
                return $content;
            }
            return \preg_replace('/const ISO8601_BASIC = \'(.*)\';/', '    const ISO8601_BASIC = \'Ymd\THis\Z\';', $content);
        },
        // Needed to address https://github.com/humbug/php-scoper/issues/298
        function (string $filePath, string $prefix, string $content): string {
            if (false === (\strpos($filePath, 'autoload_files.php'))) {
                return $content;
            }
            return \preg_replace('/\'(.*?)\' => (.*?),', '\'a$1\' => $2,', $content);
        },
        // Needed to address https://github.com/humbug/php-scoper/issues/298
        function (string $filePath, string $prefix, string $content): string {
            if (false === (\strpos($filePath, 'autoload_static.php'))) {
                return $content;
            }
            return \preg_replace('/\'(.*?)\' => __DIR__ \. (.*?),/', '\'a$1\' => __DIR__ . $2,', $content);
        },
        // Fix AWS Exception magic
        function (string $filePath, string $prefix, string $content): string {
            if (false === (\strpos($filePath, 'vendor/aws/aws-sdk-php/src/AwsClient.php'))) {
                return $content;
            }
            $content = \preg_replace('/Aws\\\\\\\\\{\$service\}\\\\\\\\Exception\\\\\\\\\{\$service\}Exception/', $prefix.'\\\\\\\\Aws\\\\\\\\{$service}\\\\\\\\Exception\\\\\\\\{$service}Exception', $content);
            return $content;
        }
    ],

    // PHP-Scoper's goal is to make sure that all code for a project lies in a distinct PHP namespace. However, you
    // may want to share a common API between the bundled code of your PHAR and the consumer code. For example if
    // you have a PHPUnit PHAR with isolated code, you still want the PHAR to be able to understand the
    // PHPUnit\Framework\TestCase class.
    //
    // A way to achieve this is by specifying a list of classes to not prefix with the following configuration key. Note
    // that this does not work with functions or constants neither with classes belonging to the global namespace.
    //
    // Fore more see https://github.com/humbug/php-scoper#whitelist
    'whitelist' => [
        'OCP\*',
        'OC\*',
        'OCA\*',
        'Symfony\Component\Console\*',
        'OCA\Files_Primary_S3'
    ],

    // If `true` then the user defined constants belonging to the global namespace will not be prefixed.
    //
    // For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace
    'whitelist-global-constants' => true,

    // If `true` then the user defined classes belonging to the global namespace will not be prefixed.
    //
    // For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace
    'whitelist-global-classes' => true,

    // If `true` then the user defined functions belonging to the global namespace will not be prefixed.
    //
    // For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace
    'whitelist-global-functions' => true,
];
