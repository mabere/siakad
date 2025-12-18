<?php

use App\Models\AcademicYear;

if (!function_exists('getCurrentAcademicYear')) {
    function getCurrentAcademicYear()
    {
        return AcademicYear::where('status', 1)->first();
    }
}
