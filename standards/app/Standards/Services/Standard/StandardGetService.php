<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardDtoFactory;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Domain\Standard\StandardRepository;

final readonly class StandardGetService
{
	public function __construct(
		private StandardDtoFactory $standardDtoFactory,
		private StandardRepository $standardRepository
	) {
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function byCatalogueNumber(int $catalogueNumber): StandardDto
	{
		$standard = $this->standardRepository->getByCatalogueNumber($catalogueNumber);

		return $this->standardDtoFactory->createDto($standard);
	}

	/**
	 * @throws StandardNotFoundException
	 */
	public function byId(StandardId $id): StandardDto
	{
		$standard = $this->standardRepository->getById($id);

		return $this->standardDtoFactory->createDto($standard);
	}
}
