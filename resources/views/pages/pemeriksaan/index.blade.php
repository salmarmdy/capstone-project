@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header">
        <h4>Data Pemeriksaan Kendaraan</h4>
    </div>
    
    <!-- Filters -->
    <div class="filter-group">
        <div class="filter-item">
            <label for="kendaraan">Kendaraan:</label>
            <select class="form-control" id="kendaraan">
                <option value="">Semua Kendaraan</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})</option>
                @endforeach
            </select>
        </div>
        
        <div class="filter-item">
            <label for="karyawan">Karyawan:</label>
            <select class="form-control" id="karyawan">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- <div class="filter-item">
            <label for="status">Status:</label>
            <select class="form-control" id="status">
                <option value="">Semua Status</option>
                <option value="layak">Layak</option>
                <option value="perbaikan">Perlu Perbaikan</option>
                <option value="tidak-layak">Tidak Layak</option>
            </select>
        </div> -->
        
        <div class="filter-item">
            <label for="tanggal">Tanggal:</label>
            <input type="date" class="form-control" id="tanggal">
        </div>
        
        <div class="filter-item">
            <button class="btn btn-primary" onclick="filterAssessments()">
                <i class="fas fa-search"></i> Filter
            </button>
        </div>
    </div>
    
    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kendaraan</th>
                    <th>Karyawan</th>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Status Kelayakan</th>
                    <th>Jumlah Item</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="assessmentTableBody">
                @forelse($assessments as $assessment)
                    @php
                        $totalItems = $assessment->checklistResults->count();
                        $passedItems = $assessment->checklistResults->where('passed', true)->count();
                        $statusClass = '';
                        $statusText = '';
                        
                        if ($assessment->status_name) {
                            $statusText = $assessment->status_name;
                            $statusClass = strtolower(str_replace(' ', '-', $assessment->status_name));
                        } else {
                            // Fallback logic based on passed items
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
                    <tr>
                        <td>{{ $assessment->id }}</td>
                        <td>
                            @if($assessment->vehicle)
                                {{ $assessment->vehicle->brand }} {{ $assessment->vehicle->model }} 
                                ({{ $assessment->vehicle->license_plate }})
                            @else
                                <span class="text-muted">Kendaraan tidak ditemukan</span>
                            @endif
                        </td>
                        <td>
                            @if($assessment->employee)
                                {{ $assessment->employee->name }}
                            @else
                                <span class="text-muted">Karyawan tidak ditemukan</span>
                            @endif
                        </td>
                        <td>{{ $assessment->assessment_date ? $assessment->assessment_date->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="status {{ $statusClass }}" 
                                  @if($assessment->status_color_code) 
                                      style="background-color: {{ $assessment->status_color_code }}; color: white;" 
                                  @endif>
                                {{ $statusText }}
                            </span>
                        </td>
                        <td>{{ $passedItems }}/{{ $totalItems }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm action-btn" 
                                    onclick="viewAssessment('{{ $assessment->id }}')"
                                    title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <!-- @if($assessment->approved)
                                <button class="btn btn-success btn-sm action-btn" 
                                        onclick="printReport('{{ $assessment->id }}')"
                                        title="Cetak Laporan">
                                    <i class="fas fa-print"></i>
                                </button>
                            @endif -->
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data pemeriksaan</h5>
                                <p class="text-muted">Data pemeriksaan kendaraan akan muncul di sini setelah ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($assessments->hasPages())
        <div class="pagination">
            {{-- Previous Page Link --}}
            @if ($assessments->onFirstPage())
                <div class="page-item disabled">
                    <span class="page-link"><i class="fas fa-angle-double-left"></i></span>
                </div>
            @else
                <div class="page-item">
                    <a href="{{ $assessments->previousPageUrl() }}" class="page-link">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </div>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($assessments->getUrlRange(1, $assessments->lastPage()) as $page => $url)
                @if ($page == $assessments->currentPage())
                    <div class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </div>
                @else
                    <div class="page-item">
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    </div>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($assessments->hasMorePages())
                <div class="page-item">
                    <a href="{{ $assessments->nextPageUrl() }}" class="page-link">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            @else
                <div class="page-item disabled">
                    <span class="page-link"><i class="fas fa-angle-double-right"></i></span>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- View Assessment Detail Modal -->
<div id="viewAssessmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Detail Pemeriksaan Kendaraan</h3>
            <span class="close" onclick="closeModal('viewAssessmentModal')">&times;</span>
        </div>
        
        <div id="assessmentDetailContent">
            <!-- Content will be loaded via AJAX -->
            <div class="text-center p-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Memuat detail pemeriksaan...</p>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeModal('viewAssessmentModal')">Tutup</button>
            <!-- <button type="button" class="btn btn-success" onclick="printCurrentReport()" id="printBtn" style="display: none;">
                <i class="fas fa-print"></i> Cetak Laporan
            </button> -->
        </div>
    </div>
</div>

<script>
// Function to view assessment details
function viewAssessment(assessmentId) {
    document.getElementById('viewAssessmentModal').style.display = "block";
    
    // Load assessment details via AJAX
    fetch(`/admin-cms/pemeriksaan/${assessmentId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('assessmentDetailContent').innerHTML = data.html;
                document.getElementById('printBtn').style.display = data.assessment.approved ? 'inline-block' : 'none';
            } else {
                document.getElementById('assessmentDetailContent').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mb-0">${data.message || 'Gagal memuat detail pemeriksaan'}</p>
                    </div>
                `;
            }
        })
        // .catch(error => {
        //     console.error('Error:', error);
        //     document.getElementById('assessmentDetailContent').innerHTML = `
        //         <div class="alert alert-danger text-center">
        //             <i class="fas fa-exclamation-triangle"></i>
        //             <p class="mb-0">Terjadi kesalahan saat memuat data</p>
        //         </div>
        //     `;
        // });
}

// Function to close modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Function to filter assessments
function filterAssessments() {
    const kendaraan = document.getElementById('kendaraan').value;
    const karyawan = document.getElementById('karyawan').value;
    const status = document.getElementById('status').value;
    const tanggal = document.getElementById('tanggal').value;
    
    const params = new URLSearchParams();
    if (kendaraan) params.append('vehicle_id', kendaraan);
    if (karyawan) params.append('employee_id', karyawan);
    if (status) params.append('status', status);
    if (tanggal) params.append('date', tanggal);
    
    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

// Function to print report
function printReport(assessmentId) {
    window.open(`/admin-cms/pemeriksaan/${assessmentId}/report`, '_blank');
}

// Function to print current report (from modal)
let currentAssessmentId = null;
function printCurrentReport() {
    if (currentAssessmentId) {
        printReport(currentAssessmentId);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}

// Auto-refresh every 30 seconds if there are active assessments
@if($assessments->count() > 0)
    setInterval(function() {
        // Only refresh if no modal is open
        if (!document.querySelector('.modal[style*="block"]')) {
            // Soft refresh - reload table content only
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.getElementById('assessmentTableBody');
                if (newTableBody) {
                    document.getElementById('assessmentTableBody').innerHTML = newTableBody.innerHTML;
                }
            })
            .catch(error => {
                console.log('Auto-refresh failed:', error);
            });
        }
    }, 30000); // 30 seconds
@endif
</script>

<style>
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

.empty-state {
    padding: 60px 20px;
}

.filter-group {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    min-width: 150px;
}

.filter-item label {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 14px;
}

.action-btn {
    margin-right: 5px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #dee2e6;
    text-align: right;
}

.close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    gap: 5px;
}

.page-item {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.page-item.active {
    background-color: #007bff;
    border-color: #007bff;
}

.page-item.active .page-link {
    color: white;
}

.page-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-link {
    padding: 8px 12px;
    text-decoration: none;
    color: #007bff;
    display: block;
}

.page-link:hover {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .filter-group {
        flex-direction: column;
    }
    
    .filter-item {
        min-width: 100%;
    }
    
    .modal-content {
        width: 95%;
        margin: 2% auto;
    }
}
</style>

@endsection