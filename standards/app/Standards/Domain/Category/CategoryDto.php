<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

final readonly class CategoryDto
{
	/**
	 * @param array<string,mixed> $metadata
	 */
	public function __construct(
		public CategoryId $id,
		public string $title,
		public ?string $externalId,
		public array $metadata,
		public ?CategoryId $parentId,
		public int $treeLevel,
		public int $treeLeft,
		public int $treeRight,
		public int $subItemCount
	) {
	}
}
