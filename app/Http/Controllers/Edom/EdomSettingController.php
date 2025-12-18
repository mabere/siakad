<?php

namespace App\Http\Controllers\Edom;

use App\Models\Setting;
// use App\Models\EdomSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EdomSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getEdomSettings();
        $academicYear = AcademicYear::where('status', 1)->first();
        return view('admin.edom.settings.index', compact('settings', 'academicYear'));
    }
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'min_respondents' => 'required|integer|min:1',
                'submission_deadline' => 'required|date_format:Y-m-d',
            ]);

            Setting::set('min_respondents', $validated['min_respondents']);
            Setting::set('submission_deadline', $validated['submission_deadline']);
            Setting::clearCache();

            DB::commit();

            return redirect()
                ->route('admin.edom.settings.index')
                ->with('success', 'Pengaturan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving EDOM settings: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleEdomx(Request $request)
    {
        try {
            DB::beginTransaction();

            $isActive = $request->input('is_active', 0) ? true : false;
            Setting::set('edom_active', $isActive);
            Setting::clearCache();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status EDOM berhasil diubah',
                'is_active' => $isActive
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling EDOM status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 422);
        }
    }

    public function toggleEdom(Request $request)
    {
        try {
            DB::beginTransaction();

            $isActive = $request->input('is_active', 0) ? true : false;
            Setting::set('edom_active', $isActive);
            Setting::clearCache();

            DB::commit();

            return redirect()
                ->route('admin.edom.settings.index')
                ->with('success', 'Status EDOM berhasil diubah menjadi ' . ($isActive ? 'Aktif' : 'Nonaktif'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling EDOM status: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
