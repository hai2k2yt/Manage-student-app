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

    case AbsenceReportStore = 'E-060';
    case AbsenceReportUpdate = 'E-061';
    case AbsenceReportDelete = 'E-062';

    case AttendanceStore = 'E-070';
    case AttendanceUpdate = 'E-071';
    case AttendanceDelete = 'E-072';

    case ClubSessionPhotoStore = 'E-080';
    case ClubSessionPhotoUpdate = 'E-081';
    case ClubSessionPhotoDelete = 'E-082';

    case CommentStore = 'E-090';
    case CommentUpdate = 'E-091';
    case CommentDelete = 'E-092';
}
