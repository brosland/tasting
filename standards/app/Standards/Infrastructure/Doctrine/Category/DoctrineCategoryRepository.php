<?php
declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Category;

use App\Standards\Domain\Category\Category;
use App\Standards\Domain\Category\CategoryId;
use App\Standards\Domain\Category\CategoryNotFoundException;
use App\Standards\Domain\Category\CategoryPlacement;
use App\Standards\Domain\Category\CategoryQuery;
use App\Standards\Domain\Category\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use InvalidArgumentException;
use RuntimeException;

final readonly class DoctrineCategoryRepository implements CategoryRepository
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getById(CategoryId $id): Category
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('category')
			->from(Category::class, 'category')
			->where('category.id = :id')
			->setParameter('id', $id->toBinary());

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw CategoryNotFoundException::notFoundById($id);
		} catch (NonUniqueResultException $e) {
			throw new RuntimeException('Unexpected result of query.', $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getByExternalId(string $externalId): Category
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('category')
			->from(Category::class, 'category')
			->where('category.externalId = :externalId')
			->setParameter('externalId', $externalId);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw CategoryNotFoundException::notFoundByExternalId($externalId);
		} catch (NonUniqueResultException $e) {
			throw new RuntimeException('Unexpected result of query.', $e->getCode(), $e);
		}
	}

	/**
	 * @return array<Category>
	 * @throws CategoryNotFoundException
	 */
	public function findTreePath(CategoryId $categoryId): array
	{
		$category = $this->getById($categoryId);

		$qb = $this->entityManager->createQueryBuilder()
			->select('category')
			->from(Category::class, 'category')
			->where('category.treeLeft <= :treeLeft')
			->andWhere('category.treeRight >= :treeRight')
			->orderBy('category.treeLeft', 'ASC')
			->setParameter('treeLeft', $category->getTreeLeft())
			->setParameter('treeRight', $category->getTreeRight());

		return $qb->getQuery()->getResult();
	}

	public function createCategoryQuery(): CategoryQuery
	{
		return new DoctrineCategoryQuery($this->entityManager);
	}

	/**
	 * @throws CategoryNotFoundException
	 */
	public function add(Category $category, ?CategoryPlacement $placement = null): void
	{
		$this->move($category, $placement ?? CategoryPlacement::END($category->getParent()?->getId()));
	}

	/**
	 * @throws CategoryNotFoundException
	 */
	public function move(Category $category, CategoryPlacement $placement): void
	{
		$parent = $sibling = null;

		if ($placement->parentId !== null) {
			$parent = $this->getById($placement->parentId);
		}

		if ($placement->siblingId !== null) {
			$sibling = $this->getById($placement->siblingId);
		}

		if ($placement->position === CategoryPlacement::POSITION_START) {
			if ($parent === null) {
				$this->getNestedTreeRepository()->persistAsFirstChild($category);
			} else {
				$this->getNestedTreeRepository()->persistAsFirstChildOf($category, $parent);
			}
		} elseif ($placement->position === CategoryPlacement::POSITION_AFTER) {
			if ($sibling === null) {
				throw new InvalidArgumentException('Missing category sibling.');
			}

			$this->getNestedTreeRepository()->persistAsNextSiblingOf($category, $sibling);
		} elseif ($placement->position === CategoryPlacement::POSITION_END) {
			if ($parent === null) {
				$this->getNestedTreeRepository()->persistAsLastChild($category);
			} else {
				$this->getNestedTreeRepository()->persistAsLastChildOf($category, $parent);
			}
		}

		$this->entityManager->flush();
	}

	public function remove(Category $category): void
	{
		$this->entityManager->remove($category);
		$this->entityManager->flush();
	}

	public function storeChanges(): void
	{
		$this->entityManager->flush();
	}

	/**
	 * @return NestedTreeRepository<Category>
	 */
	private function getNestedTreeRepository(): NestedTreeRepository
	{
		/** @var NestedTreeRepository<Category> $nestedTreeRepository */
		$nestedTreeRepository = $this->entityManager->getRepository(Category::class);

		return $nestedTreeRepository;
	}
}
