<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Common\Services\AtomicExecutor;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Replacement\StandardReplacement;
use App\Standards\Domain\Standard\Replacement\StandardReplacementRepository;
use App\Standards\Domain\Standard\Standard;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardRepository;
use App\Standards\Domain\Standard\StandardType;

final readonly class UnmsStandardImporter
{
	public function __construct(
		private AtomicExecutor $atomicExecutor,
		private StandardReplacementRepository $standardReplacementRepository,
		private StandardRepository $standardRepository,
		private UnmsConnection $connection,
		private UnmsStandardRepository $unmsStandardRepository
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function createStandardByCatalogueNumber(int $catalogueNumber): Standard
	{
		$unmsStandardDto = $this->unmsStandardRepository->getByCatalogueNumber($catalogueNumber);

		return $this->createStandard($unmsStandardDto);
	}

	public function createStandard(UnmsStandardDto $data): Standard
	{
		$standard = new Standard(
			id: StandardId::create(),
			type: StandardType::Original,
			catalogueNumber: $data->catalogueNumber,
			code: $data->code,
			title: $data->title,
			description: $data->description ?? '',
			language: $data->language,
			publicationDate: $data->publicationDate,
			approvalDate: $data->approvalDate,
			effectiveDate: $data->effectiveDate,
			withdrawalDate: $data->withdrawalDate,
			announcementDate: $data->announcementDate,
			isValid: $data->isValid
		);
		$standard->setSourceHash($data->sourceHash);

		$this->updateStandardCodes($standard, $data);

		$this->atomicExecutor->execute(fn() => $this->standardRepository->add($standard));

		return $standard;
	}

	public function updateStandard(Standard $standard, UnmsStandardDto $data): void
	{
		if ($standard->getSourceHash() === $data->sourceHash) {
			return; // already up to date
		}

		$standard->setCode($data->code);
		$standard->setTitle($data->title);
		$standard->setDescription($data->description ?? '');
		$standard->setLanguage($data->language);
		$standard->setPublicationDate($data->publicationDate);
		$standard->setApprovalDate($data->approvalDate);
		$standard->setEffectiveDate($data->effectiveDate);
		$standard->setWithdrawalDate($data->withdrawalDate);
		$standard->setAnnouncementDate($data->announcementDate);
		$standard->setIsValid($data->isValid);
		$standard->setSourceHash($data->sourceHash);

		$this->updateStandardCodes($standard, $data);

		$this->atomicExecutor->storeChanges();
	}

	public function postProcessStandard(Standard $standard): void
	{
		$stream = $this->connection->get('/norma/' . $standard->getCatalogueNumber());
		$parser = new UnmsStandardPageParser($stream->getContents());

		// revisions
		$revisionCatalogueNumbers = $parser->parseRevisions();

		foreach ($revisionCatalogueNumbers as $catalogueNumber) {
			try {
				$revision = $this->standardRepository->getByCatalogueNumber($catalogueNumber);
			} catch (StandardNotFoundException) {
				try {
					$revision = $this->createStandardByCatalogueNumber($catalogueNumber);
				} catch (StandardNotFoundException) {
					continue;
				}
			}

			$revision->setType(StandardType::Revision);

			$standard->addRevision($revision);
		}

		$this->updateReplacedStandards($standard, $parser->parseReplacedStandards());
		$this->updateReplacementStandards($standard, $parser->parseReplacementStandards());

		$standard->setPostProcessRequired(false);

		$this->atomicExecutor->storeChanges();
	}

	/**
	 * @param array<int> $catalogueNumbers
	 */
	private function updateReplacedStandards(Standard $standard, array $catalogueNumbers): void
	{
		$replacedStandardQuery = $this->standardReplacementRepository->createStandardReplacementQuery();
		$replacedStandardQuery->byReplacement($standard->getId());
		$replacedStandardQuery->withStandard();

		$standardReplacements = $replacedStandardQuery->getResult();

		// remove untracked entities
		foreach ($standardReplacements as $standardReplacement) {
			foreach ($catalogueNumbers as $catalogueNumber) {
				if ($standardReplacement->getStandard()->getCatalogueNumber() === $catalogueNumber) {
					continue 2;
				}
			}

			$do = fn() => $this->standardReplacementRepository->remove($standardReplacement);

			$this->atomicExecutor->execute($do);
		}

		foreach ($catalogueNumbers as $catalogueNumber) {
			try {
				$replacedStandard = $this->standardRepository->getByCatalogueNumber($catalogueNumber);
			} catch (StandardNotFoundException) {
				try {
					$replacedStandard = $this->createStandardByCatalogueNumber($catalogueNumber);
				} catch (StandardNotFoundException) {
					continue;
				}
			}

			foreach ($standardReplacements as $standardReplacement) {
				if ($standardReplacement->getStandard()->getCatalogueNumber() === $catalogueNumber) {
					continue 2;
				}
			}

			$standardReplacement = new StandardReplacement(
				standard: $replacedStandard,
				replacement: $standard
			);

			$do = fn() => $this->standardReplacementRepository->add($standardReplacement);

			$this->atomicExecutor->execute($do);
		}
	}

	/**
	 * @param array<int> $catalogueNumbers
	 */
	private function updateReplacementStandards(Standard $standard, array $catalogueNumbers): void
	{
		$replacedStandardQuery = $this->standardReplacementRepository->createStandardReplacementQuery();
		$replacedStandardQuery->byStandard($standard->getId());
		$replacedStandardQuery->withReplacement();

		$standardReplacements = $replacedStandardQuery->getResult();

		// remove untracked entities
		foreach ($standardReplacements as $standardReplacement) {
			foreach ($catalogueNumbers as $catalogueNumber) {
				if ($standardReplacement->getReplacement()->getCatalogueNumber() === $catalogueNumber) {
					continue 2;
				}
			}

			$do = fn() => $this->standardReplacementRepository->remove($standardReplacement);

			$this->atomicExecutor->execute($do);
		}

		foreach ($catalogueNumbers as $catalogueNumber) {
			try {
				$replacementStandard = $this->standardRepository->getByCatalogueNumber($catalogueNumber);
			} catch (StandardNotFoundException) {
				try {
					$replacementStandard = $this->createStandardByCatalogueNumber($catalogueNumber);
				} catch (StandardNotFoundException) {
					continue;
				}
			}

			foreach ($standardReplacements as $standardReplacement) {
				if ($standardReplacement->getReplacement()->getCatalogueNumber() === $catalogueNumber) {
					continue 2;
				}
			}

			$standardReplacement = new StandardReplacement(
				standard: $standard,
				replacement: $replacementStandard
			);

			$do = fn() => $this->standardReplacementRepository->add($standardReplacement);

			$this->atomicExecutor->execute($do);
		}
	}

	private function updateStandardCodes(Standard $standard, UnmsStandardDto $data): void
	{
		$codes = [];

		foreach (explode(',', $data->icsCodes) as $value) {
			$icsCode = IcsCode::tryFrom(trim($value));

			if ($icsCode !== null) {
				$codes[$icsCode->toString()] = $icsCode;
			}
		}

		$standardIcsList = $standard->getIcs();

		foreach ($standardIcsList as $standardIcs) {
			$icsCode = $standardIcs->getIcsCode();

			if (!isset($codes[$icsCode->toString()])) {
				$standard->removeIcs($icsCode);
			}
		}

		foreach ($codes as $icsCode) {
			$standard->addIcs($icsCode);
		}
	}
}
