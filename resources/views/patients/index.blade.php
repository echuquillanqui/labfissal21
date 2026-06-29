@extends('layouts.app')

@section('content')
<div class="container py-4" x-data="patientCrud()">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-primary fw-bold">Gestión de Pacientes</h2>
            <p class="text-muted mb-0">Administra el registro, edición y eliminación de pacientes.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel me-1" viewBox="0 0 16 16">
                  <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                Importar Excel
            </button>
            <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                + Nuevo Paciente
            </button>
        </div>
    </div>

    <div class="mb-4">
        <form action="{{ route('patients.index') }}" method="GET">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-white border-end-0 text-muted px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </span>
                
                <input type="text" name="search" class="form-control border-start-0 border-end-0 shadow-none" 
                       placeholder="Buscar por nombre o DNI del paciente..." value="{{ request('search') }}" autocomplete="off">
                
                @if(request('search'))
                    <a href="{{ route('patients.index') }}" class="btn btn-white border border-start-0 border-end-0 text-danger bg-white d-flex align-items-center" title="Limpiar búsqueda">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                          <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                    </a>
                @endif
                
                <button type="submit" class="btn btn-primary px-4 fw-medium">Buscar</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" 
             x-transition.duration.500ms class="alert alert-success d-flex align-items-center shadow-sm rounded-3" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Completo</th>
                            <th class="px-4 py-3">Documento (DNI)</th>
                            <th class="px-4 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td class="px-4 py-3 text-muted">#{{ $patient->id }}</td>
                                <td class="px-4 py-3 fw-semibold">{{ $patient->name }}</td>
                                <td class="px-4 py-3"><span class="badge bg-secondary rounded-pill px-3">{{ $patient->dni }}</span></td>
                                <td class="px-4 py-3 text-end">
                                    <button @click="setEditData({{ $patient->id }}, '{{ $patient->name }}', '{{ $patient->dni }}')" 
                                            class="btn btn-sm btn-outline-primary rounded-3 px-3 me-1" 
                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                        Editar
                                    </button>
                                    
                                    <button @click="setDeleteData({{ $patient->id }}, '{{ $patient->name }}')" 
                                            class="btn btn-sm btn-outline-danger rounded-3 px-3" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    No hay pacientes registrados aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patients->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $patients->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- ============================== --}}
    {{-- MODAL CREAR --}}
    {{-- ============================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-bg-primary border-0">
                    <h5 class="modal-title" id="createModalLabel">Registrar Nuevo Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">DNI</label>
                            <input type="text" name="dni" class="form-control" required placeholder="Número de documento">
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Paciente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================== --}}
    {{-- MODAL EDITAR (Dinámico con Alpine) --}}
    {{-- ============================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-bg-primary border-0">
                    <h5 class="modal-title" id="editModalLabel">Actualizar Datos del Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form :action="editFormUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" name="name" x-model="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">DNI</label>
                            <input type="text" name="dni" x-model="editDni" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================== --}}
    {{-- MODAL ELIMINAR (Dinámico con Alpine) --}}
    {{-- ============================== --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-bg-danger border-0">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form :action="deleteFormUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <svg class="text-danger" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                            </svg>
                        </div>
                        <h5 class="mb-3">¿Estás seguro de eliminar a este paciente?</h5>
                        <p class="text-muted mb-0">Se eliminará de forma permanente el registro de <strong x-text="deleteName"></strong>.</p>
                    </div>
                    <div class="modal-footer border-0 bg-light justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger px-4">Sí, Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================== --}}
    {{-- MODAL IMPORTAR EXCEL --}}
    {{-- ============================== --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-bg-success border-0">
                    <h5 class="modal-title" id="importModalLabel">Importar Pacientes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data" x-data="{ uploading: false }" @submit="uploading = true">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info bg-light border-info pb-0 shadow-sm rounded-3">
                            <p class="mb-2"><small>Tu archivo Excel debe tener la siguiente estructura en la primera fila (Cabeceras):</small></p>
                            <ul>
                                <li><small><strong>nombre</strong></small></li>
                                <li><small><strong>dni</strong></small></li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seleccionar Archivo (.xlsx, .csv)</label>
                            <input class="form-control" type="file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4" :disabled="uploading">
                            <span x-show="!uploading">Importar Datos</span>
                            <span x-show="uploading">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('patientCrud', () => ({
            // Variables para el Edit
            editFormUrl: '',
            editName: '',
            editDni: '',
            
            // Variables para el Delete
            deleteFormUrl: '',
            deleteName: '',

            // Método para cargar datos al modal de Editar
            setEditData(id, name, dni) {
                this.editFormUrl = `/patients/${id}`;
                this.editName = name;
                this.editDni = dni;
            },

            // Método para cargar datos al modal de Eliminar
            setDeleteData(id, name) {
                this.deleteFormUrl = `/patients/${id}`;
                this.deleteName = name;
            }
        }));
    });
</script>
@endsection