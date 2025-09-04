<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

/**
 * @template T
 */
final readonly class UnmsResponseDto
{
	/**
	 * @param array<string,mixed> $data
	 * @return self<T>
	 */
	public static function from(array $data, callable $dataConverter): self
	{
		return new self(
			page: $data['pagination']['page'] ?? 1,
			pageSize: $data['pagination']['pageSize'] ?? 20,
			dataCount: $data['pagination']['dataCount'] ?? 0,
			pagesCount: $data['pagination']['pagesCount'] ?? 1,
			pageDataCount: $data['pagination']['pageDataCount'] ?? 0,
			data: array_map($dataConverter, $data['data'] ?? [])
		);
	}

	/**
	 * @param array<T> $data
	 */
	private function __construct(
		public int $page,
		public int $pageSize,
		public int $dataCount,
		public int $pagesCount,
		public int $pageDataCount,
		public array $data
	) {
	}
}
