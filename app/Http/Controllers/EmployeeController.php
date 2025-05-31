<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::whereHas('user', function ($query) {
            $query->where('role', '!=', 'admin');
        })->latest()->paginate(10);
        return view('pages.karyawan.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.karyawan.create');
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'sim_number' => 'nullable|string|max:50',
            'sim_expiry_date' => 'nullable|date',
            'employment_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create employee
        $employee = new Employee($request->all());
        $employee->save();

        // Generate username and password for user account
        $username = $this->generateUsername($request->name);
        $password = $this->generatePassword();

        // Create user account
        $user = new User();
        $user->user_id = 'EMP' . str_pad($employee->id, 6, '0', STR_PAD_LEFT);
        $user->username = $username;
        $user->password_hash = Hash::make($password);
        $user->email = $request->email;
        $user->role = 'employee';
        $user->employee_id = $employee->id;
        $user->save();

        return redirect()->route('employee-index')
            ->with('success', 'Karyawan berhasil ditambahkan.')
            ->with('login_info', [
                'username' => $username,
                'password' => $password,
                'employee_name' => $request->name
            ]);
    }

    /**
     * Display the specified employee.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('pages.karyawan.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('pages.karyawan.edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'sim_number' => 'nullable|string|max:50',
            'sim_expiry_date' => 'nullable|date',
            'employment_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        // Update user email if changed
        $user = User::where('employee_id', $id)->first();
        if ($user && $user->email !== $request->email) {
            $user->email = $request->email;
            $user->save();
        }

        return redirect()->route('employee-index')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Delete associated user account
        User::where('employee_id', $id)->delete();
        
        $employee->delete();

        return redirect()->route('employee-index')
            ->with('success', 'Karyawan berhasil dihapus');
    }

    /**
     * Generate unique username based on employee name
     *
     * @param string $name
     * @return string
     */
    private function generateUsername($name)
    {
        // Clean name and create base username
        $cleanName = strtolower(str_replace(' ', '', $name));
        $baseUsername = substr($cleanName, 0, 8);
        
        // Check if username exists
        $counter = 1;
        $username = $baseUsername;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Generate random password
     *
     * @return string
     */
    private function generatePassword()
    {
        return Str::random(8);
    }

    public function createUserAccount($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Check if user account already exists
        if (User::where('employee_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun sudah ada untuk karyawan ini'
            ]);
        }

        // Generate username and password
        $username = $this->generateUsername($employee->name);
        $password = $this->generatePassword();

        // Create user account
        $user = new User();
        $user->user_id = 'EMP' . str_pad($employee->id, 6, '0', STR_PAD_LEFT);
        $user->username = $username;
        $user->password_hash = Hash::make($password);
        $user->email = $employee->email;
        $user->role = 'employee';
        $user->employee_id = $employee->id;
        $user->save();

        return response()->json([
            'success' => true,
            'username' => $username,
            'password' => $password,
            'message' => 'Akun berhasil dibuat'
        ]);
    }
}