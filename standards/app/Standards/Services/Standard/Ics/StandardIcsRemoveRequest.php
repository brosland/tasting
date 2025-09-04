<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard\Ics;

use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardId;

final class StandardIcsRemoveRequest
{
	public function __construct(
		public StandardId $standardId,
		public IcsCode $icsCode
	) {
	}
}
