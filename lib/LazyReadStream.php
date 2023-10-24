<?php

namespace OCA\Files_Primary_S3;

use Aws\S3\S3Client;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class LazyReadStream implements StreamInterface {
	use StreamDecoratorTrait;

	private S3Client $client;
	private string $bucket;
	private string $key;
	private ?string $versionId;
	private int $size;
	private int $offset = 0;

	public function __construct(S3Client $client, string $bucket, string $key, ?string $versionId = null) {
		$this->client = $client;
		$this->bucket = $bucket;
		$this->key = $key;
		$this->versionId = $versionId;
		$this->resetStream();

		// get size
		$result = $this->client->headObject([
			'Bucket'    => $this->bucket,
			'Key'       => $this->key,
			'VersionId' => $this->versionId,
		]);
		$this->size = (int) $result['ContentLength'];
	}

	protected function createStream(): StreamInterface {
		$options = [
			'Bucket'    => $this->bucket,
			'Key'       => $this->key,
			'VersionId' => $this->versionId,
			'seekable'  => true,
		];
		if ($this->offset > 0) {
			$options['Range'] = "bytes=$this->offset-";
		}
		$command = $this->client->getCommand('GetObject', $options);
		$command['@http']['stream'] = true;
		$result = $this->client->execute($command);

		/* @phan-suppress-next-line PhanTypeMismatchReturn */
		return $result['Body'];
	}

	public function getSize(): ?int {
		return $this->size;
	}

	public function seek($offset, $whence = SEEK_SET): void {
		if ($whence === SEEK_SET) {
			$this->offset = $offset;
		}
		if ($whence === SEEK_END) {
			$this->offset = $offset + $this->size;
		}
		if ($whence === SEEK_CUR) {
			$this->offset += $offset;
		}
		$this->resetStream();
	}

	public function isReadable(): bool {
		# due to successful HEAD in ctor we know that we have access and can read
		return true;
	}

	public function isWritable(): bool {
		return false;
	}

	public function tell(): int {
		return $this->offset;
	}

	public function eof(): bool {
		if (isset($this->stream)) {
			return $this->stream->eof();
		}
		return false;
	}

	private function resetStream(): void {
		// unsetting the property forces the first access to go through
		// __get().
		unset($this->stream);
	}
}
