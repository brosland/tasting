<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Account\Name;

interface AccountNameFormFactory
{
    function create(): AccountNameForm;
}