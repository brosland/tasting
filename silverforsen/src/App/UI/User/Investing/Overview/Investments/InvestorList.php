<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Investing\Overview\Investments;

use Bon\App\UI\Common\ListControl\ListControl;
use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\Core\Domain\QueryMode;
use Bon\Funds\Domain\Investor\InvestorSorting;
use Bon\Funds\Services\Investor\FindInvestorsRequest;
use Bon\Funds\Services\Investor\FindInvestorsService;
use Bon\Funds\Services\Investor\InvestorDto;
use Nette\Application\UI\Link;

/**
 * @method void onRequest(FindInvestorsRequest $request)
 */
final class InvestorList extends ListControl
{
	use TranslationTrait;

	/** @var array<callable> */
	public array $onRequest = [];

	public function __construct(
		private FindInvestorsService $findInvestorsService
	) {
		parent::__construct();
	}

	/**
	 * @return array<InvestorDto>
	 */
	protected function requestItems(): array
	{
		$request = new FindInvestorsRequest(QueryMode::USER(), InvestorSorting::CREATED_AT());
		$request->limit = $this->paginator->getItemsPerPage();
		$request->offset = $this->paginator->getOffset();

		$this->onRequest($request);

		$response = $this->findInvestorsService->execute($request);

		$this->paginator->setItemCount($response->getTotalCount());

		return $response->getResults();
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		$template->setFile(__DIR__ . '/InvestorList.latte');
	}

	// factories ***************************************************************

	public function createDetailLink(): Link
	{
		return $this->lazyLink('this#');
	}
}