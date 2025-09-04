<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard\Ics;

use App\Common\Domain\Sorting;
use App\Common\Infrastructure\Doctrine\DoctrineBaseQuery;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Ics\StandardIcs;
use App\Standards\Domain\Standard\Ics\StandardIcsQuery;
use App\Standards\Domain\Standard\Ics\StandardIcsSortField;
use App\Standards\Domain\Standard\StandardId;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

final class DoctrineStandardIcsQuery extends DoctrineBaseQuery implements StandardIcsQuery
{
	public function byIcs(IcsCode $icsCode): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($icsCode): void {
			$qb->andWhere('standardIcs.icsCode = :icsCode')
				->setParameter('icsCode', $icsCode->toString());
		};
	}

	public function byStandard(StandardId $standardId): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($standardId): void {
			$qb->andWhere('standard.id = :standardId')
				->setParameter('standardId', $standardId->toBinary());
		};
	}

	/**
	 * @param Sorting<StandardIcsSortField> $sorting
	 */
	public function sortBy(Sorting $sorting): void
	{
		if ($sorting->field === StandardIcsSortField::Ics) {
			$this->addSorting('standardIcs.icsCode', $sorting->ascending);
		} else { // StandardIcsSortField::Standard
			$this->addSorting('standard.code', $sorting->ascending);
		}
	}

	public function getTotalCount(): int
	{
		$qb = $this->createBaseQuery();
		$qb->select('COUNT(standardIcs.pk)');

		try {
			return (int)$qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException) {
			return 0;
		} catch (NonUniqueResultException) {
			throw new RuntimeException('Unexpected result of the query.');
		}
	}

	/**
	 * @return array<StandardIcs>
	 */
	public function getResult(): array
	{
		$qb = $this->createBaseQuery();

		$this->applySorting($qb);
		$this->applyPagination($qb);

		return $qb->getQuery()->getResult();
	}

	private function createBaseQuery(): QueryBuilder
	{
		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('standardIcs')
			->from(StandardIcs::class, 'standardIcs', 'standardIcs.pk')
			->innerJoin('standardIcs.standard', 'standard');

		$this->applyFilters($qb);

		return $qb;
	}
}
