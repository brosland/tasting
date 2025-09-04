<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

final readonly class StandardRefDtoFactory
{
	public function __construct(
		private StandardRepository $standardRepository
	) {
	}

	public function createDto(Standard $standard): StandardRefDto
	{
		return $this->createDtoList([$standard])[0];
	}

	/**
	 * @param array<Standard> $standards
	 * @return array<StandardRefDto>
	 */
	public function createDtoList(array $standards): array
	{
		$result = [];

		foreach ($standards as $key => $standard) {
			$result[$key] = new StandardRefDto(
				id: $standard->getId(),
				type: $standard->getType(),
				catalogueNumber: $standard->getCatalogueNumber(),
				code: $standard->getCode(),
				title: $standard->getTitle(),
				language: $standard->getLanguage()
			);
		}

		return $result;
	}

	/**
	 * @param array<int> $primaryKeys
	 * @return array<StandardRefDto>
	 */
	public function createDtoListByPrimaryKeys(array $primaryKeys): array
	{
		if (count($primaryKeys) === 0) {
			return [];
		}

		$query = $this->standardRepository->createStandardQuery();
		$query->byPrimaryKeys($primaryKeys);

		return $this->createDtoList($query->getResult());
	}
}
