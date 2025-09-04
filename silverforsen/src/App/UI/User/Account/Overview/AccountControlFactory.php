<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\Users\Services\Identity\IdentityDto;

interface AccountControlFactory
{
	function create(IdentityDto $identity): AccountControl;
}