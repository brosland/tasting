<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Standards\Domain\Ics\IcsCode;

final class IcsUpdateRequest
{
	public function __construct(
		public IcsCode $code,
		public string $title,
		public string $description
	) {
	}
}
