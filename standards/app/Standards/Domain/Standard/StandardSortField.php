<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Sorting;

enum StandardSortField: string
{
	case Code = 'code';
	case Title = 'title';
	case PublicationDate = 'publicationDate';
	case ApprovalDate = 'approvalDate';
	case EffectiveDate = 'effectiveDate';
	case WithdrawalDate = 'withdrawalDate';
	case AnnouncementDate = 'announcementDate';

	/**
	 * @return Sorting<self>
	 */
	public static function createSorting(self $value, bool $ascending = true): Sorting
	{
		return new Sorting($value, $ascending);
	}
}
