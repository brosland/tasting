<?php

declare(strict_types=1);

namespace App\Standards\Infrastructure\Doctrine\Standard;

use App\Common\Infrastructure\Doctrine\Uuid\IdentifierDbalType;
use App\Standards\Domain\Standard\StandardId;

final class StandardIdDbalType extends IdentifierDbalType
{
	public function getName(): string
	{
		return 'app.standards.standard.id';
	}

	public function getTargetClass(): string
	{
		return StandardId::class;
	}
}
