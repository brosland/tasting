<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Category\CategoryNotFoundException;
use App\Standards\Domain\Category\CategoryRepository;

final readonly class CategoryDeleteService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private CategoryRepository $categoryRepository
	) {
	}

	/**
	 * @throws CategoryNotFoundException
	 */
	public function execute(CategoryRequest $request): void
	{
		$category = $this->categoryRepository->getById($request->id);

		$do = function () use ($category): void {
			$this->categoryRepository->remove($category);
		};

		$this->atomicExecutor->execute($do);
	}
}
