<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ClubEnrollmentStatusEnum: int
{
    use EnumHelper;

    case STUDY = 1;
    case ABSENCE = 2;
}
