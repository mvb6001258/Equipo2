@extends('layouts.app')

@section('title', 'Lotes y Trazabilidad Agrícola')
@section('page_title', 'Gestión de Lotes Agrícolas')
@section('breadcrumb', 'Listado de Lotes')

@section('content')
<!-- Metric Cards (Flat UI) -->
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-navy">
            <div class="inner">
                <h3>{{ $batches->count() }}</h3>
                <p class="text-uppercase font-weight-bold">Lotes Registrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $farms->count() }}</h3>
                <p class="text-uppercase font-weight-bold">Fincas & Productoras</p>
            </div>
            <div class="icon">
                <i class="fas fa-seedling"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $batches->sum(fn($b) => $b->events->count()) }}</h3>
                <p class="text-uppercase font-weight-bold">Bloques SHA-256 Enlazados</p>
            </div>
            <div class="icon">
                <i class="fas fa-link"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h3 class="card-title text-navy font-weight-bold m-0">
            <i class="fas fa-list mr-2"></i> Lotes Agrícolas en la Cadena de Suministro
        </h3>
        <div>
            <button type="button" class="btn btn-outline-secondary btn-sm mr-2" data-toggle="modal" data-target="#modalFarm">
                <i class="fas fa-plus-circle mr-1"></i> Nueva Finca
            </button>
            <button type="button" class="btn btn-primary btn-sm bg-navy border-0" data-toggle="modal" data-target="#modalBatch">
                <i class="fas fa-plus-circle mr-1"></i> Registrar Nuevo Lote
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-0 text-sm">
                <thead class="bg-light text-uppercase text-xs text-muted">
                    <tr>
                        <th>Código QR Token</th>
                        <th>Producto</th>
                        <th>Finca / Origen</th>
                        <th>Cantidad</th>
                        <th>Fecha Cosecha</th>
                        <th class="text-center">Bloques SHA-256</th>
                        <th>Último Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        @php
                            $lastEvent = $batch->events->last();
                        @endphp
                        <tr>
                            <td class="font-weight-bold text-navy">
                                <span class="hash-code">{{ $batch->qr_code_token }}</span>
                            </td>
                            <td>
                                <strong>{{ $batch->product_name }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $batch->farm->name }}
                                <br><small class="text-muted">{{ $batch->farm->location }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary px-2 py-1">{{ $batch->quantity }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($batch->harvest_date)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-info px-2 py-1">
                                    <i class="fas fa-cubes mr-1"></i> {{ $batch->events->count() }} bloques
                                </span>
                            </td>
                            <td>
                                @if($lastEvent)
                                    <span class="badge bg-success px-2 py-1">{{ $lastEvent->stage }}</span>
                                    <br><small class="text-muted">{{ $lastEvent->recorded_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark px-2 py-1">Sin Eventos (Génesis)</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('traceability.show', $batch->qr_code_token) }}" class="btn btn-navy btn-sm bg-navy text-white">
                                    <i class="fas fa-stream mr-1"></i> Ver Timeline Blockchain
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle fa-2x d-block mb-2 text-secondary"></i>
                                No hay lotes agrícolas registrados actualmente. Use los botones superiores para registrar uno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Registrar Finca -->
<div class="modal fade" id="modalFarm" tabindex="-1" role="dialog" aria-labelledby="modalFarmLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('farms.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title font-weight-bold" id="modalFarmLabel">
                        <i class="fas fa-seedling mr-2"></i> Registrar Nueva Finca Productora
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Nombre de la Finca / Hacienda *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Hacienda El Cafetal" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Ubicación / Georreferenciación *</label>
                        <input type="text" name="location" class="form-control" placeholder="Ej. Sector San José, Mérida" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Propietario / Responsable *</label>
                        <input type="text" name="owner" class="form-control" placeholder="Ej. Ing. Roberto Gómez" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm">Guardar Finca</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Crear Lote Agrícola -->
<div class="modal fade" id="modalBatch" tabindex="-1" role="dialog" aria-labelledby="modalBatchLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('batches.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title font-weight-bold" id="modalBatchLabel">
                        <i class="fas fa-box-open mr-2"></i> Crear Nuevo Lote Agrícola
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Seleccionar Finca de Origen *</label>
                        <select name="farm_id" class="form-control" required>
                            <option value="">-- Seleccione una finca --</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}">{{ $farm->name }} ({{ $farm->location }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Nombre del Producto Agroindustrial *</label>
                        <input type="text" name="product_name" class="form-control" placeholder="Ej. Café Arábica Orgánico" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Cantidad / Volumen *</label>
                            <input type="text" name="quantity" class="form-control" placeholder="Ej. 1000 kg" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Fecha de Cosecha *</label>
                            <input type="date" name="harvest_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Token Código QR (Opcional - Autogenerado si vacío)</label>
                        <input type="text" name="qr_code_token" class="form-control" placeholder="Ej. BATCH-2026-X99">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm bg-navy border-0">Crear Lote</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
