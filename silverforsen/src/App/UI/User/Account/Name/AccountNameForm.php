<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Name;

use Bon\Users\Domain\Account\AccountName;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;

final class AccountNameForm extends Form
{
	public function __construct(Translator $translator)
	{
		parent::__construct();

		$this->setTranslator($translator);

		$this->addText('name')
			->setCaption(AccountNameModal::translationPrefix('name'))
			->addRule(self::MIN_LENGTH, null, AccountName::MIN_LENGTH)
			->addRule(self::MAX_LENGTH, null, AccountName::MAX_LENGTH)
			->setRequired();

		$this->addSubmit('confirm')
			->setCaption(AccountNameModal::translationPrefix('confirm'));
	}
}