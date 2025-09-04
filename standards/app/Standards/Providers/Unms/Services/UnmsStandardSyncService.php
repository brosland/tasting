<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Services;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardRepository;
use App\Standards\Providers\Unms\Domain\UnmsStandardImporter;
use App\Standards\Providers\Unms\Domain\UnmsStandardRepository;

final readonly class UnmsStandardSyncService
{
	/**
	 * @param array<string> $icsCodes
	 */
	public function __construct(
		private array $icsCodes,
		private AtomicExecutor $atomicExecutor,
		private StandardRepository $standardRepository,
		private UnmsStandardRepository $unmsStandardRepository,
		private UnmsStandardImporter $standardImporter
	) {
	}

	public function execute(UnmsStandardSyncRequest $request): void
	{
		$onProgress = $request->onProgress;

		if ($request->skipLoading) {
			$onProgress('Skipped loading of standards.');
		} else {
			$this->loadStandards($onProgress);
		}

		if ($request->skipPostProcessing) {
			$onProgress('Skipped post-processing of standards.');
		} else {
			$this->postProcessStandards($onProgress);
		}
	}

	private function loadStandards(callable $onProgress): void
	{
		foreach ($this->icsCodes as $icsCode) {
			$query = $this->unmsStandardRepository->createUnmsStandardQuery();
			$query->byIcs(IcsCode::from($icsCode));

			$onProgress(sprintf('Loading standards for ICS %s...', $icsCode));
			$totalProcessed = 0;

			do {
				$queryResponse = $query->getResponse();

				$onProgress(sprintf(
					'Processing %s standards (page %d/%d) for ICS %s',
					$queryResponse->dataCount, $queryResponse->page, $queryResponse->pagesCount, $icsCode
				));

				foreach ($queryResponse->data as $data) {
					try {
						$standard = $this->standardRepository->getByCatalogueNumber($data->catalogueNumber);

						if ($standard->getSourceHash() !== $data->sourceHash) {
							$this->standardImporter->updateStandard($standard, $data);
						}
					} catch (StandardNotFoundException) {
						$this->standardImporter->createStandard($data);
					}

					$totalProcessed++;
				}

				$this->atomicExecutor->clear();

				$query->paginate($queryResponse->page + 1);
			} while ($queryResponse->page < $queryResponse->pagesCount);

			$onProgress(sprintf('Total processed standards for ICS %s: %d', $icsCode, $totalProcessed));
		}
	}

	private function postProcessStandards(callable $onProgress): void
	{
		$query = $this->standardRepository->createStandardQuery();
		$query->byPostProcessRequired(true);
		$query->paginate(10);

		do {
			$standards = $query->getResult();

			foreach ($standards as $standard) {
				$onProgress(sprintf(
					'Post-processing standard %d (remaining %d)...',
					$standard->getCatalogueNumber(), $query->getTotalCount()
				));

				$this->standardImporter->postProcessStandard($standard);
			}

			$this->atomicExecutor->clear();
		} while (count($standards) > 0);
	}
}
