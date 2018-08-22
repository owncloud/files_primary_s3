<?php
$CONFIG = [
	'objectstore' => [
		'class' => 'OCA\Files_Primary_S3\S3Storage',
		'arguments' => [
			// replace with your bucket
			'bucket' => 'owncloud',
			// uncomment to enable server side encryption
			//'serversideencryption' => 'AES256',
			'options' => [
				// version and region are required
				'version' => '2006-03-01',
				'region'  => 'us-east-1',
				'credentials' => [
					// replace key and secret with your credentials
					'key' => 'owncloud123456',
					'secret' => 'secret123456',
				],
				'use_path_style_endpoint' => false,
				'endpoint' => 'http://ceph:8080/',
			],
		],
	],
];
