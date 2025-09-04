<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Standards\Domain\Ics\IcsCode;

final class IcsCreateRequest
{
	public function __construct(
		public IcsCode $code,
		public string $title,
		public string $description
	) {
	}
}
