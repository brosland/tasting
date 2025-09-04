<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

use App\Common\Domain\Sorting;

enum IcsSortField: string
{
	case Title = 'title';
	case Tree = 'tree';

	/**
	 * @return Sorting<self>
	 */
	public static function createSorting(self $value, bool $ascending = true): Sorting
	{
		return new Sorting($value, $ascending);
	}
}
