<?php
declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Category;

use App\Common\Domain\Sorting;
use App\Common\Infrastructure\Doctrine\DoctrineBaseQuery;
use App\Standards\Domain\Category\Category;
use App\Standards\Domain\Category\CategoryId;
use App\Standards\Domain\Category\CategoryQuery;
use App\Standards\Domain\Category\CategorySortField;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

final class DoctrineCategoryQuery extends DoctrineBaseQuery implements CategoryQuery
{
	public function byMaxLevel(int $maxLevel): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($maxLevel): void {
			$qb->andWhere('category.treeLevel <= :maxLevel')
				->setParameter('maxLevel', $maxLevel);
		};
	}

	public function byMinLevel(int $minLevel): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($minLevel): void {
			$qb->andWhere('category.treeLevel >= :minLevel')
				->setParameter('minLevel', $minLevel);
		};
	}

	public function byParent(CategoryId $parentId): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($parentId): void {
			$qb->innerJoin(Category::class, 'parentCategory', Join::WITH, 'parentCategory.id = :parentCategoryId')
				->andWhere('category.root = parentCategory.root')
				->andWhere('category.treeLeft > parentCategory.treeLeft')
				->andWhere('category.treeRight < parentCategory.treeRight')
				->setParameter('parentCategoryId', $parentId->toBinary());
		};
	}

	/**
	 * @inheritDoc
	 */
	public function byPrimaryKeys(array $primaryKeys): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($primaryKeys): void {
			$qb->andWhere('category.pk IN(:primaryKeys)')
				->setParameter('primaryKeys', array_unique($primaryKeys));
		};
	}

	public function bySearchQuery(string $searchQuery): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($searchQuery): void {
			$parts = preg_split("/[\s,]+/", $searchQuery, -1, PREG_SPLIT_NO_EMPTY);

			if ($parts !== false && count($parts) > 0) {
				$condition = new Orx();

				foreach ($parts as $i => $part) {
					$condition->add("category.title LIKE :searchQuery_$i");

					$qb->setParameter("searchQuery_$i", sprintf('%%%s%%', $part));
				}

				$qb->andWhere($condition);
			}
		};
	}

	/**
	 * @param Sorting<CategorySortField> $sorting
	 */
	public function sortBy(Sorting $sorting): void
	{
		if ($sorting->field === CategorySortField::Tree) {
			$this->addSorting('category.treeLeft', $sorting->ascending);
		} else {
			$this->addSorting('category.' . $sorting->field->value, $sorting->ascending);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getResult(): array
	{
		$qb = $this->createBaseQuery();

		$this->applySorting($qb);
		$this->applyPagination($qb);

		return $qb->getQuery()->getResult();
	}

	public function getTotalCount(): int
	{
		$qb = $this->createBaseQuery();
		$qb->select('COUNT(category.pk)');

		try {
			return (int)$qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException) {
			return 0;
		} catch (NonUniqueResultException) {
			throw new RuntimeException('Unexpected result of the query.');
		}
	}

	private function createBaseQuery(): QueryBuilder
	{
		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('category')
			->from(Category::class, 'category', 'category.pk');

		$this->applyFilters($qb);

		return $qb;
	}
}
