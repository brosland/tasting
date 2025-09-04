<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Ics;

use App\Common\Domain\Event\EventStorage;
use App\Common\Domain\Event\EventStorageProvider;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Standard;

class StandardIcs implements EventStorageProvider
{
	use EventStorage;

	protected int $pk;
	private Standard $standard;
	private IcsCode $icsCode;

	public function __construct(Standard $standard, IcsCode $icsCode)
	{
		$this->standard = $standard;
		$this->icsCode = $icsCode;
	}

	public function getStandard(): Standard
	{
		return $this->standard;
	}

	public function getIcsCode(): IcsCode
	{
		return $this->icsCode;
	}
}
