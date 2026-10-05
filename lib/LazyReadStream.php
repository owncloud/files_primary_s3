<?php

namespace OCA\Files_Primary_S3;

use Aws\S3\S3Client;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class LazyReadStream implements StreamInterface {
	use StreamDecoratorTrait;

	private S3Client $client;
	/**
	 * Used for every operation that is not a streamed download - see the note on
	 * the constructor.
	 */
	private S3Client $metaClient;
	private string $bucket;
	private string $key;
	private ?string $versionId;
	private int $size;
	private int $offset = 0;

	/**
	 * @param S3Client $client streams the object body; built on guzzle's
	 *        StreamHandler, so its response bodies are not seekable
	 * @param S3Client|null $metaClient issues the non-streaming operations. Pass a
	 *        client whose bodies ARE seekable - aws-sdk-php inspects every
	 *        non-streaming S3 response for S3's "HTTP 200 carrying an error
	 *        document" case, which reads the first bytes of the body and rewinds
	 *        it. GetObject is exempt because its output shape has a streaming
	 *        member; HeadObject is not, so issuing it on $client throws
	 *        "Stream is not seekable". Defaults to $client to stay compatible
	 *        with callers that pass only one.
	 */
	public function __construct(
		S3Client $client,
		string $bucket,
		string $key,
		?string $versionId = null,
		?S3Client $metaClient = null
	) {
		$this->client = $client;
		$this->metaClient = $metaClient ?? $client;
		$this->bucket = $bucket;
		$this->key = $key;
		$this->versionId = $versionId;
		$this->resetStream();

		// get size
		$result = $this->metaClient->headObject([
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
