<?php
declare(strict_types=1);

namespace App\Standards\Services\Ics;

use App\Common\Services\FindResponse;
use App\Standards\Domain\Ics\IcsDto;
use App\Standards\Domain\Ics\IcsDtoFactory;
use App\Standards\Domain\Ics\IcsRepository;

final readonly class IcsFindService
{
	public function __construct(
		private IcsDtoFactory $icsDtoFactory,
		private IcsRepository $icsRepository
	) {
	}

	/**
	 * @return FindResponse<IcsDto>
	 */
	public function execute(IcsFindRequest $request): FindResponse
	{
		$query = $this->icsRepository->createIcsQuery();

		if ($request->sorting !== null) {
			$query->sortBy($request->sorting);
		}

		if ($request->limit > 0) {
			$query->paginate($request->limit, $request->offset);
		}

		if ($request->minLevel !== null) {
			$query->byMinLevel($request->minLevel);
		}

		if ($request->maxLevel !== null) {
			$query->byMaxLevel($request->maxLevel);
		}

		if ($request->parentCode !== null) {
			$query->byParent($request->parentCode);
		}

		if ($request->searchQuery !== null) {
			$query->bySearchQuery($request->searchQuery);
		}

		$results = $request->limit === 0 ? [] :
			$this->icsDtoFactory->createDtoList($query->getResult());

		$totalCount = $request->limit > -1 ? $query->getTotalCount() : null;

		return new FindResponse($results, $totalCount);
	}
}
