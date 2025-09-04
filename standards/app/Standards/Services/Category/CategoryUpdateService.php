<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Category\CategoryDto;
use App\Standards\Domain\Category\CategoryDtoFactory;
use App\Standards\Domain\Category\CategoryRepository;

final readonly class CategoryUpdateService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private CategoryDtoFactory $categoryDtoFactory,
		private CategoryRepository $categoryRepository,
	) {
	}

	public function execute(CategoryUpdateRequest $request): CategoryDto
	{
		$category = $this->categoryRepository->getById($request->id);
		$category->setTitle($request->title);
		$category->setExternalId($request->externalId);

		$do = function () use ($category, $request): void {
			$this->categoryRepository->move($category, $request->placement);
		};

		$this->atomicExecutor->execute($do);

		return $this->categoryDtoFactory->createDto($category);
	}
}
