<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Standards\Domain\Ics\IcsCode;

final class UnmsStandardQuery
{
	/** @var array<callable> */
	private array $filters = [];

	public function __construct(
		private readonly UnmsConnection $connection
	) {
	}

	public function byIcs(IcsCode $icsCode): void
	{
		/**
		 * @param array<string,mixed> $query
		 */
		$this->filters[] = function (array &$query) use ($icsCode): void {
			$icsParts = explode('.', $icsCode->toString());

			$query['ics1'] = $icsParts[0];

			if (isset($icsParts[1])) {
				$query['ics2'] = $icsParts[1];
			}

			if (isset($icsParts[2])) {
				$query['ics3'] = $icsParts[2];
			}
		};
	}

	public function paginate(int $page): void
	{
		/**
		 * @param array<string,mixed> $query
		 */
		$this->filters[] = function (array &$query) use ($page): void {
			$query['page'] = $page;
		};
	}

	/**
	 * @return UnmsResponseDto<UnmsStandardDto>
	 */
	public function getResponse(): UnmsResponseDto
	{
		$query = [];

		foreach ($this->filters as $filter) {
			$filter($query);
		}

		$response = $this->connection->get('api/standard', $query)->getContents();
		$json = json_decode($response, true);

		return UnmsResponseDto::from(
			data: $json,
			dataConverter: fn(array $data) => UnmsStandardDto::from($data)
		);
	}
}
