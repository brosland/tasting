<?php
declare(strict_types=1);

namespace App\Standards\Domain\Category;

use App\Common\Domain\Sorting;

interface CategoryQuery
{
	function byMaxLevel(int $maxLevel): void;

	function byMinLevel(int $minLevel): void;

	function byParent(CategoryId $parentId): void;

	/**
	 * @param array<int> $primaryKeys
	 */
	function byPrimaryKeys(array $primaryKeys): void;

	function bySearchQuery(string $searchQuery): void;

	function paginate(int $limit, int $offset = 0): void;

	/**
	 * @param Sorting<CategorySortField> $sorting
	 */
	function sortBy(Sorting $sorting): void;

	/**
	 * @return array<Category>
	 */
	function getResult(): array;

	function getTotalCount(): int;

}
