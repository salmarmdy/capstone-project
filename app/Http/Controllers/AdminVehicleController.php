<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminVehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::with('employee')->latest()->paginate(10);
        $employees = Employee::all(); 
        return view('pages.kendaraan.index', compact('vehicles', 'employees'));
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->with('employee')->get();
        return view('pages.kendaraan.create', compact('employees'));
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'vehicle_type' => 'required|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'engine_capacity' => 'required|integer',
            'license_expiry' => 'required|date',
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pages.kendaraan.create')
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle = new Vehicle($request->except('license_document'));
        $vehicle->save();

        $vehicles = Vehicle::with('employee')->latest()->paginate(10);
        $employees = Employee::all(); 

        return view('pages.kendaraan.index', compact('vehicles', 'employees'));
    }

    /**
     * Display the specified vehicle.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['employee', 'assessments'])->findOrFail($id);
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $employees = Employee::orderBy('name')->get();
        return view('vehicles.edit', compact('vehicle', 'employees'));
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $id . ',vehicle_id',
            'vehicle_type' => 'required|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'engine_capacity' => 'required|integer',
            'license_expiry' => 'required|date',
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vehicles.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->fill($request->except('license_document'));
        
        // Handle file upload
        if ($request->hasFile('license_document')) {
            // Delete old file if exists
            if ($vehicle->license_document_path) {
                Storage::disk('public')->delete($vehicle->license_document_path);
            }
            
            $path = $request->file('license_document')->store('license_documents', 'public');
            $vehicle->license_document_path = $path;
        }

        $vehicle->save();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle updated successfully');
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Delete associated file if exists
        if ($vehicle->license_document_path) {
            Storage::disk('public')->delete($vehicle->license_document_path);
        }
        
        $vehicle->delete();

        $vehicles = Vehicle::with('employee')->latest()->paginate(10);
        return view('pages.kendaraan.index', compact('vehicles'));
    }
}