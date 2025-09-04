<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Services;

final class UnmsStandardSyncRequest
{
	/** @var callable */
	public $onProgress;

	public function __construct(
		callable $onProgress,
		public bool $skipLoading = false,
		public bool $skipPostProcessing = false
	) {
		$this->onProgress = $onProgress;
	}
}
