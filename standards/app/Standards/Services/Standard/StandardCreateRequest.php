<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Common\Domain\Language\Language;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardType;
use DateTimeImmutable;

final class StandardCreateRequest
{
	public function __construct(
		public StandardType $type,
		public int $catalogueNumber,
		public string $code,
		public string $title,
		public string $description,
		public Language $language,
		public ?DateTimeImmutable $publicationDate,
		public ?DateTimeImmutable $approvalDate,
		public ?DateTimeImmutable $effectiveDate,
		public ?DateTimeImmutable $withdrawalDate,
		public ?DateTimeImmutable $announcementDate,
		public bool $isValid,
		/** @var array<IcsCode> */
		public array $icsCodes = [],
		public ?StandardId $parentId = null,
		public ?string $source = null
	) {
	}
}
