<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ChecklistItem;
use App\Models\Assessment;
use App\Models\AssessmentChecklistResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = session('employee_id');
        $query = Vehicle::query()->where('employee_id', $employeeId);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('created_at', 'desc')->get();

        // Get upcoming expirations
        // $expiringLicenses = DrivingLicense::expiringSoon(30)->get();
        // $expiringVehicles = Vehicle::expiringLicense(30)->get();

        // Statistics
        $stats = [
            'total' => Vehicle::count(),
            // 'active' => Vehicle::where('status', 'active')->count(),
            // 'assessment_due' => Vehicle::assessmentDue()->count(),
            // 'expiring_licenses' => $expiringVehicles->count() + $expiringLicenses->count()
        ];

        return view('pages.employee.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('pages.employee.vehicles.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'employee_id' => 'required|exists:employees,id', // Hapus validasi ini karena kita hardcode
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'vehicle_type' => 'required|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'engine_capacity' => 'nullable|integer',
            'license_expiry' => 'nullable|date',
            'license_document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $employeeId = session('employee_id');

        // Hardcode employee_id = 11
        $data['employee_id'] = $employeeId;
        $data['engine_capacity'] = 100;
        // Handle file upload
        if ($request->hasFile('license_document_path')) {
            $file = $request->file('license_document_path');
            $path = $file->store('documents/vehicles', 'public');
            $data['license_document_path'] = $path;
        }

        Vehicle::create($data);

        return redirect()->route('pages.employee.vehicles.index')
                        ->with('success', 'Kendaraan berhasil didaftarkan!');
    }

    public function show(Vehicle $vehicle)
    {
        return view('pages.employee.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('pages.employee.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'type' => 'required|string|max:255',
            'engine_capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,maintenance',
            'assessment_status' => 'required|in:approved,pending,due,expired',
            'license_expiry' => 'required|date',
            'assessment_due' => 'nullable|date'
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
                        ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
                        ->with('success', 'Vehicle deleted successfully!');
    }

    public function assess(Request $request, $id)
    {
        $vehicles = Vehicle::where('id', $id)->get();
        $checklistItems = ChecklistItem::orderBy('created_at', 'asc')->get();

        // return view('pages.employee.vehicles.assessment');
        return view('pages.employee.vehicles.assessment', compact('vehicles', 'checklistItems'));

    }

    public function assessment(Vehicle $vehicle)
    {
        // Update assessment status
        $vehicle->update([
            'assessment_status' => 'approved',
            'assessment_due' => Carbon::now()->addYear()
        ]);

        return redirect()->route('vehicles.index')
                        ->with('success', 'Vehicle assessment completed!');
    }

    public function assessStore(Request $request)
    {
        // Validasi input
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'assessment_date' => 'required|date',
            'employee_id' => 'nullable|exists:employees,id',
            'approved' => 'required|boolean',
            'status_name' => 'required|string|max:255',
            'status_description' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'checklist_results' => 'required|array',
            'checklist_results.*.item_id' => 'required|exists:checklist_items,id',
            'checklist_results.*.passed' => 'nullable|boolean',
        ]);

        try {
            $employeeId = session('employee_id');

            // Simpan assessment utama
            $assessmentId = Assessment::insertGetId([
                'vehicle_id' => $request->vehicle_id,
                'employee_id' => $employeeId,
                'assessment_date' => $request->assessment_date,
                'approved' => $request->approved,
                'status_name' => $request->status_name,
                'status_description' => $request->status_description,
                'comments' => $request->comments,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Simpan checklist results
            $checklistData = [];
            foreach ($request->checklist_results as $result) {
                $checklistData[] = [
                    'assessment_id' => $assessmentId,
                    'checklist_items_id' => $result['item_id'],
                    'passed' => isset($result['passed']) ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insert batch checklist results
            AssessmentChecklistResult::insert($checklistData);

            return redirect()->route('pages.employee.vehicles.index')
                ->with('success', 'Penilaian kendaraan berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan penilaian: ' . $e->getMessage());
        }
    }

    public function editAssessment($id)
    {
        // Get the assessment with related data
        $assessment = Assessment::with(['vehicle', 'employee.user'])
            ->where('vehicle_id', $id)
            ->first();
       
        // Get all checklist items
        $checklistItems = ChecklistItem::orderBy('created_at', 'asc')->get();
        
        $checklistResults = AssessmentChecklistResult::where('assessment_id', optional($assessment)->id)->first();

        $employeeId = session('employee_id');

        $userName = session('username');

        $vehicles = Vehicle::where('employee_id',$employeeId)->get();

        return view('pages.employee.vehicles.edit-assessment', compact('assessment', 'checklistItems', 'checklistResults','vehicles','userName'));
    }

    /**
     * Update the specified assessment
     */
    public function updateAssessment(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'assessment_date' => 'required|date',
            'employee_id' => 'nullable|exists:employees,id',
            'approved' => 'required|boolean',
            'status_name' => 'required|string|max:255',
            'status_description' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'checklist_results' => 'required|array',
            'checklist_results.*.item_id' => 'required|exists:checklist_items,id',
            'checklist_results.*.passed' => 'nullable|boolean',
        ]);

        // Update or Create Assessment
        $assessment = Assessment::updateOrCreate(
            ['id' => $id], // Kondisi pencarian
            [
                'vehicle_id' => $request->vehicle_id,
                'employee_id' => $request->employee_id,
                'assessment_date' => $request->assessment_date,
                'approved' => $request->approved,
                'status_name' => $request->status_name,
                'status_description' => $request->status_description,
                'comments' => $request->comments,
            ]
        );       

        // Handle checklist results dengan updateOrCreate
        $requestedItemIds = collect($request->checklist_results)->pluck('item_id')->toArray();
        
        // Delete checklist results yang tidak ada dalam request
        AssessmentChecklistResult::where('assessment_id', $assessment->id)
            ->whereNotIn('checklist_items_id', $requestedItemIds)
            ->delete();
        
        // Update or Create setiap checklist result
        foreach ($request->checklist_results as $result) {
            AssessmentChecklistResult::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'checklist_items_id' => $result['item_id']
                ],
                [
                    'passed' => isset($result['passed']) ? ($result['passed'] ? 1 : 0) : 0,
                ]
            );
        }
        
        return redirect()->route('pages.employee.vehicles.index')
            ->with('success', 'Penilaian kendaraan berhasil diperbarui!');
        // // Validasi input
        // $request->validate([
        //     'vehicle_id' => 'required|exists:vehicles,id',
        //     'assessment_date' => 'required|date',
        //     'employee_id' => 'nullable|exists:employees,id',
        //     'approved' => 'required|boolean',
        //     'status_name' => 'required|string|max:255',
        //     'status_description' => 'required|string|max:255',
        //     'comments' => 'nullable|string',
        //     'checklist_results' => 'required|array',
        //     'checklist_results.*.item_id' => 'required|exists:checklist_items,id',
        //     'checklist_results.*.passed' => 'nullable|boolean',
        // ]);

        // // Find the assessment
        // $assessment = Assessment::whereId($id)->first();
        
        // // Update assessment data
        // $assessment->update([
        //     'vehicle_id' => $request->vehicle_id,
        //     'employee_id' => $request->employee_id ?? $assessment->employee_id,
        //     'assessment_date' => $request->assessment_date,
        //     'approved' => $request->approved,
        //     'status_name' => $request->status_name,
        //     'status_description' => $request->status_description,
        //     'comments' => $request->comments,
        // ]);

        // // Delete existing checklist results
        // AssessmentChecklistResult::where('assessment_id', $assessment->id)->delete();
        
        // // Insert new checklist results
        // $checklistData = [];
        // foreach ($request->checklist_results as $result) {
        //     $checklistData[] = [
        //         'assessment_id' => $assessment->id,
        //         'checklist_items_id' => $result['item_id'],
        //         'passed' => isset($result['passed']) ? 1 : 0,
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ];
        // }
        
        // AssessmentChecklistResult::insert($checklistData);
        
        // return redirect()->route('pages.employee.vehicles.index')
        //     ->with('success', 'Penilaian kendaraan berhasil diperbarui!');
    }

    // API endpoints for AJAX requests
    public function apiIndex(Request $request)
    {
        $query = Vehicle::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'vehicles' => $vehicles->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'plate_number' => $vehicle->plate_number,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'type' => $vehicle->type,
                    'engine_capacity' => $vehicle->engine_capacity,
                    'status' => $vehicle->status,
                    'assessment_status' => $vehicle->assessment_status,
                    'license_expiry' => $vehicle->license_expiry->format('M d, Y'),
                    'license_expiry_status' => $vehicle->license_expiry_status,
                    'assessment_badge' => $vehicle->assessment_status_badge,
                    'vehicle_icon' => $vehicle->vehicle_icon
                ];
            })
        ]);
    }

    public function storeAsses(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'assessment_date' => 'required|date',
            'employee_id' => 'nullable|exists:employees,id',
            'approved' => 'required|boolean',
            'status_name' => 'required|string|max:255',
            'status_description' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'checklist_results' => 'required|array',
            'checklist_results.*.item_id' => 'required|exists:checklist_items,id',
            'checklist_results.*.passed' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create the assessment
            $assessment = Assessment::create([
                'vehicle_id' => $request->vehicle_id,
                'employee_id' => $request->employee_id ?? auth()->user()->employee->id ?? null,
                'assessment_date' => $request->assessment_date,
                'approved' => $request->approved,
                'status_name' => $request->status_name,
                'status_description' => $request->status_description,
                'comments' => $request->comments,
            ]);

            // Save checklist results
            foreach ($request->checklist_results as $result) {
                AssessmentChecklistItem::create([
                    'assessment_id' => $assessment->id,
                    'checklist_item_id' => $result['item_id'],
                    'passed' => isset($result['passed']) ? 1 : 0,
                ]);
            }

            DB::commit();

            return redirect()->route('assessments.show', $assessment->id)
                ->with('success', 'Penilaian kendaraan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan penilaian: ' . $e->getMessage());
        }
    }
}
