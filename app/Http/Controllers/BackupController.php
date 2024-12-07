<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $backups = collect(Storage::files('backups'))->map(function($file) {
            return [
                'name' => basename($file),
                'size' => Storage::size($file),
                'date' => Storage::lastModified($file)
            ];
        })->sortByDesc('date');

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            Artisan::call('db:backup');
            return redirect()->route('backups.index')
                ->with('success', 'Backup creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Error al crear backup: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = storage_path("app/backups/{$filename}");
        
        if (!file_exists($path)) {
            return redirect()->route('backups.index')
                ->with('error', 'El archivo no existe.');
        }

        return response()->download($path);
    }

    public function destroy($filename)
    {
        try {
            if (Storage::exists("backups/{$filename}")) {
                Storage::delete("backups/{$filename}");
                return redirect()->route('backups.index')
                    ->with('success', 'Backup eliminado exitosamente.');
            }
            return redirect()->route('backups.index')
                ->with('error', 'El archivo no existe.');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Error al eliminar backup: ' . $e->getMessage());
        }
    }
}