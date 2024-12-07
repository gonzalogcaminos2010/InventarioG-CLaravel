<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')
            ->withCount('eppDeliveries')
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_number' => 'required|string|unique:employees',
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_number' => 'required|string|unique:employees,document_number,' . $employee->id,
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['eppDeliveries' => function($query) {
            $query->with(['items.item', 'warehouse', 'user'])
                  ->latest();
        }]);

        return view('employees.show', compact('employee'));
    }
}