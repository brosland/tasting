<?php

declare(strict_types=1);

namespace App\Standards\Api\Controllers\Standards;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use App\Common\Api\Controllers\V1\BaseV1Controller;
use App\Common\Domain\DateTime\DateTimeFactory;
use App\Common\Domain\Language\Language;
use App\Common\Domain\Sorting;
use App\Common\Services\FindResponse;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardSortField;
use App\Standards\Domain\Standard\StandardType;
use App\Standards\Services\Standard\StandardFindRequest;
use App\Standards\Services\Standard\StandardFindService;
use DateTimeImmutable;
use InvalidArgumentException;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/standards")
 * @Apitte\Tag("Standards")
 */
final class StandardFindController extends BaseV1Controller
{
	public function __construct(
		private readonly StandardFindService $standardFindService
	) {
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: List standards.
	 * ")
	 * @Apitte\Path("/")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *  	@Apitte\RequestParameter(name="type", type="string", in="query", required=false),
	 *  	@Apitte\RequestParameter(name="language", type="string", in="query", required=false),
	 *  	@Apitte\RequestParameter(name="publicationStartDate", type="datetime", in="query", required=false),
	 *  	@Apitte\RequestParameter(name="publicationEndDate", type="datetime", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="approvalStartDate", type="datetime", in="query", required=false),
	 *   	@Apitte\RequestParameter(name="approvalEndDate", type="datetime", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="effectiveStartDate", type="datetime", in="query", required=false),
	 *   	@Apitte\RequestParameter(name="effectiveEndDate", type="datetime", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="withdrawalStartDate", type="datetime", in="query", required=false),
	 *   	@Apitte\RequestParameter(name="withdrawalEndDate", type="datetime", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="announcementStartDate", type="datetime", in="query", required=false),
	 *   	@Apitte\RequestParameter(name="announcementEndDate", type="datetime", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="isValid", type="bool", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="ics", type="string", in="query", required=false),
	 *     	@Apitte\RequestParameter(name="searchQuery", type="string", in="query", required=false),
	 *  	@Apitte\RequestParameter(name="sorting", type="string", in="query", required=false),
	 *  	@Apitte\RequestParameter(name="limit", type="int", in="query", required=false, description="Data limit"),
	 *  	@Apitte\RequestParameter(name="offset", type="int", in="query", required=false, description="Data offset")
	 *  })
	 * @return FindResponse<StandardDto>
	 */
	public function handleFind(ApiRequest $request): FindResponse
	{
		try {
			/** @var Sorting<StandardSortField>|null $sorting */
			$sorting = Sorting::tryFrom($request->getParameter('sorting') ?? '', StandardSortField::class);

			$types = array_filter(
				array_map(
					fn($type) => StandardType::tryFrom(trim($type)),
					array_unique(explode(',', $request->getParameter('type') ?? ''))
				)
			);

			$findRequest = new StandardFindRequest(
				sorting: $sorting,
				types: $types,
				language: Language::tryFrom($request->getParameter('language') ?? ''),
				publicationStartDate: $this->valueToDateTime($request->getParameter('publicationStartDate')),
				publicationEndDate: $this->valueToDateTime($request->getParameter('publicationEndDate')),
				approvalStartDate: $this->valueToDateTime($request->getParameter('approvalStartDate')),
				approvalEndDate: $this->valueToDateTime($request->getParameter('approvalEndDate')),
				effectiveStartDate: $this->valueToDateTime($request->getParameter('effectiveStartDate')),
				effectiveEndDate: $this->valueToDateTime($request->getParameter('effectiveEndDate')),
				withdrawalStartDate: $this->valueToDateTime($request->getParameter('withdrawalStartDate')),
				withdrawalEndDate: $this->valueToDateTime($request->getParameter('withdrawalEndDate')),
				announcementStartDate: $this->valueToDateTime($request->getParameter('announcementStartDate')),
				announcementEndDate: $this->valueToDateTime($request->getParameter('announcementEndDate')),
				isValid: $this->valueToBoolean($request->getParameter('isValid')),
				ics: IcsCode::tryFrom($request->getParameter('ics') ?? ''),
				searchQuery: $request->getParameter('searchQuery')
			);
			$findRequest->limit = min((int)$request->getParameter('limit', 20), 50);
			$findRequest->offset = (int)$request->getParameter('offset', 0);

			return $this->standardFindService->execute($findRequest);
		} catch (InvalidArgumentException $e) {
			throw ClientErrorException::create()
				->withMessage('Invalid input: ' . $e->getMessage())
				->withCode(IResponse::S400_BadRequest)
				->withPrevious($e);
		}
	}

	private function valueToDateTime(mixed $value): ?DateTimeImmutable
	{
		if ($value === null) {
			return null;
		}

		return DateTimeFactory::tryFrom($value, '!Y-m-d');
	}

	private function valueToBoolean(mixed $value): ?bool
	{
		if ($value === null) {
			return null;
		}

		return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
	}
}
