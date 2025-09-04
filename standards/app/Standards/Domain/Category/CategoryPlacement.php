<?php
declare(strict_types=1);

namespace App\Standards\Domain\Category;

final readonly class CategoryPlacement
{
	const string POSITION_START = 'start';
	const string POSITION_AFTER = 'after';
	const string POSITION_END = 'end';

	public string $position;
	public ?CategoryId $parentId, $siblingId;

	public static function START(?CategoryId $parentId = null): self
	{
		return new self(self::POSITION_START, $parentId);
	}

	public static function AFTER(CategoryId $siblingId): self
	{
		return new self(self::POSITION_AFTER, $siblingId);
	}

	public static function END(?CategoryId $parentId = null): self
	{
		return new self(self::POSITION_END, $parentId);
	}

	public function __construct(string $position, ?CategoryId $parentOrSiblingId = null)
	{
		$this->position = $position;

		if ($position === self::POSITION_AFTER) {
			$this->parentId = null;
			$this->siblingId = $parentOrSiblingId;
		} else {
			$this->parentId = $parentOrSiblingId;
			$this->siblingId = null;
		}
	}
}
