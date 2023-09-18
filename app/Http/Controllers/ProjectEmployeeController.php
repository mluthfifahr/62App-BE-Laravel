<?php

namespace App\Http\Controllers;

use App\Models\ProjectEmployee;
use Illuminate\Http\Request;

class ProjectEmployeeController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $search = $request->query('search');
        $sort = $request->query('sort') ? $request->query('sort') : 'employee_name';
        $direction = $request->query('direction') ? $request->query('direction') : 'asc';
        $per_page = $request->query('per_page') ? $request->query('per_page') : 10;

        $projectEmployees = ProjectEmployee::leftJoin('employees', 'employees.id', '=', 'employee_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_id')
            ->where('employees.name', 'like', '%' . $search . '%' )
            ->select(['employees.name as employee_name', 'projects.name as project_name', 'project_employees.start_date', 'project_employees.end_date', 'project_employees.status', 'project_employees.updated_at', 'project_employees.created_at'])
            ->orderBy($sort, $direction)
            ->paginate($per_page);

        return response()->json($projectEmployees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'required|integer|min:1|max:2'
        ]);

        $projectEmployee = ProjectEmployee::create($validated);

        return response()->json(['data' => $projectEmployee, 'message' => 'Project Employee Added']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectEmployee $projectEmployee) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectEmployee $projectEmployee) {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'required|integer|min:1|max:2'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $deleted = ProjectEmployee::where('id', $id)->delete();

        return response()->json(['deleted' => $deleted,  'message' => 'Project Employee Deleted']);
    }
}
