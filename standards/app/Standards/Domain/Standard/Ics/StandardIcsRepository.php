<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Ics;

use App\Standards\Domain\Standard\StandardId;

interface StandardIcsRepository
{
	/**
	 * @param array<StandardId> $standardIds
	 * @return array<StandardIcs>
	 */
	function findByStandards(array $standardIds): array;

	function createStandardIcsQuery(): StandardIcsQuery;

	function add(StandardIcs $standardIcs): void;

	function remove(StandardIcs $standardIcs): void;

	function storeChanges(): void;
}
