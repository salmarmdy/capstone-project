<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::withCount('employees')->latest()->paginate(10);
        return view('pages.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.department.create');
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string|max:500',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        Department::create($data);

        return redirect()->route('department-index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    /**
     * Display the specified department.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = Department::with('employees')->findOrFail($id);
        return view('pages.department.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('pages.department.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'code' => 'required|string|max:10|unique:departments,code,' . $id,
            'description' => 'nullable|string|max:500',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department = Department::findOrFail($id);
        
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;
        
        // Update employees' department if name changed
        if ($department->name !== $request->name) {
            Employee::where('department', $department->name)
                ->update(['department' => $request->name]);
        }

        $department->update($data);

        return redirect()->route('department-index')
            ->with('success', 'Data departemen berhasil diperbarui');
    }

    /**
     * Remove the specified department from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        
        // Check if department has employees
        $employeeCount = Employee::where('department', $department->name)->count();
        if ($employeeCount > 0) {
            return redirect()->route('department-index')
                ->with('error', 'Tidak dapat menghapus departemen yang masih memiliki karyawan. Total karyawan: ' . $employeeCount);
        }
        
        $department->delete();

        return redirect()->route('department-index')
            ->with('success', 'Departemen berhasil dihapus');
    }

    /**
     * Toggle department status
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->status = !$department->status;
        $department->save();

        $status = $department->status ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Departemen berhasil {$status}",
            'status' => $department->status
        ]);
    }

    /**
     * Get active departments for dropdown
     *
     * @return \Illuminate\Http\Response
     */
    public function getActiveDepartments()
    {
        $departments = Department::active()->orderBy('name')->get(['id', 'name', 'code']);
        return response()->json($departments);
    }
}