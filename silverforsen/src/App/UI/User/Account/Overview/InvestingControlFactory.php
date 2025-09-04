<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Overview;

use Bon\Users\Services\Identity\IdentityDto;

interface InvestingControlFactory
{
	function create(IdentityDto $identity): InvestingControl;
}