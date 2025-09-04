<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard;

use App\Common\Domain\Sorting;
use App\Common\Infrastructure\Doctrine\DoctrineBaseQuery;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Ics\StandardIcs;
use App\Standards\Domain\Standard\Standard;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardQuery;
use App\Standards\Domain\Standard\StandardSortField;
use App\Standards\Domain\Standard\StandardType;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

final class DoctrineStandardQuery extends DoctrineBaseQuery implements StandardQuery
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
	 * @param array<StandardType> $types
	 */
	public function byType(array $types): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($types): void {
			$qb->andWhere($qb->expr()->in('standard.type', ':types'))
				->setParameter('types', $types);
		};
	}

	public function byPublicationDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($startDate, $endDate): void {
			if ($startDate !== null) {
				$qb->andWhere('standard.publicationDate >= :startPublicationDate')
					->setParameter('startPublicationDate', $startDate);
			}

			if ($endDate !== null) {
				$qb->andWhere('standard.publicationDate < :endPublicationDate')
					->setParameter('endPublicationDate', $endDate);
			}
		};
	}

	public function byApprovalDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($startDate, $endDate): void {
			if ($startDate !== null) {
				$qb->andWhere('standard.approvalDate >= :startApprovalDate')
					->setParameter('startApprovalDate', $startDate);
			}

			if ($endDate !== null) {
				$qb->andWhere('standard.approvalDate < :endApprovalDate')
					->setParameter('endApprovalDate', $endDate);
			}
		};
	}

	public function byEffectiveDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($startDate, $endDate): void {
			if ($startDate !== null) {
				$qb->andWhere('standard.effectiveDate >= :startEffectiveDate')
					->setParameter('startEffectiveDate', $startDate);
			}

			if ($endDate !== null) {
				$qb->andWhere('standard.effectiveDate < :endEffectiveDate')
					->setParameter('endEffectiveDate', $endDate);
			}
		};
	}

	public function byWithdrawalDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($startDate, $endDate): void {
			if ($startDate !== null) {
				$qb->andWhere('standard.withdrawalDate >= :startWithdrawalDate')
					->setParameter('startWithdrawalDate', $startDate);
			}

			if ($endDate !== null) {
				$qb->andWhere('standard.withdrawalDate < :endWithdrawalDate')
					->setParameter('endWithdrawalDate', $endDate);
			}
		};
	}

	public function byAnnouncementDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($startDate, $endDate): void {
			if ($startDate !== null) {
				$qb->andWhere('standard.announcementDate >= :startAnnouncementDate')
					->setParameter('startAnnouncementDate', $startDate);
			}

			if ($endDate !== null) {
				$qb->andWhere('standard.announcementDate < :endAnnouncementDate')
					->setParameter('endAnnouncementDate', $endDate);
			}
		};
	}

	public function byIsValid(bool $isValid): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($isValid): void {
			$qb->andWhere('standard.isValid = :isValid')
				->setParameter('isValid', $isValid);
		};
	}

	public function byPostProcessRequired(bool $postProcessRequired): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($postProcessRequired): void {
			$qb->andWhere('standard.postProcessRequired = :postProcessRequired')
				->setParameter('postProcessRequired', $postProcessRequired);
		};
	}

	public function byParent(StandardId $parentId): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($parentId): void {
			$qb->andWhere('parent.id = :parentId')
				->setParameter('parentId', $parentId->toBinary());
		};
	}

	public function byIcs(IcsCode $icsCode): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($icsCode): void {
			$sub = $qb->getEntityManager()->createQueryBuilder();
			$sub->select('1')
				->from(StandardIcs::class, 'standardIcs')
				->where('standardIcs.standard = standard.pk')
				->andWhere('standardIcs.icsCode = :icsCode');

			$qb->andWhere($qb->expr()->exists($sub->getDQL()))
				->setParameter('icsCode', $icsCode->toString());
		};
	}

	public function bySearchQuery(string $searchQuery): void
	{
		$this->filters[] = function (QueryBuilder $qb) use ($searchQuery): void {
			$condition = new Orx([
				'standard.code LIKE :searchQuery',
				'standard.title LIKE :searchQuery',
				'standard.description LIKE :searchQuery',
			]);

			$qb->andWhere($condition)
				->setParameter('searchQuery', sprintf('%%%s%%', $searchQuery));
		};
	}

	/**
	 * @param Sorting<StandardSortField> $sorting
	 */
	public function sortBy(Sorting $sorting): void
	{
		$this->addSorting('standard.' . $sorting->field->value, $sorting->ascending);
	}

	public function getTotalCount(): int
	{
		$qb = $this->createBaseQuery();
		$qb->select('COUNT(standard.pk)');

		try {
			return (int)$qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException) {
			return 0;
		} catch (NonUniqueResultException) {
			throw new RuntimeException('Unexpected result of the query.');
		}
	}

	/**
	 * @return array<Standard>
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
		$qb->select('standard')
			->from(Standard::class, 'standard', 'standard.pk')
			->leftJoin('standard.parent', 'parent');

		$this->applyFilters($qb);

		return $qb;
	}
}
