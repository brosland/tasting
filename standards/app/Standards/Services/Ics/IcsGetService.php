<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Standards\Domain\Ics\IcsDto;
use App\Standards\Domain\Ics\IcsDtoFactory;
use App\Standards\Domain\Ics\IcsRepository;

final readonly class IcsGetService
{
	public function __construct(
		private IcsDtoFactory $icsDtoFactory,
		private IcsRepository $icsRepository
	) {
	}

	public function execute(IcsRequest $request): IcsDto
	{
		$ics = $this->icsRepository->getByCode($request->code);

		return $this->icsDtoFactory->createDto($ics);
	}
}
