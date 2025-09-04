<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

final readonly class CategoryDtoFactory
{
	public function __construct(
		private CategoryRepository $categoryRepository
	) {
	}

	public function createDto(Category $category): CategoryDto
	{
		return $this->createDtoList([$category])[0];
	}

	/**
	 * @param array<Category> $categories
	 * @return array<CategoryDto>
	 */
	public function createDtoList(array $categories): array
	{
		$parentList = $this->createParentCategoryListByCategories($categories);
		$result = [];

		foreach ($categories as $key => $category) {
			$parentPk = $category->getParent()?->getPk();

			$result[$key] = new CategoryDto(
				id: $category->getId(),
				title: $category->getTitle(),
				externalId: $category->getExternalId(),
				metadata: $category->getMetadata(),
				parentId: ($parentList[$parentPk] ?? null)?->getId(),
				treeLevel: $category->getTreeLevel(),
				treeLeft: $category->getTreeLeft(),
				treeRight: $category->getTreeRight(),
				subItemCount: $category->getSubBlockCount()
			);
		}

		return $result;
	}

	/**
	 * @param array<int> $primaryKeys
	 * @return array<CategoryDto>
	 */
	public function createDtoListByPrimaryKeys(array $primaryKeys): array
	{
		if (count($primaryKeys) === 0) {
			return [];
		}

		$query = $this->categoryRepository->createCategoryQuery();
		$query->byPrimaryKeys($primaryKeys);

		return $this->createDtoList($query->getResult());
	}

	/**
	 * @param array<Category> $categories
	 * @return array<int,Category> Indexed by primary keys.
	 */
	private function createParentCategoryListByCategories(array $categories): array
	{
		$primaryKeys = [];

		foreach ($categories as $category) {
			$primaryKeys[] = $category->getParent()?->getPk();
		}

		$primaryKeys = array_unique(array_filter($primaryKeys, fn($pk) => $pk !== null));

		if (count($primaryKeys) === 0) {
			return [];
		}

		$query = $this->categoryRepository->createCategoryQuery();
		$query->byPrimaryKeys($primaryKeys);

		return $query->getResult();
	}
}
