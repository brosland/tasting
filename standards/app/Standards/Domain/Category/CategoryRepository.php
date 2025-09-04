<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

interface CategoryRepository
{
	/**
	 * @throws CategoryNotFoundException
	 */
	function getById(CategoryId $id): Category;

	/**
	 * @throws CategoryNotFoundException
	 */
	function getByExternalId(string $externalId): Category;

	/**
	 * @return array<Category>
	 * @throws CategoryNotFoundException
	 */
	public function findTreePath(CategoryId $categoryId): array;

	function createCategoryQuery(): CategoryQuery;

	function add(Category $category, ?CategoryPlacement $placement = null): void;

	function move(Category $category, CategoryPlacement $placement): void;

	function remove(Category $category): void;

	function storeChanges(): void;
}
