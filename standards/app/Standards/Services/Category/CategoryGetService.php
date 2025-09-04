<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Standards\Domain\Category\CategoryDto;
use App\Standards\Domain\Category\CategoryDtoFactory;
use App\Standards\Domain\Category\CategoryRepository;
use InvalidArgumentException;

final readonly class CategoryGetService
{
	public function __construct(
		private CategoryDtoFactory $categoryDtoFactory,
		private CategoryRepository $categoryRepository
	) {
	}

	public function execute(CategoryGetRequest $request): CategoryDto
	{
		if ($request instanceof CategoryRequest) {
			$category = $this->categoryRepository->getById($request->id);
		} elseif ($request instanceof CategoryGetByExternalIdRequest) {
			$category = $this->categoryRepository->getByExternalId($request->externalId);
		} else {
			throw new InvalidArgumentException('Unknown request type');
		}

		return $this->categoryDtoFactory->createDto($category);
	}
}
