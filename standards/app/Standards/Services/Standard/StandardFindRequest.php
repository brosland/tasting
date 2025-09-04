<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Common\Domain\Language\Language;
use App\Common\Domain\Sorting;
use App\Common\Services\Pagination;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardSortField;
use App\Standards\Domain\Standard\StandardType;
use DateTimeImmutable;

final class StandardFindRequest
{
	use Pagination;

	/**
	 * @param Sorting<StandardSortField>|null $sorting
	 */
	public function __construct(
		public ?Sorting $sorting = null,
		/** @var array<StandardType> */
		public array $types = [],
		public ?Language $language = null,
		public ?DateTimeImmutable $publicationStartDate = null,
		public ?DateTimeImmutable $publicationEndDate = null,
		public ?DateTimeImmutable $approvalStartDate = null,
		public ?DateTimeImmutable $approvalEndDate = null,
		public ?DateTimeImmutable $effectiveStartDate = null,
		public ?DateTimeImmutable $effectiveEndDate = null,
		public ?DateTimeImmutable $withdrawalStartDate = null,
		public ?DateTimeImmutable $withdrawalEndDate = null,
		public ?DateTimeImmutable $announcementStartDate = null,
		public ?DateTimeImmutable $announcementEndDate = null,
		public ?bool $isValid = null,
		public ?bool $relinkRequired = null,
		public ?StandardId $parent = null,
//		public ?StandardId $replacedStandard = null,
//		public ?StandardId $replacementStandard = null,
		public ?IcsCode $ics = null,
		public ?string $searchQuery = null
	) {
	}
}
