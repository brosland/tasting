<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

use App\Common\Domain\Sorting;

enum CategorySortField: string
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
