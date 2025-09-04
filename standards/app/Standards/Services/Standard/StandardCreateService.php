<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Standard\Standard;
use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardDtoFactory;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardRepository;

final readonly class StandardCreateService
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private StandardDtoFactory $standardDtoFactory,
		private StandardRepository $standardRepository
	) {
	}

	public function execute(StandardCreateRequest $request): StandardDto
	{
		$parent = null;

		if ($request->parentId !== null) {
			$parent = $this->standardRepository->getById($request->parentId);
		}

		$standard = new Standard(
			id: StandardId::create(),
			type: $request->type,
			catalogueNumber: $request->catalogueNumber,
			code: $request->code,
			title: $request->title,
			description: $request->description,
			language: $request->language,
			publicationDate: $request->publicationDate,
			approvalDate: $request->approvalDate,
			effectiveDate: $request->effectiveDate,
			withdrawalDate: $request->withdrawalDate,
			announcementDate: $request->announcementDate,
			isValid: $request->isValid
		);

		$parent?->addRevision($standard);

		if ($request->source !== null) {
			$standard->setSourceHash($request->source);
		}

		foreach ($request->icsCodes as $icsCode) {
			$standard->addIcs($icsCode);
		}

		$do = function () use ($standard): void {
			$this->standardRepository->add($standard);
		};

		$this->atomicExecutor->execute($do);

		return $this->standardDtoFactory->createDto($standard);
	}
}
