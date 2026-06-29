<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PatientImport;



class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Construimos la consulta base
        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                // Filtramos por nombre o DNI usando LIKE
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Mantiene el término de búsqueda al cambiar de página

        return view('patients.index', compact('patients', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // No lo usamos porque el formulario de creación está en un Modal en el index
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dni'  => 'required|string|max:20|unique:patients,dni',
        ]);

        Patient::create($request->all());

        return back()->with('success', 'Paciente registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // No lo usamos en este CRUD básico, pero podrías usarlo para un perfil detallado
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // No lo usamos porque editamos a través del Modal inyectando los datos con Alpine.js
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Ignoramos el DNI del paciente actual para que no dé error de "unique" al guardar
            'dni'  => 'required|string|max:20|unique:patients,dni,' . $patient->id,
        ]);

        $patient->update($request->all());

        return back()->with('success', 'Datos del paciente actualizados.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return back()->with('success', 'Paciente eliminado del sistema.');
    }

    public function import(Request $request)
    {
        // Validar que se haya subido un archivo y que sea de tipo Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'excel_file.required' => 'Por favor, selecciona un archivo Excel.',
            'excel_file.mimes' => 'El archivo debe ser un Excel válido (.xlsx, .xls o .csv).',
        ]);

        try {
            Excel::import(new PatientImport, $request->file('excel_file'));
            return back()->with('success', '¡Pacientes importados correctamente desde el Excel!');
        } catch (\Exception $e) {
            // Capturar errores (por ejemplo, si el formato del Excel es incorrecto)
            return back()->withErrors(['error' => 'Error al importar: Revisa que tu archivo tenga las columnas "nombre" y "dni".']);
        }
    }
}