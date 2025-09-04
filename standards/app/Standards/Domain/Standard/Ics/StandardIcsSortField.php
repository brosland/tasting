<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard\Ics;

use App\Common\Domain\Sorting;

enum StandardIcsSortField: string
{
	case Ics = 'ics';
	case Standard = 'standard';

	/**
	 * @return Sorting<self>
	 */
	public static function createSorting(self $value, bool $ascending = true): Sorting
	{
		return new Sorting($value, $ascending);
	}
}
