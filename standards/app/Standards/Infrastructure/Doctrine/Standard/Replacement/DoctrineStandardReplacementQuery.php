<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard\Replacement;

use App\Common\Infrastructure\Doctrine\DoctrineBaseQuery;
use App\Standards\Domain\Standard\Replacement\StandardReplacement;
use App\Standards\Domain\Standard\Replacement\StandardReplacementQuery;
use App\Standards\Domain\Standard\StandardId;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

final class DoctrineStandardReplacementQuery extends DoctrineBaseQuery implements StandardReplacementQuery
{
	/**
	 * @inheritDoc
	 */
	public function byPrimaryKeys(array $primaryKeys): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($primaryKeys): void {
			$qb->andWhere('standard.pk IN(:primaryKeys)')
				->setParameter('primaryKeys', array_unique($primaryKeys));
		};
	}

	/**
	 * @inheritDoc
	 */
	public function byStandard(StandardId|array $standardId): void
	{
		$ids = is_array($standardId) ? $standardId : [$standardId];
		$binaryIds = array_map(fn(StandardId $id) => $id->toBinary(), $ids);

		$this->filters[] = function (QueryBuilder $qb) use ($binaryIds): void {
			$qb->andWhere('standard.id IN(:standardIds)')
				->setParameter('standardIds', $binaryIds);
		};
	}

	/**
	 * @inheritDoc
	 */
	public function byReplacement(StandardId|array $replacementId): void
	{
		$ids = is_array($replacementId) ? $replacementId : [$replacementId];
		$binaryIds = array_map(fn(StandardId $id) => $id->toBinary(), $ids);

		$this->filters[] = function (QueryBuilder $qb) use ($binaryIds): void {
			$qb->andWhere('replacement.id IN(:replacementIds)')
				->setParameter('replacementIds', $binaryIds);
		};
	}

	public function withStandard(): void
	{
		$this->filters[] = function (QueryBuilder $qb): void {
			$qb->addSelect('standard');
		};
	}

	public function withReplacement(): void
	{
		$this->filters[] = function (QueryBuilder $qb): void {
			$qb->addSelect('replacement');;
		};
	}

	public function getTotalCount(): int
	{
		$qb = $this->createBaseQuery();
		$qb->select('COUNT(standardReplacement.pk)');

		try {
			return (int)$qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException) {
			return 0;
		} catch (NonUniqueResultException) {
			throw new RuntimeException('Unexpected result of the query.');
		}
	}

	/**
	 * @return array<StandardReplacement>
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
		$qb->select('standardReplacement')
			->from(StandardReplacement::class, 'standardReplacement', 'standardReplacement.pk')
			->innerJoin('standardReplacement.standard', 'standard')
			->innerJoin('standardReplacement.replacement', 'replacement');

		$this->applyFilters($qb);

		return $qb;
	}
}
