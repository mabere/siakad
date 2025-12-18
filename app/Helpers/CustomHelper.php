<?php

use Carbon\Carbon;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

if (!function_exists('weekOfSemester')) {
    function weekOfSemester()
    {
        $academicYear = AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return null;
        }
        $startOfSemester = Carbon::parse($academicYear->start_date);
        $now = Carbon::now();
        if ($now->lessThan($startOfSemester)) {
            return 1;
        }
        $diffInWeeks = $now->diffInWeeks($startOfSemester);
        return max(1, $diffInWeeks + 1);
    }
}


if (!function_exists('getProgressColor')) {
    function getProgressColor($avg)
    {
        if ($avg >= 4)
            return 'success';
        if ($avg >= 3)
            return 'warning';
        return 'danger';
    }
}


if (!function_exists('get_creator_role')) {
    function get_creator_role($user)
    {
        if (!$user)
            return 'Unknown';

        if ($user->hasRole('admin'))
            return 'Admin';
        if (Faculty::where('dekan_user_id', $user->id)->exists())
            return 'Dekan';
        if ($user->lecturer && Department::where('head_id', $user->lecturer->id)->exists())
            return 'Kaprodi';

        return 'Unknown';
    }
}
if (!function_exists('getDepartmentName')) {
    function getDepartmentName($id)
    {
        return Department::find($id)->name ?? 'Program Studi Tidak Diketahui';
    }
}

if (!function_exists('active_role')) {
    function active_role()
    {
        return Auth::user()?->active_role ?? Auth::user()?->getRoleNames()->first();
    }
}
