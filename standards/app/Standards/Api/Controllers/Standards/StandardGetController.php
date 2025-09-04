<?php

declare(strict_types=1);

namespace App\Standards\Api\Controllers\Standards;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use App\Common\Api\Controllers\V1\BaseV1Controller;
use App\Standards\Domain\Standard\StandardDto;
use App\Standards\Domain\Standard\StandardId;
use App\Standards\Domain\Standard\StandardNotFoundException;
use App\Standards\Services\Standard\StandardGetService;
use InvalidArgumentException;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/standards")
 * @Apitte\Tag("Standards")
 */
final class StandardGetController extends BaseV1Controller
{
	public function __construct(
		private readonly StandardGetService $standardGetService
	) {
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Get standard by catalogue number.
	 * ")
	 * @Apitte\Path("/cn/{cn}")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 * 		@Apitte\RequestParameter(name="cn", in="path", type="int", description="Catalogue number")
	 * })
	 */
	public function handleGetByCatalogueNumber(ApiRequest $request): StandardDto
	{
		try {
			return $this->standardGetService->byCatalogueNumber((int)$request->getParameter('cn'));
		} catch (StandardNotFoundException) {
			throw ClientErrorException::create()
				->withMessage('Standard not found')
				->withCode(IResponse::S404_NotFound);
		}
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Get standard by id.
	 * ")
	 * @Apitte\Path("/{id}")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *  	@Apitte\RequestParameter(name="id", in="path", type="string", description="Standard ID")
	 * })
	 */
	public function handleGetById(ApiRequest $request): StandardDto
	{
		try {
			$standardId = StandardId::fromString($request->getParameter('id'));

			return $this->standardGetService->byId($standardId);
		} catch (StandardNotFoundException|InvalidArgumentException) {
			throw ClientErrorException::create()
				->withMessage('Standard not found')
				->withCode(IResponse::S404_NotFound);
		}
	}
}
