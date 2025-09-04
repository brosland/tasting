<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Ics\StandardIcsRepository;
use App\Standards\Domain\Standard\Replacement\StandardReplacementRepository;

final readonly class StandardDtoFactory
{
	public function __construct(
		private StandardIcsRepository $standardIcsRepository,
		private StandardRefDtoFactory $standardRefDtoFactory,
		private StandardReplacementRepository $standardReplacementRepository,
		private StandardRepository $standardRepository
	) {
	}

	public function createDto(Standard $standard): StandardDto
	{
		return $this->createDtoList([$standard])[0];
	}

	/**
	 * @param array<Standard> $standards
	 * @return array<StandardDto>
	 */
	public function createDtoList(array $standards): array
	{
		$icsCodes = $this->createIcsCodeListByStandards($standards);
		$parentStandards = $this->createParentStandardDtoListByStandards($standards);
		$replacedStandards = $this->createReplacedStandardListByStandards($standards);
		$replacementStandards = $this->createReplacementStandardListByStandards($standards);
		$result = [];

		foreach ($standards as $key => $standard) {
			$parentPk = $standard->getParent()?->getPk();

			$result[$key] = new StandardDto(
				id: $standard->getId(),
				type: $standard->getType(),
				catalogueNumber: $standard->getCatalogueNumber(),
				code: $standard->getCode(),
				title: $standard->getTitle(),
				description: $standard->getDescription(),
				language: $standard->getLanguage(),
				publicationDate: $standard->getPublicationDate(),
				approvalDate: $standard->getApprovalDate(),
				effectiveDate: $standard->getEffectiveDate(),
				withdrawalDate: $standard->getWithdrawalDate(),
				announcementDate: $standard->getAnnouncementDate(),
				isValid: $standard->isValid(),
				parent: $parentStandards[$parentPk] ?? null,
				replacedStandards: $replacedStandards[$standard->getPk()] ?? [],
				replacementStandards: $replacementStandards[$standard->getPk()] ?? [],
				icsCodes: $icsCodes[$standard->getPk()] ?? [],
			);
		}

		return $result;
	}

	/**
	 * @param array<int> $primaryKeys
	 * @return array<StandardDto>
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

	/**
	 * @param array<Standard> $standards
	 * @return array<int,array<IcsCode>> Indexed by primary keys of Standards.
	 */
	private function createIcsCodeListByStandards(array $standards): array
	{
		$standardIds = [];

		foreach ($standards as $standard) {
			$standardIds[] = $standard->getId();
		}

		$standardIds = array_unique($standardIds);

		if (count($standardIds) === 0) {
			return [];
		}

		$standardIcsList = $this->standardIcsRepository->findByStandards($standardIds);
		$result = [];

		foreach ($standards as $standard) {
			$result[$standard->getPk()] = [];
		}

		foreach ($standardIcsList as $standardIcs) {
			$result[$standardIcs->getStandard()->getPk()][] = $standardIcs->getIcsCode();
		}

		return $result;
	}

	/**
	 * @param array<Standard> $standards
	 * @return array<int,StandardRefDto> Indexed by primary keys.
	 */
	private function createParentStandardDtoListByStandards(array $standards): array
	{
		$primaryKeys = [];

		foreach ($standards as $standard) {
			$primaryKeys[] = $standard->getParent()?->getPk();
		}

		$primaryKeys = array_unique(array_filter($primaryKeys, fn($pk) => $pk !== null));

		if (count($primaryKeys) === 0) {
			return [];
		}

		return $this->standardRefDtoFactory->createDtoListByPrimaryKeys($primaryKeys);
	}

	/**
	 * @param array<Standard> $standards
	 * @return array<int,array<StandardRefDto>> Indexed by primary keys.
	 */
	private function createReplacedStandardListByStandards(array $standards): array
	{
		$standardIds = [];
		$result = [];

		foreach ($standards as $standard) {
			$standardIds[] = $standard->getId();
			$result[$standard->getPk()] = [];
		}

		// find replaced standards by replacement standard IDs
		$replacedStandardQuery = $this->standardReplacementRepository->createStandardReplacementQuery();
		$replacedStandardQuery->byReplacement($standardIds);
		$replacedStandardQuery->withStandard();

		foreach ($replacedStandardQuery->getResult() as $standardReplacement) {
			$replacementPk = $standardReplacement->getReplacement()->getPk();
			$standardRefDto = $this->standardRefDtoFactory->createDto($standardReplacement->getStandard());

			$result[$replacementPk][] = $standardRefDto;
		}

		return $result;
	}

	/**
	 * @param array<Standard> $standards
	 * @return array<int,array<StandardRefDto>> Indexed by primary keys.
	 */
	private function createReplacementStandardListByStandards(array $standards): array
	{
		$standardIds = [];
		$result = [];

		foreach ($standards as $standard) {
			$standardIds[] = $standard->getId();
			$result[$standard->getPk()] = [];
		}

		// find replacement standards by replaced standard IDs
		$replacedStandardQuery = $this->standardReplacementRepository->createStandardReplacementQuery();
		$replacedStandardQuery->byStandard($standardIds);
		$replacedStandardQuery->withReplacement();

		foreach ($replacedStandardQuery->getResult() as $standardReplacement) {
			$standardPk = $standardReplacement->getStandard()->getPk();
			$replacementRefDto = $this->standardRefDtoFactory->createDto($standardReplacement->getReplacement());

			$result[$standardPk][] = $replacementRefDto;
		}

		return $result;
	}
}
