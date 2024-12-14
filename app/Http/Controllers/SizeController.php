<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;


class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('name')->paginate(10);
        return view('sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('sizes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes',
            'description' => 'nullable|string'
        ]);

        Size::create($validated);

        return redirect()->route('sizes.index')
            ->with('success', 'Talle creado exitosamente.');
    }

    public function edit(Size $size)
    {
        return view('sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,' . $size->id,
            'description' => 'nullable|string'
        ]);

        $size->update($validated);

        return redirect()->route('sizes.index')
            ->with('success', 'Talle actualizado exitosamente.');
    }

    public function destroy(Size $size)
    {
        if($size->items()->exists()) {
            return back()->with('error', 'No se puede eliminar el talle porque tiene productos asociados.');
        }

        $size->delete();

        return redirect()->route('sizes.index')
            ->with('success', 'Talle eliminado exitosamente.');
    }
}