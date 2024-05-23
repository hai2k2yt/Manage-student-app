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
    case ClubMe = 'E-023';
    case ClubShow = 'E-024';
    case ClubGetStudents = 'E-025';

    case ClubEnrollmentStore = 'E-030';
    case ClubEnrollmentUpdate = 'E-031';
    case ClubEnrollmentCancel = 'E-032';

    case ClubEnrollmentDelete = 'E-033';
    case ClubEnrollmentAssignStudent = 'E-034';

    case ClubScheduleStore = 'E-040';
    case ClubScheduleShow = 'E-041';
    case ClubScheduleUpdate = 'E-042';
    case ClubScheduleDelete = 'E-043';

    case ClubSessionStore = 'E-050';
    case ClubSessionShow = 'E-051';
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
    case UserRegister = 'E-120';
    case UserShow = 'E-121';
    case UserUpdate = 'E-122';
    case UserDelete = 'E-123';

    case AuthUpdateProfile = 'E-130';


    case StatisticStudentFee = 'E-140';
    case StatisticTeacherFee = 'E-141';

}
