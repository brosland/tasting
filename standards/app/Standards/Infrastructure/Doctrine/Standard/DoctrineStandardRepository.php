<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard;

use App\Common\Domain\Exception\Runtime\Database\PersistenceException;
use App\Standards\Domain\Standard\Standard;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardQuery;
use App\Standards\Domain\Standard\StandardRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use RuntimeException;

final readonly class DoctrineStandardRepository implements StandardRepository
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function getByCatalogueNumber(int $catalogueNumber): Standard
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('standard')
			->from(Standard::class, 'standard')
			->where('standard.catalogueNumber = :catalogueNumber')
			->setParameter('catalogueNumber', $catalogueNumber);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw StandardNotFoundException::notFoundByCatalogueNumber($catalogueNumber);
		} catch (NonUniqueResultException $e) {
			throw new RuntimeException('Unexpected result of query.', $e->getCode(), $e);
		}
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function getById(StandardId $id): Standard
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('standard')
			->from(Standard::class, 'standard')
			->where('standard.id = :id')
			->setParameter('id', $id->toBinary());

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw StandardNotFoundException::notFoundById($id);
		} catch (NonUniqueResultException $e) {
			throw new RuntimeException('Unexpected result of query.', $e->getCode(), $e);
		}
	}

	public function createStandardQuery(): StandardQuery
	{
		return new DoctrineStandardQuery($this->entityManager);
	}

	public function add(Standard $standard): void
	{
		try {
			$this->entityManager->persist($standard);
			$this->entityManager->flush();
		} catch (UniqueConstraintViolationException $e) {
			throw new PersistenceException(sprintf(
				'Standard %s (%d) already exists.',
				$standard->getCode(), $standard->getCatalogueNumber()
			), 0, $e);
		}
	}

	public function remove(Standard $standard): void
	{
		$this->entityManager->remove($standard);
		$this->entityManager->flush();
	}

	public function storeChanges(): void
	{
		$this->entityManager->flush();
	}
}
