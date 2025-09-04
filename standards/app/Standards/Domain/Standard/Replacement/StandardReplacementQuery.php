<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Replacement;

use App\Standards\Domain\Standard\StandardId;

interface StandardReplacementQuery
{
	/**
	 * @param array<int> $primaryKeys
	 */
	function byPrimaryKeys(array $primaryKeys): void;

	/**
	 * @param StandardId|array<StandardId> $standardId
	 */
	function byStandard(StandardId|array $standardId): void;

	/**
	 * @param StandardId|array<StandardId> $replacementId
	 */
	function byReplacement(StandardId|array $replacementId): void;

	function withStandard(): void;

	function withReplacement(): void;

	function paginate(int $limit, int $offset = 0): void;

	/**
	 * @return array<StandardReplacement>
	 */
	function getResult(): array;

	function getTotalCount(): int;
}
