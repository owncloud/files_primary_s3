<?php

$config = new OC\CodingStandard\Config();

$config
    ->setUsingCache(true)
    ->getFinder()
	->exclude('vendor')
	->exclude('.composer')
	->exclude('vendor-bin')
	->notPath('/^c3.php/')
    ->in(__DIR__);

return $config;