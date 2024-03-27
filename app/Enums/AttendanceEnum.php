<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum AttendanceEnum: int
{
    use EnumHelper;
    case PRESENT = 1;
    case PERMISSION_ABSENCE = 2;
    case UNEXCUSED_ABSENCE = 3;
}
