<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Symfony\Ics;

use App\Standards\Domain\Ics\IcsCode;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class IcsCodeNormalizer implements NormalizerInterface, DenormalizerInterface
{
	/**
	 * @return array<string,bool>
	 */
	public function getSupportedTypes(?string $format): array
	{
		return [IcsCode::class => true];
	}

	/**
	 * @param array<string,mixed> $context
	 */
	public function supportsDenormalization(
		mixed $data,
		string $type,
		?string $format = null,
		array $context = []
	): bool {
		return $type === IcsCode::class;
	}

	/**
	 * @param array<string,mixed> $context
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof IcsCode;
	}

	/**
	 * @param array<string,mixed> $context
	 */
	public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): IcsCode
	{
		if (!is_a($type, IcsCode::class, true)) {
			throw new InvalidArgumentException('Invalid class type.');
		}

		return IcsCode::from($data);
	}

	/**
	 * @param array<string,mixed> $context
	 */
	public function normalize(mixed $data, ?string $format = null, array $context = []): string
	{
		/** @var IcsCode $data */
		return $data->toString();
	}
}
