<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

interface StandardRepository
{
	/**
	 * @throws StandardNotFoundException
	 */
	function getByCatalogueNumber(int $catalogueNumber): Standard;

	/**
	 * @throws StandardNotFoundException
	 */
	function getById(StandardId $id): Standard;

	function createStandardQuery(): StandardQuery;

	function add(Standard $standard): void;

	function remove(Standard $standard): void;

	function storeChanges(): void;
}
