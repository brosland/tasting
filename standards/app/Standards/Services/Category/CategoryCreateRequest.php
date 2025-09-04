<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Standards\Domain\Category\CategoryPlacement;

final class CategoryCreateRequest
{
	public function __construct(
		public string $title,
		public CategoryPlacement $placement,
		public ?string $externalId = null
	) {
	}
}
