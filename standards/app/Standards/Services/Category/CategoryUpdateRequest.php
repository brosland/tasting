<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Standards\Domain\Category\CategoryId;
use App\Standards\Domain\Category\CategoryPlacement;

final class CategoryUpdateRequest
{
	public function __construct(
		public CategoryId $id,
		public string $title,
		public CategoryPlacement $placement,
		public ?string $externalId = null
	) {
	}
}
