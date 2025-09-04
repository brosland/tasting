<?php
declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Common\Services\FindResponse;
use App\Standards\Domain\Category\CategoryDto;
use App\Standards\Domain\Category\CategoryDtoFactory;
use App\Standards\Domain\Category\CategoryRepository;

final readonly class CategoryFindService
{
	public function __construct(
		private CategoryDtoFactory $categoryDtoFactory,
		private CategoryRepository $categoryRepository
	) {
	}

	/**
	 * @return FindResponse<CategoryDto>
	 */
	public function execute(CategoryFindRequest $request): FindResponse
	{
		$query = $this->categoryRepository->createCategoryQuery();

		if ($request->sorting !== null) {
			$query->sortBy($request->sorting);
		}

		if ($request->limit > 0) {
			$query->paginate($request->limit, $request->offset);
		}

		if ($request->minLevel !== null) {
			$query->byMinLevel($request->minLevel);
		}

		if ($request->maxLevel !== null) {
			$query->byMaxLevel($request->maxLevel);
		}

		if ($request->parentId !== null) {
			$query->byParent($request->parentId);
		}

		if ($request->searchQuery !== null) {
			$query->bySearchQuery($request->searchQuery);
		}

		$results = $request->limit === 0 ? [] :
			$this->categoryDtoFactory->createDtoList($query->getResult());

		$totalCount = $request->limit > -1 ? $query->getTotalCount() : null;

		return new FindResponse($results, $totalCount);
	}
}
