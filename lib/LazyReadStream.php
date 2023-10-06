<?php

namespace OCA\Files_Primary_S3;

use Aws\S3\S3Client;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class LazyReadStream implements StreamInterface {
	use StreamDecoratorTrait;

	private S3Client $client;
	private string $bucket;
	private string $key;
	private ?string $versionId;

	public function __construct(S3Client $client, string $bucket, string $key, ?string $versionId = null) {
		$this->client = $client;
		$this->bucket = $bucket;
		$this->key = $key;
		$this->versionId = $versionId;

		// unsetting the property forces the first access to go through
		// __get().
		unset($this->stream);
	}

	protected function createStream(): StreamInterface {
		$options = [
			'Bucket'    => $this->bucket,
			'Key'       => $this->key,
			'VersionId' => $this->versionId,
			'seekable'  => true
		];
		$command = $this->client->getCommand('GetObject', $options);
		$command['@http']['stream'] = true;
		$result = $this->client->execute($command);

		// Wrap the body in a caching entity body if seeking is allowed
		// Phan does not understand that Body can be a StreamInterface
		// It thinks that body is just a string. Suppress the message.
		/* @phan-suppress-next-line PhanNonClassMethodCall */
		/* @phan-suppress-next-line PhanTypeMismatchArgument */
		return new CachingStream($result['Body']);
	}
}
