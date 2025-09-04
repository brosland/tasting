<?php

declare(strict_types=1);

namespace App\Standards\Services\Category;

final class CategoryGetByExternalIdRequest implements CategoryGetRequest
{
	public function __construct(
		public string $externalId
	) {
	}
}
