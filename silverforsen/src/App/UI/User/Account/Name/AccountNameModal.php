<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Name;

use Bon\App\UI\Common\RefreshTrait;
use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\Users\Domain\Account\AccountName;
use Bon\Users\Domain\Account\AccountNameAlreadyUsedException;
use Bon\Users\Domain\Account\AccountNotFoundException;
use Bon\Users\Services\Account\AccountDto;
use Bon\Users\Services\Account\Name\ChangeAccountNameRequest;
use Bon\Users\Services\Account\Name\ChangeAccountNameService;
use Brosland\Modals\UI\Modal;
use InvalidArgumentException;
use Nette\Application\BadRequestException;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;

/**
 * @method void onChange(AccountDto $account)
 */
final class AccountNameModal extends Modal
{
	use RefreshTrait;
	use TranslationTrait;

	/** @var array<callable> */
	public array $onChange = [];

	public function __construct(
		private ChangeAccountNameService $changeAccountNameService,
		private AccountNameFormFactory $accountNameFormFactory,
		private AccountDto $account
	) {
		parent::__construct();
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		$template->setFile(__DIR__ . '/AccountNameModal.latte');
	}

	// factories ***************************************************************

	protected function createComponentForm(): AccountNameForm
	{
		$form = $this->accountNameFormFactory->create();
		$form->onSubmit[] = fn() => $this->refresh();
		$form->onSuccess[] = [$this, 'processForm'];
		$form->setDefaults(['name' => $this->account->name]);

		return $form;
	}

	// events ******************************************************************

	/**
	 * @throws BadRequestException
	 */
	public function processForm(AccountNameForm $form, ArrayHash $values): void
	{
		/** @var TextInput $nameInput */
		$nameInput = $form['name'];

		try {
			$accountName = AccountName::fromString($values['name']);
			$request = new ChangeAccountNameRequest($this->account->id, $accountName);
			$this->account = $this->changeAccountNameService->execute($request);

			$this->onChange($this->account);
		} catch(InvalidArgumentException) {
			$nameInput->addError(self::translationPrefix('invalidName'));
		} catch (AccountNotFoundException) {
			throw new BadRequestException();
		} catch (AccountNameAlreadyUsedException) {
			$nameInput->addError(self::translationPrefix('nameAlreadyUsed'));
		}
	}
}