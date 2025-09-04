<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Replacement;

interface StandardReplacementRepository
{
	function createStandardReplacementQuery(): StandardReplacementQuery;

	function add(StandardReplacement $standardReplacement): void;

	function remove(StandardReplacement $standardReplacement): void;

	function storeChanges(): void;
}
