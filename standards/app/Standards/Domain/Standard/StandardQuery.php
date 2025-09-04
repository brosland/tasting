<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Sorting;
use App\Standards\Domain\Ics\IcsCode;
use DateTimeImmutable;

interface StandardQuery
{
	/**
	 * @param array<int> $primaryKeys
	 */
	function byPrimaryKeys(array $primaryKeys): void;

	/**
	 * @param array<StandardType> $types
	 */
	function byType(array $types): void;

	function byPublicationDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void;

	function byApprovalDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void;

	function byEffectiveDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void;

	function byWithdrawalDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void;

	function byAnnouncementDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void;

	function byIsValid(bool $isValid): void;

	function byPostProcessRequired(bool $postProcessRequired): void;

	function byParent(StandardId $parentId): void;

	function byIcs(IcsCode $icsCode): void;

	function bySearchQuery(string $searchQuery): void;

	/**
	 * @param Sorting<StandardSortField> $sorting
	 */
	function sortBy(Sorting $sorting): void;

	function paginate(int $limit, int $offset = 0): void;

	/**
	 * @return array<Standard>
	 */
	function getResult(): array;

	function getTotalCount(): int;
}
