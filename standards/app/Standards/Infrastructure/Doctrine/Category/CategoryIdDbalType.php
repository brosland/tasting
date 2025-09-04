<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Category;

use App\Common\Infrastructure\Doctrine\Uuid\IdentifierDbalType;
use App\Standards\Domain\Category\CategoryId;

final class CategoryIdDbalType extends IdentifierDbalType
{
	public function getName(): string
	{
		return 'app.standards.category.id';
	}

	public function getTargetClass(): string
	{
		return CategoryId::class;
	}
}
