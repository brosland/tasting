<?php
declare(strict_types=1);

namespace App\Standards\Domain\Ics;

final readonly class IcsPlacement
{
	const string POSITION_START = 'start';
	const string POSITION_AFTER = 'after';
	const string POSITION_END = 'end';

	public string $position;
	public ?IcsCode $parentCode, $siblingCode;

	public static function START(?IcsCode $parentCode = null): self
	{
		return new self(self::POSITION_START, $parentCode);
	}

	public static function AFTER(IcsCode $siblingCode): self
	{
		return new self(self::POSITION_AFTER, $siblingCode);
	}

	public static function END(?IcsCode $parentCode = null): self
	{
		return new self(self::POSITION_END, $parentCode);
	}

	public function __construct(string $position, ?IcsCode $parentOrSiblingCode = null)
	{
		$this->position = $position;

		if ($position === self::POSITION_AFTER) {
			$this->parentCode = null;
			$this->siblingCode = $parentOrSiblingCode;
		} else {
			$this->parentCode = $parentOrSiblingCode;
			$this->siblingCode = null;
		}
	}
}
