<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

final readonly class IcsDto
{
	public function __construct(
		public IcsCode $code,
		public string $title,
		public string $description,
		public ?IcsCode $parentCode,
		public int $treeLevel,
		public int $treeLeft,
		public int $treeRight,
		public int $subItemCount
	) {
	}
}
