<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\App\UI\Common\Translation\TranslationTrait;
use Bon\Memberships\Domain\Membership\MembershipNotFoundException;
use Bon\Memberships\Services\Membership\GetMembershipByAccountRequest;
use Bon\Memberships\Services\Membership\GetMembershipService;
use Bon\Users\Services\Identity\IdentityDto;
use Bon\Verifications\Domain\Verification\VerificationSorting;
use Bon\Verifications\Services\Verification\FindVerificationsRequest;
use Bon\Verifications\Services\Verification\FindVerificationsService;
use Nette\Application\UI\Control;

final class AccountControl extends Control
{
	use TranslationTrait;

	public function __construct(
		private FindVerificationsService $findVerificationsService,
		private GetMembershipService $getMembershipService,
		private IdentityDto $identity
	) {
	}

	public function render(): void
	{
		try {
			$membershipRequest = new GetMembershipByAccountRequest($this->identity->account->id);
			$membership = $this->getMembershipService->execute($membershipRequest);
		} catch (MembershipNotFoundException) {
			$membership = null;
		}

		$verificationRequest = new FindVerificationsRequest(VerificationSorting::CREATED_AT());
		$verificationRequest->subjectId = $this->identity->account->owner->id;
		$verificationRequest->limit = 1;

		$verification = $this->findVerificationsService
			->execute($verificationRequest)
			->getSingleOrNullResult();

		$template = $this->getTemplate();
		$template->identity = $this->identity;
		$template->membership = $membership;
		$template->verification = $verification;

		$template->setFile(__DIR__ . '/AccountControl.latte');
		$template->render();
	}
}