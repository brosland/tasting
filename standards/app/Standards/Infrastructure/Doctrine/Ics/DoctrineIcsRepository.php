<?php
declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Ics;

use App\Standards\Domain\Ics\Ics;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Ics\IcsNotFoundException;
use App\Standards\Domain\Ics\IcsPlacement;
use App\Standards\Domain\Ics\IcsQuery;
use App\Standards\Domain\Ics\IcsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use InvalidArgumentException;
use RuntimeException;

final readonly class DoctrineIcsRepository implements IcsRepository
{
	public function __construct(
		private EntityManagerInterface $entityManager
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getByCode(IcsCode $code): Ics
	{
		$qb = $this->entityManager->createQueryBuilder()
			->select('ics')
			->from(Ics::class, 'ics')
			->where('ics.code = :code')
			->setParameter('code', $code->toString());

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw IcsNotFoundException::notFoundByCode($code);
		} catch (NonUniqueResultException $e) {
			throw new RuntimeException('Unexpected result of query.', $e->getCode(), $e);
		}
	}

	/**
	 * @return array<Ics>
	 * @throws IcsNotFoundException
	 */
	public function findTreePath(IcsCode $icsCode): array
	{
		$ics = $this->getByCode($icsCode);

		$qb = $this->entityManager->createQueryBuilder()
			->select('ics')
			->from(Ics::class, 'ics')
			->where('ics.treeLeft <= :treeLeft')
			->andWhere('ics.treeRight >= :treeRight')
			->orderBy('ics.treeLeft', 'ASC')
			->setParameter('treeLeft', $ics->getTreeLeft())
			->setParameter('treeRight', $ics->getTreeRight());

		return $qb->getQuery()->getResult();
	}

	public function createIcsQuery(): IcsQuery
	{
		return new DoctrineIcsQuery($this->entityManager);
	}

	/**
	 * @throws IcsNotFoundException
	 */
	public function add(Ics $ics, ?IcsPlacement $placement = null): void
	{
		$this->move($ics, $placement ?? IcsPlacement::END($ics->getParent()?->getCode()));
	}

	/**
	 * @throws IcsNotFoundException
	 */
	public function move(Ics $ics, IcsPlacement $placement): void
	{
		$parent = $sibling = null;

		if ($placement->parentCode !== null) {
			$parent = $this->getByCode($placement->parentCode);
		}

		if ($placement->siblingCode !== null) {
			$sibling = $this->getByCode($placement->siblingCode);
		}

		if ($placement->position === IcsPlacement::POSITION_START) {
			if ($parent === null) {
				$this->getNestedTreeRepository()->persistAsFirstChild($ics);
			} else {
				$this->getNestedTreeRepository()->persistAsFirstChildOf($ics, $parent);
			}
		} elseif ($placement->position === IcsPlacement::POSITION_AFTER) {
			if ($sibling === null) {
				throw new InvalidArgumentException('Missing ICS sibling.');
			}

			$this->getNestedTreeRepository()->persistAsNextSiblingOf($ics, $sibling);
		} elseif ($placement->position === IcsPlacement::POSITION_END) {
			if ($parent === null) {
				$this->getNestedTreeRepository()->persistAsLastChild($ics);
			} else {
				$this->getNestedTreeRepository()->persistAsLastChildOf($ics, $parent);
			}
		}

		$this->entityManager->flush();
	}

	public function remove(Ics $ics): void
	{
		$this->entityManager->remove($ics);
		$this->entityManager->flush();
	}

	public function storeChanges(): void
	{
		$this->entityManager->flush();
	}

	/**
	 * @return NestedTreeRepository<Ics>
	 */
	private function getNestedTreeRepository(): NestedTreeRepository
	{
		/** @var NestedTreeRepository<Ics> $nestedTreeRepository */
		$nestedTreeRepository = $this->entityManager->getRepository(Ics::class);

		return $nestedTreeRepository;
	}
}
