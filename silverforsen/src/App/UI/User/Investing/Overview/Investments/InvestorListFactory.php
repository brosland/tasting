<?php
declare(strict_types=1);

namespace Bon\App\UI\User\Investing\Overview\Investments;

interface InvestorListFactory
{
	function create(): InvestorList;
}