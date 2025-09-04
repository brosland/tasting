<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Ics;

use App\Standards\Domain\Ics\IcsCode;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DbalType;
use InvalidArgumentException;

final class IcsCodeDbalType extends DbalType
{
	public function getName(): string
	{
		return 'app.standards.ics.code';
	}

	/**
	 * @param array<string,mixed> $column
	 */
	public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
	{
		$column['length'] = 16;

		return $platform->getStringTypeDeclarationSQL($column);
	}

	public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?IcsCode
	{
		if ($value === null || $value instanceof IcsCode) {
			return $value;
		} elseif (is_string($value)) {
			return IcsCode::from($value);
		}

		throw new InvalidArgumentException('Invalid value.');
	}

	public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
	{
		if ($value === null || is_string($value)) {
			return $value;
		} elseif ($value instanceof IcsCode) {
			return $value->toString();
		}

		throw new InvalidArgumentException('Invalid value.');
	}
}
