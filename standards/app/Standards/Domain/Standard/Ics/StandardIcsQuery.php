<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Ics;

use App\Common\Domain\Sorting;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardId;

interface StandardIcsQuery
{
	function byIcs(IcsCode $icsCode): void;

	function byStandard(StandardId $standardId): void;

	/**
	 * @param Sorting<StandardIcsSortField> $sorting
	 */
	function sortBy(Sorting $sorting): void;

	function paginate(int $limit, int $offset = 0): void;

	/**
	 * @return array<StandardIcs>
	 */
	function getResult(): array;

	function getTotalCount(): int;
}
