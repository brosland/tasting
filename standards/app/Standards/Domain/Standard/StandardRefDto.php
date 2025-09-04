<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Language\Language;

final readonly class StandardRefDto
{
	public function __construct(
		public StandardId $id,
		public StandardType $type,
		public int $catalogueNumber,
		public string $code,
		public string $title,
		public ?Language $language
	) {
	}
}
