<?php
declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Common\Domain\Sorting;
use App\Common\Services\Pagination;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Ics\IcsSortField;

final class IcsFindRequest
{
	use Pagination;

	/**
	 * @param Sorting<IcsSortField>|null $sorting
	 */
	public function __construct(
		public ?Sorting $sorting = null,
		public ?int $minLevel = null,
		public ?int $maxLevel = null,
		public ?IcsCode $parentCode = null,
		public ?string $searchQuery = null
	) {
	}
}
