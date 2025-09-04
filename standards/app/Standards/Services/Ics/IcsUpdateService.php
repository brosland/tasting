<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Ics\IcsDto;
use App\Standards\Domain\Ics\IcsDtoFactory;
use App\Standards\Domain\Ics\IcsRepository;

final readonly class IcsUpdateService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private IcsDtoFactory $icsDtoFactory,
		private IcsRepository $icsRepository,
	) {
	}

	public function execute(IcsUpdateRequest $request): IcsDto
	{
		$ics = $this->icsRepository->getByCode($request->code);
		$ics->setTitle($request->title);
		$ics->setDescription($request->description);

		$this->atomicExecutor->storeChanges();

		return $this->icsDtoFactory->createDto($ics);
	}
}
