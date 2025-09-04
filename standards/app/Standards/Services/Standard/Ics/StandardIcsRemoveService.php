<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard\Ics;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Standard\Ics\StandardIcsRepository;

final readonly class StandardIcsRemoveService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private StandardIcsRepository $standardIcsRepository
	) {
	}

	public function execute(StandardIcsAddRequest $request): void
	{
		$findQuery = $this->standardIcsRepository->createStandardIcsQuery();
		$findQuery->byIcs($request->icsCode);
		$findQuery->byStandard($request->standardId);

		$result = $findQuery->getResult();

		$do = function () use ($result): void {
			foreach ($result as $standardIcs) {
				$standardIcs->getStandard()->removeIcs($standardIcs->getIcsCode());
			}
		};

		$this->atomicExecutor->execute($do);
	}
}
