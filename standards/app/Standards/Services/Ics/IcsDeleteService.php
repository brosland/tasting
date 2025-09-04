<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Ics\IcsNotFoundException;
use App\Standards\Domain\Ics\IcsRepository;

final readonly class IcsDeleteService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private IcsRepository $icsRepository
	) {
	}

	/**
	 * @throws IcsNotFoundException
	 */
	public function execute(IcsRequest $request): void
	{
		$ics = $this->icsRepository->getByCode($request->code);

		$do = function () use ($ics): void {
			$this->icsRepository->remove($ics);
		};

		$this->atomicExecutor->execute($do);
	}
}
