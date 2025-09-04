<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Avatar;

use Bon\Users\Services\Account\AccountDto;

interface AvatarModalFactory
{
	function create(AccountDto $account): AvatarModal;
}