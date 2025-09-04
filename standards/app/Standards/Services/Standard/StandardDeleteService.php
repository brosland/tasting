<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardRepository;

final readonly class StandardDeleteService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private StandardRepository $standardRepository
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function execute(StandardId $id): void
	{
		$standard = $this->standardRepository->getById($id);

		$do = function () use ($standard): void {
			$this->standardRepository->remove($standard);
		};

		$this->atomicExecutor->execute($do);
	}
}
