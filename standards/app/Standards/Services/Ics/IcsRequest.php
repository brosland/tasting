<?php

declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Standards\Domain\Ics\IcsCode;

final class IcsRequest
{
	public function __construct(
		public IcsCode $code
	) {
	}
}
