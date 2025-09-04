<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Language\Language;
use App\Standards\Domain\Ics\IcsCode;
use DateTimeImmutable;

final readonly class StandardDto
{
	/**
	 * @param array<StandardRefDto> $replacedStandards
	 * @param array<StandardRefDto> $replacementStandards
	 * @param array<IcsCode> $icsCodes
	 */
	public function __construct(
		public StandardId $id,
		public StandardType $type,
		public int $catalogueNumber,
		public string $code,
		public string $title,
		public string $description,
		public ?Language $language,
		public ?DateTimeImmutable $publicationDate,
		public ?DateTimeImmutable $approvalDate,
		public ?DateTimeImmutable $effectiveDate,
		public ?DateTimeImmutable $withdrawalDate,
		public ?DateTimeImmutable $announcementDate,
		public bool $isValid,
		public ?StandardRefDto $parent,
		public array $replacedStandards,
		public array $replacementStandards,
		public array $icsCodes
	) {
	}
}
