<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Category\Category;
use App\Standards\Domain\Category\CategoryDto;
use App\Standards\Domain\Category\CategoryDtoFactory;
use App\Standards\Domain\Category\CategoryId;
use App\Standards\Domain\Category\CategoryRepository;

final readonly class CategoryCreateService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private CategoryDtoFactory $categoryDtoFactory,
		private CategoryRepository $categoryRepository
	) {
	}

	public function execute(CategoryCreateRequest $request): CategoryDto
	{
		$parent = null;

		if ($request->placement->parentId !== null) {
			$parent = $this->categoryRepository->getById($request->placement->parentId);
		}

		$category = new Category(
			id: CategoryId::create(),
			title: $request->title,
			parent: $parent,
			externalId: $request->externalId
		);

		$do = function () use ($category, $request): void {
			$this->categoryRepository->add($category, $request->placement);
		};

		$this->atomicExecutor->execute($do);

		return $this->categoryDtoFactory->createDto($category);
	}
}
