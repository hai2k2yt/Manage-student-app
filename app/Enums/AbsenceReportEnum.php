<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum AbsenceReportEnum: int
{
    use EnumHelper;

    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
}
