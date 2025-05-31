<div class="assessment-detail-container">
    {{-- Header Information --}}
    <div class="detail-header">
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <h5><i class="fas fa-car"></i> Informasi Kendaraan</h5>
                    @if($assessment->vehicle)
                        <p><strong>Merek:</strong> {{ $assessment->vehicle->brand }}</p>
                        <p><strong>Model:</strong> {{ $assessment->vehicle->model }}</p>
                        <p><strong>Plat Nomor:</strong> {{ $assessment->vehicle->license_plate }}</p>
                        @if($assessment->vehicle->year)
                            <p><strong>Tahun:</strong> {{ $assessment->vehicle->year }}</p>
                        @endif
                    @else
                        <p class="text-muted">Data kendaraan tidak tersedia</p>
                    @endif
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-group">
                    <h5><i class="fas fa-user"></i> Informasi Pemeriksaan</h5>
                    <p><strong>Pemeriksa:</strong> 
                        {{ $assessment->employee ? $assessment->employee->name : 'Tidak diketahui' }}
                    </p>
                    <p><strong>Tanggal:</strong> 
                        {{ $assessment->assessment_date ? $assessment->assessment_date->format('d M Y H:i') : '-' }}
                    </p>
                    <p><strong>Status:</strong> 
                        @php
                            $totalItems = $assessment->checklistResults->count();
                            $passedItems = $assessment->checklistResults->where('passed', true)->count();
                            $statusClass = '';
                            $statusText = '';
                            
                            if ($assessment->status_name) {
                                $statusText = $assessment->status_name;
                                $statusClass = strtolower(str_replace(' ', '-', $assessment->status_name));
                            } else {
                                // Fallback logic
                                if ($passedItems == $totalItems && $totalItems > 0) {
                                    $statusText = 'Layak';
                                    $statusClass = 'layak';
                                } elseif ($passedItems > ($totalItems * 0.7)) {
                                    $statusText = 'Perlu Perbaikan';
                                    $statusClass = 'perbaikan';
                                } else {
                                    $statusText = 'Tidak Layak';
                                    $statusClass = 'tidak-layak';
                                }
                            }
                        @endphp
                        <span class="status {{ $statusClass }}" 
                              @if($assessment->status_color_code) 
                                  style="background-color: {{ $assessment->status_color_code }}; color: white;" 
                              @endif>
                            {{ $statusText }}
                        </span>
                    </p>
                    @if($assessment->approved)
                        <p><strong>Status Approval:</strong> 
                            <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Assessment Results --}}
    <div class="detail-results">
        <h5><i class="fas fa-clipboard-check"></i> Hasil Pemeriksaan</h5>
        
        @if($assessment->checklistResults && $assessment->checklistResults->count() > 0)
            <div class="results-summary">
                <div class="summary-item">
                    <span class="summary-label">Total Item:</span>
                    <span class="summary-value">{{ $totalItems }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Lulus:</span>
                    <span class="summary-value text-success">{{ $passedItems }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Tidak Lulus:</span>
                    <span class="summary-value text-danger">{{ $totalItems - $passedItems }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Persentase:</span>
                    <span class="summary-value">{{ $totalItems > 0 ? round(($passedItems / $totalItems) * 100, 1) : 0 }}%</span>
                </div>
            </div>

            <div class="checklist-results">
                @foreach($assessment->checklistResults as $result)
                    <div class="checklist-item {{ $result->passed ? 'passed' : 'failed' }}">
                        <div class="item-header">
                            <div class="item-status">
                                @if($result->passed)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </div>
                            <div class="item-title">
                                <strong>{{ $result->checklistItem ? $result->checklistItem->name : 'Item tidak ditemukan' }}</strong>
                            </div>
                        </div>
                        
                        @if($result->checklistItem && $result->checklistItem->description)
                            <div class="item-description">
                                <small class="text-muted">{{ $result->checklistItem->description }}</small>
                            </div>
                        @endif
                        
                        @if($result->notes)
                            <div class="item-notes">
                                <strong>Catatan:</strong> {{ $result->notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-results">
                <div class="text-center p-4">
                    <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Tidak ada hasil pemeriksaan</h6>
                    <p class="text-muted">Belum ada data checklist untuk pemeriksaan ini.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Additional Notes --}}
    @if($assessment->notes)
        <div class="detail-notes">
            <h5><i class="fas fa-sticky-note"></i> Catatan Tambahan</h5>
            <div class="notes-content">
                {{ $assessment->notes }}
            </div>
        </div>
    @endif

    {{-- Approval Information --}}
    @if($assessment->approved_by || $assessment->approved_at)
        <div class="detail-approval">
            <h5><i class="fas fa-stamp"></i> Informasi Persetujuan</h5>
            @if($assessment->approved_by)
                <p><strong>Disetujui oleh:</strong> {{ $assessment->approvedBy->name ?? 'Tidak diketahui' }}</p>
            @endif
            @if($assessment->approved_at)
                <p><strong>Tanggal Persetujuan:</strong> {{ $assessment->approved_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    @endif
</div>

<style>
.assessment-detail-container {
    padding: 20px;
}

.detail-header {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-group {
    margin-bottom: 15px;
}

.info-group h5 {
    color: #495057;
    margin-bottom: 10px;
    font-size: 16px;
}

.info-group p {
    margin-bottom: 5px;
    font-size: 14px;
}

.detail-results {
    margin-bottom: 20px;
}

.results-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    padding: 15px;
    background-color: #e9ecef;
    border-radius: 6px;
    margin-bottom: 15px;
}

.summary-item {
    display: flex;
    flex-direction: column;
    text-align: center;
    min-width: 80px;
}

.summary-label {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 2px;
}

.summary-value {
    font-size: 18px;
    font-weight: bold;
}

.checklist-results {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
}

.checklist-item {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
}

.checklist-item:last-child {
    border-bottom: none;
}

.checklist-item.passed {
    background-color: #f8fff8;
    border-left: 4px solid #28a745;
}

.checklist-item.failed {
    background-color: #fff8f8;
    border-left: 4px solid #dc3545;
}

.item-header {
    display: flex;
    align-items: center;
    gap: 10px;
}

.item-status {
    font-size: 18px;
}

.item-title {
    flex: 1;
}

.item-description {
    margin-top: 5px;
    margin-left: 28px;
}

.item-notes {
    margin-top: 8px;
    margin-left: 28px;
    padding: 8px;
    background-color: #fff3cd;
    border-radius: 4px;
    font-size: 13px;
    border-left: 3px solid #ffc107;
}

.detail-notes, .detail-approval {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.detail-notes h5, .detail-approval h5 {
    color: #495057;
    margin-bottom: 10px;
    font-size: 16px;
}

.notes-content {
    background-color: white;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    white-space: pre-wrap;
}

.no-results {
    text-align: center;
    padding: 40px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status.layak {
    background-color: #28a745;
    color: white;
}

.status.perbaikan {
    background-color: #ffc107;
    color: #212529;
}

.status.tidak-layak {
    background-color: #dc3545;
    color: white;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.row {
    display: flex;
    flex-wrap: wrap;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
}

@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 15px;
    }
    
    .results-summary {
        flex-direction: column;
    }
    
    .summary-item {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        text-align: left;
    }
}
</style>