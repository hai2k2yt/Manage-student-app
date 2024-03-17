<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum DayOfWeek: int
{
    use EnumHelper;

    case SUNDAY = 1;
    case MONDAY = 2;
    case TUESDAY = 3;
    case WEDNESDAY = 4;
    case THURSDAY = 5;
    case FRIDAY = 6;
    case SATURDAY = 7;

}
