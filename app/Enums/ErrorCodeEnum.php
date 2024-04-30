<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ErrorCodeEnum: string
{
    use EnumHelper;

    case StudentStore = 'E-001';
    case StudentUpdate = 'E-002';
    case StudentDelete = 'E-003';
    case StudentGetParentCode = 'E-004';


    case ClassStore = 'E-010';
    case ClassUpdate = 'E-011';
    case ClassDelete = 'E-012';
    case ClassAssignStudent = 'E-013';

    case ClubStore = 'E-020';
    case ClubUpdate = 'E-021';
    case ClubDelete = 'E-022';

    case ClubEnrollmentStore = 'E-030';
    case ClubEnrollmentUpdate = 'E-031';
    case ClubEnrollmentCancel = 'E-032';

    case ClubEnrollmentDelete = 'E-033';
    case ClubEnrollmentAssignStudent = 'E-034';

    case ClubScheduleStore = 'E-040';
    case ClubScheduleUpdate = 'E-041';
    case ClubScheduleDelete = 'E-042';

    case ClubSessionStore = 'E-050';
    case ClubShow = 'E-051';
    case ClubGetStudents = 'E-052';
    case ClubSessionUpdate = 'E-053';
    case ClubSessionDelete = 'E-054';

    case AbsenceReportStore = 'E-060';
    case AbsenceReportUpdate = 'E-061';
    case AbsenceReportDelete = 'E-062';

    case AttendanceStore = 'E-070';
    case AttendanceUpdate = 'E-071';
    case AttendanceDelete = 'E-072';
    case AttendanceUpdateMany = 'E-073';
    case AttendanceStatisticStudent = 'E-074';

    case ClubSessionPhotoStore = 'E-080';
    case ClubSessionPhotoUpdate = 'E-081';
    case ClubSessionPhotoDelete = 'E-082';
    case ClubSessionPhotoByClub = 'E-083';
    case ClubSessionPhotoBySession = 'E-084';

    case CommentStore = 'E-090';
    case CommentShow = 'E-091';
    case CommentUpdate = 'E-092';
    case CommentDelete = 'E-093';

    case NotificationStore = 'E-100';
    case NotificationShow = 'E-101';
    case NotificationUpdate = 'E-102';
    case NotificationDelete = 'E-103';


    case TeacherShow = 'E-110';
}
