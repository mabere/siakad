<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdvisorStudentController extends Controller
{
    public function index() {
        $student = auth()->user();
        
        // Check if student is eligible to submit proposal
        $isEligible = $this->checkEligibility($student);
        
        // Fetch existing proposals
        $proposals = $student->thesisProposals()->latest()->get();
        
        // Fetch research areas
        $researchAreas = ResearchArea::all();
        
        return view('mhs.skripsi.index', [
            'student' => $student,
            'proposals' => $proposals,
            'isEligible' => $isEligible,
            'researchAreas' => $researchAreas
        ]);
    }

    private function checkEligibility($student) {
        // Example eligibility checks
        return $student->total_credits >= 120 && 
                $student->current_semester >= 6;
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'research_area_id' => 'required|exists:research_areas,id',
            'proposed_advisor_id' => 'nullable|exists:lecturers,id',
            'draft_document' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        $proposal = ThesisProposal::create([
            'student_id' => auth()->id(),
            'topic' => $validated['topic'],
            'research_area_id' => $validated['research_area_id'],
            'proposed_advisor_id' => $validated['proposed_advisor_id'] ?? null,
            'status' => 'draft',
            'submitted_at' => now()
        ]);

        // Handle document upload
        if ($request->hasFile('draft_document')) {
            $this->uploadProposalDocument($proposal, $request->file('draft_document'));
        }

        return redirect()->route('student.thesis.proposal.index')
            ->with('success', 'Thesis proposal submitted successfully');
    }

    private function uploadProposalDocument($proposal, $file) {
        $filename = 'proposal_' . $proposal->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('thesis_proposals', $filename, 'public');
        
        $proposal->update([
            'document_path' => $path
        ]);
    }

}