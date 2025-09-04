<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

final readonly class IcsDtoFactory
{
	public function __construct(
		private IcsRepository $icsRepository
	) {
	}

	public function createDto(Ics $ics): IcsDto
	{
		return $this->createDtoList([$ics])[0];
	}

	/**
	 * @param array<Ics> $icsList
	 * @return array<IcsDto>
	 */
	public function createDtoList(array $icsList): array
	{
		$result = [];

		foreach ($icsList as $key => $ics) {
			$result[$key] = new IcsDto(
				code: $ics->getCode(),
				title: $ics->getTitle(),
				description: $ics->getDescription(),
				parentCode: $ics->getCode()->getParentCode(),
				treeLevel: $ics->getTreeLevel(),
				treeLeft: $ics->getTreeLeft(),
				treeRight: $ics->getTreeRight(),
				subItemCount: $ics->getSubBlockCount()
			);
		}

		return $result;
	}

	/**
	 * @param array<int> $primaryKeys
	 * @return array<IcsDto>
	 */
	public function createDtoListByPrimaryKeys(array $primaryKeys): array
	{
		if (count($primaryKeys) === 0) {
			return [];
		}

		$query = $this->icsRepository->createIcsQuery();
		$query->byPrimaryKeys($primaryKeys);

		return $this->createDtoList($query->getResult());
	}
}
