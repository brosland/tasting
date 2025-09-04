<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

use App\Common\Domain\Exception\Runtime\Database\EntityNotFoundException;

final class IcsNotFoundException extends EntityNotFoundException
{
	public static function notFoundByCode(IcsCode $code): self
	{
		return new self("The ICS not found by the code '$code'.");
	}
}
