<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Services;

use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardDtoFactory;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardRepository;
use App\Standards\Providers\Unms\Domain\UnmsStandardImporter;
use App\Standards\Providers\Unms\Domain\UnmsStandardRepository;

final readonly class UnmsStandardImportService
{
	public function __construct(
		private StandardDtoFactory $standardDtoFactory,
		private StandardRepository $standardRepository,
		private UnmsStandardImporter $unmsStandardImporter,
		private UnmsStandardRepository $unmsStandardRepository,
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function execute(UnmsStandardImportRequest $request): StandardDto
	{
		$unmsStandardDto = $this->unmsStandardRepository->getByCatalogueNumber($request->catalogueNumber);

		try {
			$standard = $this->standardRepository->getByCatalogueNumber($request->catalogueNumber);

			if ($standard->getSourceHash() !== $unmsStandardDto->sourceHash) {
				$this->unmsStandardImporter->updateStandard($standard, $unmsStandardDto);
			}
		} catch (StandardNotFoundException) {
			$standard = $this->unmsStandardImporter->createStandard($unmsStandardDto);
		}

		if ($standard->isPostProcessRequired()) {
			$this->unmsStandardImporter->postProcessStandard($standard);
		}

		return $this->standardDtoFactory->createDto($standard);
	}
}
