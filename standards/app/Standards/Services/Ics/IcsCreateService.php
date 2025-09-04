<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Ics\Ics;
use App\Standards\Domain\Ics\IcsDto;
use App\Standards\Domain\Ics\IcsDtoFactory;
use App\Standards\Domain\Ics\IcsRepository;

final readonly class IcsCreateService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private IcsDtoFactory $icsDtoFactory,
		private IcsRepository $icsRepository
	) {
	}

	public function execute(IcsCreateRequest $request): IcsDto
	{
		$parentCode = $request->code->getParentCode();
		$parent = null;

		if ($parentCode !== null) {
			$parent = $this->icsRepository->getByCode($parentCode);
		}

		$ics = new Ics(
			code: $request->code,
			title: $request->title,
			description: $request->description,
			parent: $parent
		);

		$do = function () use ($ics): void {
			$this->icsRepository->add($ics);
		};

		$this->atomicExecutor->execute($do);

		return $this->icsDtoFactory->createDto($ics);
	}
}
