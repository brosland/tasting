<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use App\Common\Domain\Exception\RuntimeException;
use Throwable;

final class UnmsApiException extends RuntimeException
{
	public static function create(?Throwable $previous = null): self
	{
		return new self('Cannot access UNMS API endpoint.', 0, $previous);
	}
}
