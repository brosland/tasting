<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\App\UI\Common\Navigation\NavigationItem;
use Bon\App\UI\Common\Translation\TranslationTrait;
use Nette\Application\UI\Link;
use Nette\Application\UI\Presenter;

final class OverviewNavigationItem implements NavigationItem
{
	use TranslationTrait;

	const NAME = 'overview';

	public function getName(): string
	{
		return self::NAME;
	}

	public function getLabel(): string
	{
		return self::translationPrefix(self::NAME);
	}

	public function getLink(Presenter $presenter): Link
	{
		return $presenter->lazyLink(':User:Account:Overview:');
	}

	public function equals(NavigationItem $item): bool
	{
		return $this->getName() === $item->getName();
	}
}