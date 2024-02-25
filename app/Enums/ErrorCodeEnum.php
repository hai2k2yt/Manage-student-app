<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ErrorCodeEnum: string
{
    use EnumHelper;

    case StudentStore = 'E-001';

    case StudentUpdate = 'E-002';
    case StudentDelete = 'E-003';
}
