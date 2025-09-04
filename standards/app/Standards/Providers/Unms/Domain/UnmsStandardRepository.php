<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Standards\Domain\Standard\StandardNotFoundException;

final readonly class UnmsStandardRepository
{
	public function __construct(
		private UnmsConnection $connection
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function getByCatalogueNumber(int $catalogueNumber): UnmsStandardDto
	{
		$stream = $this->connection->get('/api/standard/', ['catalogueNo' => $catalogueNumber]);
		/** @var UnmsResponseDto<UnmsStandardDto> $response */
		$response = UnmsResponseDto::from(
			data: json_decode($stream->getContents(), true),
			dataConverter: fn(array $data) => UnmsStandardDto::from($data)
		);

		foreach ($response->data as $standard) {
			if ($standard->catalogueNumber === $catalogueNumber) {
				return $standard;
			}
		}

		throw StandardNotFoundException::notFoundByCatalogueNumber($catalogueNumber);
	}

	public function createUnmsStandardQuery(): UnmsStandardQuery
	{
		return new UnmsStandardQuery($this->connection);
	}
}
