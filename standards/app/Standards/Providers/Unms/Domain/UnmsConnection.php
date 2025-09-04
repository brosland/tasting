<?php
declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Common\Domain\DateTime\DateTimeProvider;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;
use Tracy\Debugger;

final class UnmsConnection
{
	private const int MIN_REQUEST_DELAY = 0;
	private const int MAX_TRY = 2;

	private Client $client;
	private ?DateTimeImmutable $lastRequestAt = null;

	public function __construct(
		private readonly DateTimeProvider $dateTimeProvider,
		private readonly string $baseUri
	) {
		$config = ['base_uri' => $baseUri];

		$this->client = new Client($config);
	}

	public function getBaseUri(): string
	{
		return $this->baseUri;
	}

	/**
	 * @param array<string,mixed> $query
	 */
	public function get(string $endPoint, array $query = []): StreamInterface
	{
		$try = 0;

		while (true) {
			$now = $this->dateTimeProvider->getNow();
			$delay = $now->getTimestamp() - ($this->lastRequestAt?->getTimestamp() ?? 0);

			if ($delay < self::MIN_REQUEST_DELAY) {
				sleep($delay);
			}

			$this->lastRequestAt = $this->dateTimeProvider->getNow();

			try {
				return $this->client->get($endPoint, ['query' => $query])->getBody();
			} catch (GuzzleException $e) {
				if ($try >= self::MAX_TRY - 1) {
					Debugger::log($e);

					throw UnmsApiException::create($e);
				}

				$try++;
			}
		}
	}
}
