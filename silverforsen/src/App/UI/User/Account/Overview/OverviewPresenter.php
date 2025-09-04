<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\App\UI\User\Account\AccountUserNavigationItem;
use Bon\App\UI\User\Investing\Overview\Investments\InvestorList;
use Bon\App\UI\User\Investing\Overview\Investments\InvestorListFactory;
use Bon\App\UI\User\UserPresenter;
use Bon\Funds\Services\Investor\FindInvestorsRequest;
use Bon\Users\Services\Account\Value\GetAccountValueRequest;
use Bon\Users\Services\Account\Value\GetAccountValueService;
use Brick\Money\Money;

final class OverviewPresenter extends UserPresenter
{
	use TranslationTrait;

	public function __construct(
		private GetAccountValueService $getAccountValueService,
		private AccountControlFactory $accountControlFactory,
		private InvestingControlFactory $investingControlFactory,
		private WalletsControlFactory $walletsControlFactory
	) {
		parent::__construct();
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->getNavigation()->setActiveItem(
			AccountUserNavigationItem::NAME,
			OverviewNavigationItem::NAME
		);

		$preferredCurrency = $this->identity->user->settings->preferredCurrency;

		$accountValueRequest = new GetAccountValueRequest(
			$this->identity->account->id,
			$preferredCurrency
		);

		$accountValue = $this->getAccountValueService->execute($accountValueRequest);

		$template = $this->getTemplate();
		$template->accountValue = $accountValue;
		$template->accountValueDelta = Money::zero($preferredCurrency);
	}

	// factories ***************************************************************

	protected function createComponentInvesting(): InvestingControl
	{
		return $this->investingControlFactory->create($this->identity);
	}

	protected function createComponentAccount(): AccountControl
	{
		return $this->accountControlFactory->create($this->identity);
	}

	protected function createComponentWallets(): WalletsControl
	{
		return $this->walletsControlFactory->create($this->identity);
	}
}