<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $search = $request->query('search');
        $sort = $request->query('sort') ? $request->query('sort') : 'name';
        $direction = $request->query('direction') ? $request->query('direction') : 'asc';
        $per_page = $request->query('per_page') ? $request->query('per_page') : 10;

        $employees = Employee::join('employee_positions', 'employee_positions.id', '=', 'employees.employee_position_id')
            ->where('employees.deleted_at', null)
            ->where('employees.name', 'like', '%' . $search . '%')
            ->select(['employees.id', 'employees.name', 'employees.profile_url', 'employee_positions.name as employee_position', 'employees.updated_at', 'employees.created_at'])
            ->orderBy($sort, $direction)
            ->paginate($per_page);

        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:30',
            'profile_url' => 'image|mimes:jpg,jpeg,png',
            'employee_position_id' => 'required|exists:employee_positions,id'
        ]);

        if($request->hasFile('profile_url')) {
            $filename = time() . '-' . $request->file('profile_url')->getClientOriginalName();
            $image = $request->file('profile_url')->storeAs('employee', $filename);
            $validated['profile_url'] = url('/') . '/storage/' . $image;
        }

        $employee = Employee::create($validated);

        return response()->json(['data' => $employee, 'message' => 'Employee Added'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        $employee = Employee::with('employeePosition')->find($id);
        return response()->json(['data' => $employee]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        $validated = $request->validate([
            'name' => 'required|max:30',
            'profile_url' => 'image|mimes:jpg,jpeg,png',
            'employee_position_id' => 'required|exists:employee_positions,id'
        ]);

        $employee = Employee::find($id);

        if($request->hasFile('profile_url')) {
            $filename = time() . '-' . $request->file('profile_url')->getClientOriginalName();
            $image = $request->file('profile_url')->storeAs('employee', $filename);
            
            try {
                $prev_img = explode(url('/'), $employee->profile_url)[1];
                unlink(public_path($prev_img));
            } catch (\Throwable $th) {
            }

            $validated['profile_url'] = url('/') . '/storage/' . $image;
        }

        $employee->update($validated);
        $employee = Employee::with('employeePosition')->find($id);

        return response()->json(['data' => $employee,  'message' => 'Employee Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $deleted = Employee::where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return response()->json(['deleted' => $deleted,  'message' => 'Employee Deleted']);
    }
}
