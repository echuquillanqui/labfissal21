@extends('layouts.app')

@section('content')
<div class="container py-4" x-data="labCrud()">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-primary fw-bold">Resultados de Laboratorio</h2>
            <p class="text-muted mb-0">Módulo analítico general sincronizado con reportes FISSAL.</p>
        </div>
        <div>
            <button class="btn btn-outline-success px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                📊 Importar Reporte FISSAL
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="alert alert-success shadow-sm rounded-3">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('laboratories.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Buscar registro</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">🔍</span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por Paciente o DNI..." value="{{ $search }}">
                        @if($search || $date_filter)
                            <a href="{{ route('laboratories.index') }}" class="btn btn-light border-top border-bottom border-end text-danger">✕</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Fecha para ver en tabla</label>
                    <input type="date" name="date_filter" class="form-control bg-light" value="{{ $date_filter }}">
                </div>
                <div class="col-lg-2 col-md-6 d-grid">
                    <button type="submit" class="btn btn-primary fw-medium">Filtrar Tabla</button>
                </div>
            
            </form>

            <hr class="my-3">

            <form action="{{ route('laboratories.print_block') }}" method="GET" target="_blank" class="row g-3 align-items-end">
                <div class="col-lg-7 col-md-12">
                    <div class="alert alert-danger-subtle border border-danger-subtle rounded-3 mb-0 py-2 small">
                        <strong>Impresión por paciente:</strong> seleccione una fecha y se generará un PDF con <strong>una hoja independiente por cada paciente</strong>, usando el formato de resultados de laboratorio.
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Fecha a imprimir</label>
                    <input type="date" name="print_date" class="form-control bg-light" value="{{ $date_filter }}" required>
                </div>
                <div class="col-lg-2 col-md-6 d-grid">
                    <button type="submit" class="btn btn-outline-danger fw-medium">
                        🖨️ Imprimir por Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Paciente / Identificación</th>
                            <th class="py-3 text-center">Procedencia</th>
                            <th class="py-3 text-center">Fecha Registro</th>
                            <th class="py-3 text-center">Hemoglobina</th>
                            <th class="py-3 text-center">Hematocrito</th>
                            <th class="py-3 text-center">Urea (Pre / Post)</th>
                            <th class="px-4 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laboratories as $lab)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $lab->patient->name }}</div>
                                <small class="text-muted">DNI: {{ $lab->patient->dni }}</small>
                            </td>
                            <td class="text-center"><span class="badge bg-primary px-3 rounded-pill">FISSAL</span></td>
                            <td class="text-center text-muted"><small>{{ $lab->created_at->format('d/m/Y') }}</small></td>
                            <td class="text-center fw-semibold text-primary">{{ $lab->hemoglobina ?? '-' }} g/dl</td>
                            <td class="text-center text-secondary">{{ $lab->hematocrito ?? '-' }}%</td>
                            <td class="text-center">
                                <span class="text-success">Pre: {{ $lab->urea_pre ?? '-' }}</span> / 
                                <span class="text-danger">Post: {{ $lab->urea_post ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button @click="setEditData(@json($lab))" class="btn btn-sm btn-outline-primary rounded-3 px-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal">Editar</button>
                                <button @click="deleteUrl = '/laboratories/' + {{ $lab->id }}" class="btn btn-sm btn-outline-danger rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#deleteModal">Eliminar</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No se encontraron registros de laboratorio cargados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($laboratories->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $laboratories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-bg-primary border-0">
                    <h5 class="modal-title fw-bold">Actualizar Historial Analítico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form :action="'/laboratories/' + editData.id" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">🩸 Hematología</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Hemoglobina (g/dl)</label>
                                <input type="text" name="hemoglobina" x-model="editData.hemoglobina" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Hematocrito (%)</label>
                                <input type="text" name="hematocrito" x-model="editData.hematocrito" class="form-control">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">🧪 Química Sanguínea y Electrolitos</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Urea Pre (mg/dl)</label>
                                <input type="text" name="urea_pre" x-model="editData.urea_pre" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Urea Post (mg/dl)</label>
                                <input type="text" name="urea_post" x-model="editData.urea_post" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Cloro (mmol/L)</label>
                                <input type="text" name="cloro" x-model="editData.cloro" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Sodio (mmol/L)</label>
                                <input type="text" name="sodio" x-model="editData.sodio" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Potasio (mmol/L)</label>
                                <input type="text" name="potasio" x-model="editData.potasio" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Fósforo (mg/dl)</label>
                                <input type="text" name="fosforo" x-model="editData.fosforo" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Calcio Total (mg/dl)</label>
                                <input type="text" name="calcio_total" x-model="editData.calcio_total" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary" title="Transaminasa Glutámico Oxalacética">TGO (U/L)</label>
                                <input type="text" name="tgo" x-model="editData.tgo" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary" title="Transaminasa Glutámico Pirúvica">TGP (U/L)</label>
                                <input type="text" name="tgp" x-model="editData.tgp" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-bg-success border-0">
                    <h5 class="modal-title fw-bold">Importar Excel de FISSAL</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('laboratories.import') }}" method="POST" enctype="multipart/form-data" x-data="{ load: false }" @submit="load = true">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info rounded-3 small">
                            El sistema procesará las filas emparejando automáticamente por nombres y asignando la fecha de hoy.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Archivo de Resultados (.xlsx, .xls, .csv)</label>
                            <input type="file" name="excel_file" class="form-control" required accept=".xlsx, .xls, .csv">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4" :disabled="load">
                            <span x-show="!load">Procesar Reporte</span>
                            <span x-show="load">Importando datos...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form :action="deleteUrl" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body p-4 text-center">
                        <h5 class="text-danger fw-bold mb-2">¿Eliminar registro?</h5>
                        <p class="text-muted small mb-0">Esta operación quitará de forma permanente el examen clínico seleccionado.</p>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger btn-sm px-3">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('labCrud', () => ({
            editData: {}, 
            deleteUrl: '',
            setEditData(lab) {
                this.editData = { ...lab }; 
            }
        }));
    });
</script>
@endsection