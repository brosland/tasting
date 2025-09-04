<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard;

use App\Common\Infrastructure\Doctrine\Enum\EnumDbalType;
use App\Standards\Domain\Standard\StandardType;

final class StandardTypeDbalType extends EnumDbalType
{
	public function getName(): string
	{
		return 'app.standards.standard.type';
	}

	public function getTargetClass(): string
	{
		return StandardType::class;
	}
}
