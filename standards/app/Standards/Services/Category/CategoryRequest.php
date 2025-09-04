<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

use App\Standards\Domain\Category\CategoryId;

final class CategoryRequest implements CategoryGetRequest
{
	public function __construct(
		public CategoryId $id
	) {
	}
}
