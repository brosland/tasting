<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard\Ics;

use App\Standards\Domain\Standard\Ics\StandardIcs;
use App\Standards\Domain\Standard\Ics\StandardIcsQuery;
use App\Standards\Domain\Standard\Ics\StandardIcsRepository;
use App\Standards\Domain\Standard\StandardId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineStandardIcsRepository implements StandardIcsRepository
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function findByStandards(array $standardIds): array
	{
		$binaryIds = array_map(fn(StandardId $id) => $id->toBinary(), $standardIds);

		$qb = $this->entityManager->createQueryBuilder()
			->select('standardIcs')
			->from(StandardIcs::class, 'standardIcs')
			->innerJoin('standardIcs.standard', 'standard')
			->where('standard.id IN (:standardIds)')
			->setParameter('standardIds', $binaryIds);

		return $qb->getQuery()->getResult();
	}


	public function createStandardIcsQuery(): StandardIcsQuery
	{
		return new DoctrineStandardIcsQuery($this->entityManager);
	}

	public function add(StandardIcs $standardIcs): void
	{
		$this->entityManager->persist($standardIcs);
		$this->entityManager->flush();
	}

	public function remove(StandardIcs $standardIcs): void
	{
		$this->entityManager->remove($standardIcs);
		$this->entityManager->flush();
	}

	public function storeChanges(): void
	{
		$this->entityManager->flush();
	}
}
