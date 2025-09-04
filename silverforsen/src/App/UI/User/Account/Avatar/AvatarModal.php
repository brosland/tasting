<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Avatar;

use Bon\App\UI\Common\FileUploader\FileUploader;
use Bon\App\UI\Common\FileUploader\FileUploaderFactory;
use Bon\App\UI\Common\RefreshTrait;
use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\Media\Domain\FileUpload\FileUploadId;
use Bon\Media\Domain\FileUpload\FileUploadNotFoundException;
use Bon\Media\Services\FileUpload\FileUploadDto;
use Bon\Users\Domain\Account\AccountNotFoundException;
use Bon\Users\Services\Account\AccountDto;
use Bon\Users\Services\Account\Avatar\SetAvatarRequest;
use Bon\Users\Services\Account\Avatar\SetAvatarService;
use Brosland\Modals\UI\Modal;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;

/**
 * @method void onChange()
 */
final class AvatarModal extends Modal
{
	use RefreshTrait;
	use TranslationTrait;

	/** @var array<callable> */
	public array $onChange = [];

	public function __construct(
		private SetAvatarService $setAvatarService,
		private FileUploaderFactory $fileUploaderFactory,
		private AccountDto $account
	) {
		parent::__construct();
	}

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	public function handleRemove(): void
	{
		if ($this->account->avatar === null) {
			throw new BadRequestException();
		}

		$this->setAvatar(null);
		$this->refresh();
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		$template->account = $this->account;

		$template->setFile(__DIR__ . '/AvatarModal.latte');
	}

	/**
	 * @throws BadRequestException
	 */
	private function setAvatar(?FileUploadId $fileUploadId): void
	{
		try {
			$request = new SetAvatarRequest($this->account->id, $fileUploadId);
			$this->account->avatar = $this->setAvatarService->execute($request);

			$this->onChange();
		} catch (AccountNotFoundException|FileUploadNotFoundException) {
			throw new BadRequestException();
		}
	}

	// factories ***************************************************************

	protected function createComponentUploader(): FileUploader
	{
		$fileTypes = [
			'Images' => ['image/jpg' => 'jpg', 'image/jpeg' => 'jpeg', 'image/png' => 'png']
		];

		$control = $this->fileUploaderFactory->create();
		$control->setMaxFileSize(1024 * 1024 * 5);
		$control->setMimeTypes($fileTypes);
		$control->onUpload[] = function (FileUploadDto $fileUpload): void {
			$this->setAvatar($fileUpload->id);
			$this->refresh();
		};

		return $control;
	}
}