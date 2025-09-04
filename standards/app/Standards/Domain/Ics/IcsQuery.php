<?php
declare(strict_types=1);

namespace App\Standards\Domain\Ics;

use App\Common\Domain\Sorting;

interface IcsQuery
{
	function byMaxLevel(int $maxLevel): void;

	function byMinLevel(int $minLevel): void;

	function byParent(IcsCode $parentCode): void;

	/**
	 * @param array<int> $primaryKeys
	 */
	function byPrimaryKeys(array $primaryKeys): void;

	function bySearchQuery(string $searchQuery): void;

	function paginate(int $limit, int $offset = 0): void;

	/**
	 * @param Sorting<IcsSortField> $sorting
	 */
	function sortBy(Sorting $sorting): void;

	/**
	 * @return array<Ics>
	 */
	function getResult(): array;

	function getTotalCount(): int;

}
