<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Common\Domain\DateTime\DateTimeFactory;
use App\Common\Domain\Language\Language;
use DateTimeImmutable;
use RuntimeException;

final readonly class UnmsStandardDto
{
	/**
	 * @param array<string,mixed> $data
	 */
	public static function from(array $data): self
	{
		$json = json_encode($data);

		if ($json === false) {
			throw new RuntimeException('Failed to encode standard data to JSON.');
		}

		return new self(
			catalogueNumber: (int)$data['catalogueNo'],
			code: (string)$data['sdName'],
			title: (string)$data['name'],
			englishTitle: $data['enName'] ?? null,
			url: $data['url'] ?? null,
			classificationCode: $data['classSeq'] ?? null,
			publicationDate: self::parseDate($data['dateVyd'] ?? null),
			approvalDate: self::parseDate($data['dateSchvl'] ?? null),
			effectiveDate: self::parseDate($data['dateUcin'] ?? null),
			withdrawalDate: self::parseDate($data['dateZrus'] ?? null),
			announcementDate: self::parseDate($data['dateZv'] ?? null),
			language: Language::tryFrom($data['language']),
			processingLevel: $data['urovSprac'] ?? null,
			journal: $data['vestnik'] ?? null,
			journalNote: $data['vestnikNote'] ?? null,
			description: $data['standardSubject'] ?? null,
			pageCount: isset($data['pageCount']) ? (int)$data['pageCount'] : null,
			pageFormat: $data['pageFormat'] ?? null,
			governmentRegulation: $data['govReg'] ?? null,
			harmonizationNotice: $data['vestnikHarm'] ?? null,
			icsCodes: $data['ics'],
			contents: $data['contents'] ?? null,
			isAvailablePrinted: self::toBool($data['canBuy'] ?? false),
			isAvailableElectronic: self::toBool($data['canBuyOnline'] ?? false),
			replacedStandards: $data['replacedStandard'] ?? null,
			replacementStandards: $data['standardReplacement'] ?? null,
			revisions: $data['changes'] ?? null,
			isValid: self::toBool($data['isValid'] ?? false),
			sourceHash: md5($json)
		);
	}

	private function __construct(
		public int $catalogueNumber,
		public string $code,
		public string $title,
		public ?string $englishTitle,
		public ?string $url,
		public ?string $classificationCode,
		public ?DateTimeImmutable $publicationDate,
		public ?DateTimeImmutable $approvalDate,
		public ?DateTimeImmutable $effectiveDate,
		public ?DateTimeImmutable $withdrawalDate,
		public ?DateTimeImmutable $announcementDate,
		public ?Language $language,
		public ?string $processingLevel,
		public ?string $journal,
		public ?string $journalNote,
		public ?string $description,
		public ?int $pageCount,
		public ?string $pageFormat,
		public ?string $governmentRegulation,
		public ?string $harmonizationNotice,
		public string $icsCodes,
		public ?string $contents,
		public bool $isAvailablePrinted,
		public bool $isAvailableElectronic,
		public ?string $replacedStandards,
		public ?string $replacementStandards,
		public ?string $revisions,
		public bool $isValid,
		public string $sourceHash
	) {
	}

	private static function parseDate(?string $value): ?DateTimeImmutable
	{
		if ($value === null || $value === '') {
			return null;
		}

		try {
			return DateTimeFactory::from($value, '!Y-m-d');
		} catch (\Throwable) {
			return null;
		}
	}

	private static function toBool(mixed $value): bool
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool)$value;
	}
}
