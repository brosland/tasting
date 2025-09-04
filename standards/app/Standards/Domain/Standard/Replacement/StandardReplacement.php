<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Replacement;

use App\Common\Domain\Event\EventStorage;
use App\Common\Domain\Event\EventStorageProvider;
use App\Standards\Domain\Standard\Standard;

class StandardReplacement implements EventStorageProvider
{
	use EventStorage;

	protected int $pk;
	private Standard $standard, $replacement;

	public function __construct(
		Standard $standard,
		Standard $replacement
	) {
		$this->standard = $standard;
		$this->replacement = $replacement;
	}

	public function getPk(): int
	{
		return $this->pk;
	}

	public function getStandard(): Standard
	{
		return $this->standard;
	}

	public function getReplacement(): Standard
	{
		return $this->replacement;
	}
}
