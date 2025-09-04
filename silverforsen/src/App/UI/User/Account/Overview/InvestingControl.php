<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\App\UI\User\Investing\Investments\InvestmentListFactory;
use Bon\App\UI\User\Investing\Overview\Investments\InvestorList;
use Bon\App\UI\User\Investing\Overview\Investments\InvestorListFactory;
use Bon\Funds\Domain\Fund\FundState;
use Bon\Funds\Services\Account\Summary\GetInvestingAccountSummaryRequest;
use Bon\Funds\Services\Account\Summary\GetInvestingAccountSummaryService;
use Bon\Funds\Services\Investor\FindInvestorsRequest;
use Bon\Users\Services\Identity\IdentityDto;
use Brick\Money\Money;
use Nette\Application\UI\Control;

final class InvestingControl extends Control
{
	use TranslationTrait;

	public function __construct(
		private readonly GetInvestingAccountSummaryService $getInvestingAccountSummaryService,
		private readonly InvestorListFactory $investorListFactory,
		private readonly IdentityDto $identity
	)
	{
	}

	public function render(): void
	{
		$request = new GetInvestingAccountSummaryRequest(
			$this->identity->account->id,
			$this->identity->user->settings->preferredCurrency
		);
		$request->fundState = [FundState::OPEN(), FundState::CLOSED()];

		$investingAccountSummary = $this->getInvestingAccountSummaryService->execute($request);

		$template = $this->getTemplate();
		$template->accountSummary = $investingAccountSummary;
		$template->lastMonthGain = Money::zero($this->identity->user->settings->preferredCurrency);

		$template->setFile(__DIR__ . '/InvestingControl.latte');
		$template->render();
	}

	// factories ***************************************************************

	protected function createComponentInvestments(): InvestorList
	{
		$list = $this->investorListFactory->create();
		$list->getPaginator()->setItemsPerPage(5);
		$list->onRequest[] = function (FindInvestorsRequest $request): void {
			$request->investorAccountId = $this->identity->account->id;
			$request->fundState = [FundState::OPEN(), FundState::CLOSED()];
		};

		return $list;
	}
}