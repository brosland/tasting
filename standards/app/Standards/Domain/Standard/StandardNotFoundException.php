<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Exception\Runtime\Database\EntityNotFoundException;

final class StandardNotFoundException extends EntityNotFoundException
{
	public static function notFoundById(StandardId $id): self
	{
		return new self("The standard not found by the ID '{$id->toString()}'.");
	}

	public static function notFoundByCatalogueNumber(int $catalogueNumber): self
	{
		return new self("The standard not found by the catalogue number '$catalogueNumber'.");
	}
}
