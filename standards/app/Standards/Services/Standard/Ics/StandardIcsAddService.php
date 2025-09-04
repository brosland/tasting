<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard\Ics;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Standard\StandardRepository;

final readonly class StandardIcsAddService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private StandardRepository $standardRepository,
	) {
	}

	public function execute(StandardIcsAddRequest $request): void
	{
		$standard = $this->standardRepository->getById($request->standardId);
		$standard->addIcs($request->icsCode);

		$this->atomicExecutor->storeChanges();
	}
}
