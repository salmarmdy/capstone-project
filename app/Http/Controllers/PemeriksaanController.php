<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\ChecklistItem;
use App\Models\Assessment;
use App\Models\AssessmentChecklistResult;
use App\Models\Employee;

class PemeriksaanController extends Controller
{
    // Main index method
    public function index(Request $request)
    {
        $query = Assessment::with(['employee', 'vehicle', 'checklistResults.checklistItem']);
        
        // Apply filters
        if ($request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->date) {
            $query->whereDate('assessment_date', $request->date);
        }
        
        $assessments = $query->paginate(10);
        $vehicles = Vehicle::all();
        $employees = Employee::all();
        
        return view('pages.pemeriksaan.index', compact('assessments', 'vehicles', 'employees'));
    }

    // Detail method for AJAX
    public function detail($id)
    {
        $assessment = Assessment::with(['employee', 'vehicle', 'checklistResultsWithItems'])
            ->findOrFail($id);
        
        $html = view('pages.pemeriksaan.detail', compact('assessment'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'assessment' => $assessment
        ]);
    }

    public function report($id)
    {
        $assessment = Assessment::with([
            'employee', 
            'vehicle', 
            'checklistResults.checklistItem'
        ])->findOrFail($id);
        
        // Check if assessment is approved
        if (!$assessment->approved) {
            abort(403, 'Laporan hanya dapat dicetak untuk pemeriksaan yang sudah disetujui.');
        }
        
        return view('pages.pemeriksaan.report', compact('assessment'));
    }
}
