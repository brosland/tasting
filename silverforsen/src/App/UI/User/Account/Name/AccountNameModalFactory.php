<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Name;

use Bon\Users\Services\Account\AccountDto;

interface AccountNameModalFactory
{
    function create(AccountDto $account): AccountNameModal;
}