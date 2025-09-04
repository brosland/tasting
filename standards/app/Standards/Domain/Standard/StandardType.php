<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

enum StandardType: string
{
	case Original = 'original';
	case Revision = 'revision';
}
