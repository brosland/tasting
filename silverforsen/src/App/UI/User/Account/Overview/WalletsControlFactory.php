<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\Users\Services\Identity\IdentityDto;

interface WalletsControlFactory
{
	function create(IdentityDto $identity): WalletsControl;
}