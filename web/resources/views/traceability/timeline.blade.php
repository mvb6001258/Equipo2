@extends('layouts.app')

@section('title', 'Timeline Blockchain - Lote ' . $batch->qr_code_token)
@section('page_title', 'Cadena de Trazabilidad Criptográfica')
@section('breadcrumb', 'Timeline de Lote')

@section('content')
<!-- Header Information Card -->
<div class="card bg-white mb-4">
    <div class="card-header bg-navy text-white d-flex justify-content-between align-items-center py-3">
        <div>
            <h3 class="card-title font-weight-bold m-0 text-white">
                <i class="fas fa-barcode mr-2"></i> Lote: <span class="text-warning font-monospace">{{ $batch->qr_code_token }}</span>
            </h3>
        </div>
        <div>
            <a href="{{ route('traceability.index') }}" class="btn btn-outline-light btn-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Lotes
            </a>
            <button type="button" class="btn btn-success btn-sm font-weight-bold" data-toggle="modal" data-target="#modalAddEvent">
                <i class="fas fa-cube mr-1"></i> Añadir Nuevo Bloque a la Cadena
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 border-right">
                <span class="text-uppercase text-xs text-muted font-weight-bold d-block">Producto</span>
                <strong class="h5 text-navy d-block mb-1">{{ $batch->product_name }}</strong>
                <span class="badge bg-secondary">{{ $batch->quantity }}</span>
            </div>
            <div class="col-md-3 border-right">
                <span class="text-uppercase text-xs text-muted font-weight-bold d-block">Origen / Finca</span>
                <strong class="d-block text-dark">{{ $batch->farm->name }}</strong>
                <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $batch->farm->location }}</small>
            </div>
            <div class="col-md-3 border-right">
                <span class="text-uppercase text-xs text-muted font-weight-bold d-block">Propietario / Productor</span>
                <strong class="d-block text-dark">{{ $batch->farm->owner }}</strong>
                <small class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> Cosechado: {{ \Carbon\Carbon::parse($batch->harvest_date)->format('d/m/Y') }}</small>
            </div>
            <div class="col-md-3">
                <span class="text-uppercase text-xs text-muted font-weight-bold d-block">Auditoría Blockchain</span>
                @if($chainStatus['is_valid'])
                    <span class="badge bg-success p-2 d-inline-block mt-1">
                        <i class="fas fa-shield-check mr-1"></i> Cadena Inmutable VÁLIDA (100%)
                    </span>
                @else
                    <span class="badge bg-danger p-2 d-inline-block mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i> ALTERACIÓN DETECTADA
                    </span>
                @endif
                <small class="d-block text-muted mt-1">{{ $chainStatus['total_blocks'] }} Bloque(s) SHA-256 enlazados</small>
            </div>
        </div>
    </div>
</div>

<!-- AdminLTE Flat Timeline Component -->
<div class="row">
    <div class="col-md-12">
        <div class="timeline">

            @forelse($batch->events as $index => $event)
                @php
                    $isGenesis = ($index === 0);
                    $blockNumber = $index + 1;
                @endphp
                
                <!-- Timeline time label -->
                <div class="time-label">
                    <span class="{{ $isGenesis ? 'bg-success' : 'bg-navy' }} px-3 py-1 font-weight-bold text-uppercase text-xs">
                        Bloque #{{ $blockNumber }} {{ $isGenesis ? '(Génesis)' : '' }} &mdash; {{ $event->recorded_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div>
                    <!-- Icon badge -->
                    <i class="fas {{ $isGenesis ? 'fa-leaf bg-success' : 'fa-cube bg-info' }} elevation-0"></i>

                    <div class="timeline-item {{ $isGenesis ? 'block-genesis' : '' }}">
                        <span class="time text-muted"><i class="fas fa-clock mr-1"></i> {{ $event->recorded_at->diffForHumans() }}</span>

                        <h3 class="timeline-header">
                            <span class="badge {{ $isGenesis ? 'bg-success' : 'bg-navy' }} font-weight-bold mr-2">
                                {{ $event->stage }}
                            </span>
                            <span class="text-dark">{{ $event->location }}</span>
                        </h3>

                        <div class="timeline-body">
                            <p class="mb-3 text-secondary" style="font-size: 1.02rem;">
                                {{ $event->description }}
                            </p>

                            <div class="row bg-light p-3 border text-sm mb-2">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-user-check text-navy mr-2"></i>
                                        <strong class="text-muted text-uppercase text-xs">Actor / Responsable:</strong>
                                    </div>
                                    <span class="font-weight-bold text-dark pl-4 d-block">{{ $event->actor }}</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-clock text-navy mr-2"></i>
                                        <strong class="text-muted text-uppercase text-xs">Fecha y Hora de Registro:</strong>
                                    </div>
                                    <span class="font-weight-bold text-dark pl-4 d-block">{{ $event->recorded_at->format('Y-m-d H:i:s T') }}</span>
                                </div>
                            </div>

                            <!-- Hashes SHA-256 Section -->
                            <div class="p-3 bg-white border">
                                <div class="row">
                                    <!-- Previous Hash -->
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <label class="text-xs font-weight-bold text-uppercase text-muted d-block mb-1">
                                            <i class="fas fa-arrow-left text-secondary mr-1"></i> Previous Hash (SHA-256)
                                        </label>
                                        <div class="hash-code hash-badge-prev w-100 p-2 text-monospace" title="{{ $event->previous_hash }}">
                                            <span class="text-muted font-weight-bold mr-1">[PREV]</span>
                                            {{ Str::limit($event->previous_hash, 18, '...') }} 
                                            <span class="float-right text-xs text-primary font-monospace copy-hash" data-hash="{{ $event->previous_hash }}" style="cursor:pointer;" onclick="navigator.clipboard.writeText('{{ $event->previous_hash }}'); alert('Previous Hash copiado al portapapeles');">
                                                <i class="fas fa-copy"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Current Hash -->
                                    <div class="col-md-6">
                                        <label class="text-xs font-weight-bold text-uppercase text-navy d-block mb-1">
                                            <i class="fas fa-fingerprint text-primary mr-1"></i> Current Hash (SHA-256)
                                        </label>
                                        <div class="hash-code hash-badge-curr w-100 p-2 text-monospace" title="{{ $event->current_hash }}">
                                            <span class="text-primary font-weight-bold mr-1">[HASH]</span>
                                            {{ Str::limit($event->current_hash, 18, '...') }}
                                            <span class="float-right text-xs text-primary font-monospace copy-hash" data-hash="{{ $event->current_hash }}" style="cursor:pointer;" onclick="navigator.clipboard.writeText('{{ $event->current_hash }}'); alert('Current Hash copiado al portapapeles');">
                                                <i class="fas fa-copy"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top text-xs text-muted font-monospace">
                                    <strong>Full Current Hash:</strong> <span class="text-dark">{{ $event->current_hash }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div>
                    <i class="fas fa-exclamation-triangle bg-warning"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header font-weight-bold text-warning">Cadena vacía</h3>
                        <div class="timeline-body">
                            No se han registrado eventos de trazabilidad para este lote. ¡Haga clic en el botón superior "Añadir Nuevo Bloque a la Cadena" para crear el Bloque Génesis!
                        </div>
                    </div>
                </div>
            @endforelse

            <div>
                <i class="fas fa-flag-checkered bg-secondary"></i>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Agregar Evento a la Cadena -->
<div class="modal fade" id="modalAddEvent" tabindex="-1" role="dialog" aria-labelledby="modalAddEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('traceability.storeEvent', $batch->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAddEventLabel">
                        <i class="fas fa-cube mr-2"></i> Registrar Bloque de Trazabilidad (SHA-256)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 text-sm border-0 rounded-0 mb-3">
                        <i class="fas fa-info-circle mr-1"></i> El sistema buscará automáticamente el <code>current_hash</code> del último evento para asignarlo como <code>previous_hash</code> del nuevo bloque.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Etapa de la Cadena *</label>
                            <select name="stage" class="form-control" required>
                                <option value="Cosecha & Recolección">Cosecha & Recolección</option>
                                <option value="Beneficio & Fermentación">Beneficio & Fermentación</option>
                                <option value="Secado & Control de Calidad">Secado & Control de Calidad</option>
                                <option value="Empaque & Sellado Ecotrazable">Empaque & Sellado Ecotrazable</option>
                                <option value="Transporte & Custodia">Transporte & Custodia</option>
                                <option value="Distribución & Almacén">Distribución & Almacén</option>
                                <option value="Punto de Venta / Consumidor Final">Punto de Venta / Consumidor Final</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Actor / Responsable *</label>
                            <input type="text" name="actor" class="form-control" placeholder="Ej. Téc. Juan Pérez (Control Calidad)" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Ubicación / Instalación *</label>
                            <input type="text" name="location" class="form-control" placeholder="Ej. Planta de Procesamiento Central" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-sm">Fecha y Hora del Registro</label>
                            <input type="datetime-local" name="recorded_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                            <small class="text-muted">Si se deja vacío, se usará la hora actual del servidor.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Descripción Detallada del Evento *</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describa los detalles técnicos, parámetros de humedad, PH, temperatura, vehículo de transporte, etc." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-lock mr-1"></i> Generar Hash SHA-256 y Enlazar Bloque
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
