<?php

declare(strict_types=1);

namespace App\Standards\Services\Standard;

use App\Common\Services\FindResponse;
use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardDtoFactory;
use App\Standards\Domain\Standard\StandardRepository;

final readonly class StandardFindService
{
	public function __construct(
		private StandardDtoFactory $standardDtoFactory,
		private StandardRepository $standardRepository
	) {
	}

	/**
	 * @return FindResponse<StandardDto>
	 */
	public function execute(StandardFindRequest $request): FindResponse
	{
		$query = $this->standardRepository->createStandardQuery();

		if ($request->sorting !== null) {
			$query->sortBy($request->sorting);
		}

		if (count($request->types) > 0) {
			$query->byType($request->types);
		}

		if ($request->publicationStartDate !== null || $request->publicationEndDate !== null) {
			$query->byPublicationDate($request->publicationStartDate, $request->publicationEndDate);
		}

		if ($request->approvalStartDate !== null || $request->approvalEndDate !== null) {
			$query->byApprovalDate($request->approvalStartDate, $request->approvalEndDate);
		}

		if ($request->effectiveStartDate !== null || $request->effectiveEndDate !== null) {
			$query->byEffectiveDate($request->effectiveStartDate, $request->effectiveEndDate);
		}

		if ($request->withdrawalStartDate !== null || $request->withdrawalEndDate !== null) {
			$query->byWithdrawalDate($request->withdrawalStartDate, $request->withdrawalEndDate);
		}

		if ($request->announcementStartDate !== null || $request->announcementEndDate !== null) {
			$query->byAnnouncementDate($request->announcementStartDate, $request->announcementEndDate);
		}

		if ($request->isValid !== null) {
			$query->byIsValid($request->isValid);
		}

		if ($request->relinkRequired !== null) {
			$query->byPostProcessRequired($request->relinkRequired);
		}

		if ($request->parent !== null) {
			$query->byParent($request->parent);
		}

//		if ($request->replacedStandard !== null) {
//			$query->byReplacedStandard($request->replacedStandard);
//		}
//
//		if ($request->replacementStandard !== null) {
//			$query->byReplacementStandard($request->replacementStandard);
//		}

		if ($request->ics !== null) {
			$query->byIcs($request->ics);
		}

		if ($request->searchQuery !== null) {
			$query->bySearchQuery($request->searchQuery);
		}

		if ($request->limit > 0) {
			$query->paginate($request->limit, $request->offset);
		}

		$results = $request->limit === 0 ? [] :
			$this->standardDtoFactory->createDtoList($query->getResult());

		$totalCount = $request->limit > -1 ? $query->getTotalCount() : null;

		return new FindResponse(
			results: $results,
			limit: $request->limit,
			offset: $request->offset,
			totalCount: $totalCount
		);
	}
}
