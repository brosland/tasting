<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

use InvalidArgumentException;
use Stringable;

final readonly class IcsCode implements Stringable
{
	private const string PATTERN = '/^\d{2}(?:\.\d{3}(?:\.\d{2})?)?$/';

	/**
	 * @throws InvalidArgumentException
	 */
	public static function from(string $value): self
	{
		$value = trim($value);

		if (preg_match(self::PATTERN, $value) !== 1) {
			throw new InvalidArgumentException(
				sprintf('Invalid ICS code: "%s". Expected formats are "NN", "NN.NNN" or "NN.NNN.NN".', $value)
			);
		}

		return new self($value);
	}

	public static function tryFrom(string $value): ?self
	{
		try {
			return self::from($value);
		} catch (InvalidArgumentException) {
			return null;
		}
	}

	private function __construct(
		private string $value
	) {
	}

	public function getLevel(): int
	{
		return count(explode('.', $this->value));
	}

	public function getParentCode(): ?self
	{
		$level = $this->getLevel();

		if ($level === 1) {
			return null;
		}

		$parts = explode('.', $this->value);

		return self::from(implode('.', array_slice($parts, 0, $level - 1)));
	}

	public function equals(self $other): bool
	{
		return $this->value === $other->value;
	}

	public function toString(): string
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->toString();
	}
}
