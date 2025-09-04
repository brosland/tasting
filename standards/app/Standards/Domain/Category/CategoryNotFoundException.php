<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

use App\Common\Domain\Exception\Runtime\Database\EntityNotFoundException;

final class CategoryNotFoundException extends EntityNotFoundException
{
	public static function notFoundById(CategoryId $id): self
	{
		return new self("The category not found by the ID '{$id->toString()}'.");
	}

	public static function notFoundByExternalId(string $id): self
	{
		return new self("The category not found by the external ID '$id'.");
	}
}
