<?php
declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Common\Domain\Sorting;
use App\Common\Services\Pagination;
use App\Standards\Domain\Category\CategoryId;
use App\Standards\Domain\Category\CategorySortField;

final class CategoryFindRequest
{
	use Pagination;

	/**
	 * @param Sorting<CategorySortField>|null $sorting
	 */
	public function __construct(
		public ?Sorting $sorting = null,
		public ?int $minLevel = null,
		public ?int $maxLevel = null,
		public ?CategoryId $parentId = null,
		public ?string $searchQuery = null
	) {
	}
}
