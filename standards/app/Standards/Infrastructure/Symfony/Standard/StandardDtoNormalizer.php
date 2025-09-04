<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Symfony\Standard;

use App\Standards\Domain\Standard\StandardDto;
use DateTimeImmutable;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class StandardDtoNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
	use NormalizerAwareTrait;

	/**
	 * @return array<string,bool>
	 */
	public function getSupportedTypes(?string $format): array
	{
		return [StandardDto::class => true];
	}

	/**
	 * @param array<string,mixed> $context
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof StandardDto;
	}

	/**
	 * @param array<string,mixed> $context
	 * @return array<string,mixed>
	 * @throws ExceptionInterface
	 */
	public function normalize(mixed $data, ?string $format = null, array $context = []): array
	{
		/** @var StandardDto $data */
		return [
			'id' => $this->normalizer->normalize($data->id, $format, $context),
			'type' => $this->normalizer->normalize($data->type, $format, $context),
			'catalogueNumber' => $data->catalogueNumber,
			'code' => $data->code,
			'title' => $data->title,
			'description' => $data->description,
			'language' => $this->normalizer->normalize($data->language, $format, $context),
			'publicationDate' => $this->formatDate($data->publicationDate),
			'approvalDate' => $this->formatDate($data->approvalDate),
			'effectiveDate' => $this->formatDate($data->effectiveDate),
			'withdrawalDate' => $this->formatDate($data->withdrawalDate),
			'announcementDate' => $this->formatDate($data->announcementDate),
			'isValid' => $data->isValid,
			'parent' => $this->normalizer->normalize($data->parent, $format, $context),
			'replacedStandards' => $this->normalizer->normalize($data->replacedStandards),
			'replacementStandards' => $this->normalizer->normalize($data->replacementStandards),
			'icsCodes' => $this->normalizer->normalize($data->icsCodes),
		];
	}

	private function formatDate(?DateTimeImmutable $date): ?string
	{
		return $date?->format('Y-m-d');
	}
}
