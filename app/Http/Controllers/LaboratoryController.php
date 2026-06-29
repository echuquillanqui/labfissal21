<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Imports\LaboratoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date_filter = $request->input('date_filter');

        $laboratories = Laboratory::with('patient')
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
                });
            })
            ->when($date_filter, function ($query, $date_filter) {
                $query->whereDate('created_at', $date_filter);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('laboratories.index', compact('laboratories', 'search', 'date_filter'));
    }

    public function store(Request $request)
    {
        Laboratory::create($request->all());
        return back()->with('success', 'Resultado analítico guardado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $laboratory = Laboratory::findOrFail($id);
        $laboratory->update($request->all());
        
        return back()->with('success', 'Los valores del laboratorio se actualizaron con éxito.');
    }

    public function destroy($id)
    {
        $laboratory = Laboratory::findOrFail($id);
        $laboratory->delete();
        
        return back()->with('success', 'El registro de laboratorio ha sido eliminado.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:4096',
        ]);

        try {
            $import = new LaboratoryImport;
            Excel::import($import, $request->file('excel_file'));

            $message = '¡Reporte FISSAL procesado! Registros importados: ' . $import->getImported();

            if ($import->getSkipped() > 0) {
                $message .= '. Filas omitidas por paciente no encontrado: ' . $import->getSkipped();
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar el Excel: ' . $e->getMessage()]);
        }
    }

    public function printBlock(Request $request)
    {
        $date = $request->input('date_filter');
        
        if (!$date) {
            return back()->withErrors(['error' => 'Debe seleccionar una fecha para generar el reporte en bloque.']);
        }

        $reports = Laboratory::with('patient')
            ->whereDate('created_at', $date)
            ->get();

        if ($reports->isEmpty()) {
            return back()->withErrors(['error' => 'No hay exámenes registrados en la fecha seleccionada.']);
        }

        $pdf = Pdf::loadView('laboratories.pdf_block', compact('reports', 'date'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Resultados_Laboratorio_{$date}.pdf");
    }
}