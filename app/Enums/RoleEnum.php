<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum RoleEnum: int
{
    use EnumHelper;

    case ADMIN = 1;
    case PARENT = 2;
    case TEACHER = 3;
    case ACCOUNTANT = 4;
}
