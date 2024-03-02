<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ErrorCodeEnum: string
{
    use EnumHelper;

    case StudentStore = 'E-001';
    case StudentUpdate = 'E-002';
    case StudentDelete = 'E-003';

    case StudentClassStore = 'E-010';
    case StudentClassUpdate = 'E-011';
    case StudentClassDelete = 'E-012';

    case ClubStore = 'E-020';
    case ClubUpdate = 'E-021';
    case ClubDelete = 'E-022';

    case ClubEnrollmentStore = 'E-030';
    case ClubEnrollmentUpdate = 'E-031';
    case ClubEnrollmentDelete = 'E-032';

    case ClubScheduleStore = 'E-040';
    case ClubScheduleUpdate = 'E-041';
    case ClubScheduleDelete = 'E-042';

    case ClubSessionStore = 'E-050';
    case ClubSessionUpdate = 'E-051';
    case ClubSessionDelete = 'E-052';
}
