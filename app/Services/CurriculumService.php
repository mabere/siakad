<?php

namespace App\Services;

use App\Models\Curriculum;
use Illuminate\Support\Facades\Log;

class CurriculumService
{
    public function createCurriculum(array $data)
    {
        try {
            $curriculum = Curriculum::create([
                'department_id' => $data['department_id'],
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 'draft',
            ]);

            Log::info('Curriculum created', [
                'curriculum_id' => $curriculum->id,
                'user_id' => auth()->id(),
            ]);

            return $curriculum;
        } catch (\Exception $e) {
            Log::error('Failed to create curriculum', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function updateCurriculum(Curriculum $curriculum, array $data)
    {
        try {
            $curriculum->update([
                'department_id' => $data['department_id'] ?? $curriculum->department_id,
                'academic_year_id' => $data['academic_year_id'] ?? $curriculum->academic_year_id,
                'name' => $data['name'] ?? $curriculum->name,
                'description' => $data['description'] ?? $curriculum->description,
                'status' => $data['status'] ?? $curriculum->status,
            ]);

            Log::info('Curriculum updated', [
                'curriculum_id' => $curriculum->id,
                'user_id' => auth()->id(),
            ]);

            return $curriculum;
        } catch (\Exception $e) {
            Log::error('Failed to update curriculum', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function deleteCurriculum(Curriculum $curriculum)
    {
        try {
            $curriculum->delete();
            Log::info('Curriculum deleted', [
                'curriculum_id' => $curriculum->id,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete curriculum', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }
}
