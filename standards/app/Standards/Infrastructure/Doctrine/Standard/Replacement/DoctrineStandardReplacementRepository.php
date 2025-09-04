<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard\Replacement;

use App\Standards\Domain\Standard\Replacement\StandardReplacement;
use App\Standards\Domain\Standard\Replacement\StandardReplacementQuery;
use App\Standards\Domain\Standard\Replacement\StandardReplacementRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineStandardReplacementRepository implements StandardReplacementRepository
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {
	}

	public function createStandardReplacementQuery(): StandardReplacementQuery
	{
		return new DoctrineStandardReplacementQuery($this->entityManager);
	}

	public function add(StandardReplacement $standardReplacement): void
	{
		$this->entityManager->persist($standardReplacement);
		$this->entityManager->flush();
	}

	public function remove(StandardReplacement $standardReplacement): void
	{
		$this->entityManager->remove($standardReplacement);
		$this->entityManager->flush();
	}

	public function storeChanges(): void
	{
		$this->entityManager->flush();
	}
}
