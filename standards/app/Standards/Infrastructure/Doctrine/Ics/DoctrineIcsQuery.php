<?php
declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Ics;

use App\Common\Domain\Sorting;
use App\Common\Infrastructure\Doctrine\DoctrineBaseQuery;
use App\Standards\Domain\Ics\Ics;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Ics\IcsQuery;
use App\Standards\Domain\Ics\IcsSortField;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

final class DoctrineIcsQuery extends DoctrineBaseQuery implements IcsQuery
{
	public function byMaxLevel(int $maxLevel): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($maxLevel): void {
			$qb->andWhere('ics.treeLevel <= :maxLevel')
				->setParameter('maxLevel', $maxLevel);
		};
	}

	public function byMinLevel(int $minLevel): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($minLevel): void {
			$qb->andWhere('ics.treeLevel >= :minLevel')
				->setParameter('minLevel', $minLevel);
		};
	}

	public function byParent(IcsCode $parentCode): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($parentCode): void {
			$qb->innerJoin(Ics::class, 'parent', Join::WITH, 'parent.code = :parentCode')
				->andWhere('ics.root = parent.root')
				->andWhere('ics.treeLeft > parent.treeLeft')
				->andWhere('ics.treeRight < parent.treeRight')
				->setParameter('parentCode', $parentCode->toString());
		};
	}

	/**
	 * @inheritDoc
	 */
	public function byPrimaryKeys(array $primaryKeys): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($primaryKeys): void {
			$qb->andWhere('ics.pk IN(:primaryKeys)')
				->setParameter('primaryKeys', array_unique($primaryKeys));
		};
	}

	public function bySearchQuery(string $searchQuery): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($searchQuery): void {
			$parts = preg_split("/[\s,]+/", $searchQuery, -1, PREG_SPLIT_NO_EMPTY);

			if ($parts !== false && count($parts) > 0) {
				$condition = new Orx();

				$qb->andWhere('ics.code LIKE :searchQuery_code')
					->setParameter('searchQuery_code', sprintf('%s%%', $parts[0]));;

				foreach ($parts as $i => $part) {
					$condition->add("ics.title LIKE :searchQuery_$i");
					$condition->add("ics.description LIKE :searchQuery_$i");

					$qb->setParameter("searchQuery_$i", sprintf('%%%s%%', $part));
				}

				$qb->andWhere($condition);
			}
		};
	}

	/**
	 * @param Sorting<IcsSortField> $sorting
	 */
	public function sortBy(Sorting $sorting): void
	{
		if ($sorting->field === IcsSortField::Tree) {
			$this->addSorting('ics.treeLeft', $sorting->ascending);
		} else {
			$this->addSorting('ics.' . $sorting->field->value, $sorting->ascending);
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
		$qb->select('COUNT(ics.pk)');

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
		$qb->select('ics')
			->from(Ics::class, 'ics', 'ics.pk');

		$this->applyFilters($qb);

		return $qb;
	}
}
