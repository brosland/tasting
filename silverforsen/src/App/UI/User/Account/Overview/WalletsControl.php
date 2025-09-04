<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\App\UI\User\Wallets\WalletList;
use Bon\App\UI\User\Wallets\WalletListFactory;
use Bon\Users\Services\Identity\IdentityDto;
use Bon\Wallets\Services\Wallet\FindWalletsRequest;
use Nette\Application\UI\Control;

final class WalletsControl extends Control
{
	use TranslationTrait;

	public function __construct(
		private WalletListFactory $walletListFactory,
		private IdentityDto $identity
	) {
	}

	public function render(): void
	{
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . '/WalletsControl.latte');
		$template->render();
	}

	// factories ***************************************************************

	protected function createComponentWallets(): WalletList
	{
		$list = $this->walletListFactory->create();
		$list->onRequest[] = function (FindWalletsRequest $request): void {
			$request->ownerId = $this->identity->account->id;
		};

		return $list;
	}
}