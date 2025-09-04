<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Services;

final readonly class UnmsStandardImportRequest
{
	public function __construct(
		public int $catalogueNumber
	) {
	}
}
