<?php

namespace OCA\Files_Primary_S3\Tests\Unit;

use Aws\S3\S3Client;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use OCA\Files_Primary_S3\LazyReadStream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * S3Storage gives LazyReadStream a client whose HTTP handler is guzzle's
 * StreamHandler, so that a download is streamed instead of buffered. Response
 * bodies produced that way are NOT seekable.
 *
 * aws-sdk-php >= 3.3xx inspects every non-streaming S3 response for S3's
 * "HTTP 200 carrying an error document" case: Aws\S3\Parser\S3Parser reads the
 * first 64 bytes of the body and then rewinds it. On a non-seekable body that
 * rewind throws, so any non-streaming operation issued on the streaming client
 * fails. GetObject is exempt (its output shape has a streaming member);
 * HeadObject is not.
 */
class LazyReadStreamTest extends TestCase {
	/** @var list<string> */
	private array $streamingClientCalls = [];

	/** @var list<string> */
	private array $metadataClientCalls = [];

	/**
	 * A client that behaves like one built on guzzle's StreamHandler: response
	 * bodies cannot be rewound.
	 */
	private function streamingClient(int $size): S3Client {
		return $this->client(
			$this->streamingClientCalls,
			static function (string $body, int $size): Response {
				return new Response(
					200,
					['Content-Length' => (string)$size],
					new NoSeekStream(Utils::streamFor($body))
				);
			},
			$size
		);
	}

	/** A client that behaves like one built on guzzle's CurlMultiHandler. */
	private function metadataClient(int $size): S3Client {
		return $this->client(
			$this->metadataClientCalls,
			static function (string $body, int $size): Response {
				return new Response(
					200,
					['Content-Length' => (string)$size],
					Utils::streamFor($body)
				);
			},
			$size
		);
	}

	private function client(array &$calls, callable $responseFactory, int $size): S3Client {
		return new S3Client([
			'region' => 'us-east-1',
			'version' => '2006-03-01',
			'credentials' => ['key' => 'k', 'secret' => 's'],
			'http_handler' => static function (RequestInterface $request) use (&$calls, $responseFactory, $size) {
				$calls[] = $request->getMethod();
				$body = $request->getMethod() === 'GET' ? \str_repeat('x', $size) : '';
				return Create::promiseFor($responseFactory($body, $size));
			},
		]);
	}

	public function testReportsTheObjectSizeWhenTheStreamingClientCannotRewind(): void {
		$size = 65536;

		$stream = new LazyReadStream(
			$this->streamingClient($size),
			'a-bucket',
			'urn:oid:42',
			null,
			$this->metadataClient($size)
		);

		$this->assertSame($size, $stream->getSize());
		$this->assertSame(
			['HEAD'],
			$this->metadataClientCalls,
			'the size lookup has to be issued on the metadata client'
		);
		$this->assertSame(
			[],
			$this->streamingClientCalls,
			'constructing the stream must not touch the streaming client'
		);
	}

	public function testReadsTheObjectBodyFromTheStreamingClient(): void {
		$size = 1024;

		$stream = new LazyReadStream(
			$this->streamingClient($size),
			'a-bucket',
			'urn:oid:42',
			null,
			$this->metadataClient($size)
		);
		$content = $stream->read($size);

		$this->assertSame(\str_repeat('x', $size), $content);
		$this->assertSame(
			['GET'],
			$this->streamingClientCalls,
			'the body has to be streamed from the streaming client, not buffered by the metadata client'
		);
		$this->assertSame(['HEAD'], $this->metadataClientCalls);
	}
}
