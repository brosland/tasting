<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

interface IcsRepository
{
	/**
	 * @throws IcsNotFoundException
	 */
	function getByCode(IcsCode $code): Ics;

	/**
	 * @return array<Ics>
	 * @throws IcsNotFoundException
	 */
	public function findTreePath(IcsCode $icsCode): array;

	function createIcsQuery(): IcsQuery;

	function add(Ics $ics, ?IcsPlacement $placement = null): void;

	function move(Ics $ics, IcsPlacement $placement): void;

	function remove(Ics $ics): void;

	function storeChanges(): void;
}
