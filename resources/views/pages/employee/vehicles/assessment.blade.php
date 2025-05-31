@extends('layouts.appv3')

@section('content')
<div class="menu-wrapper">
    <div class="main-content">
        <div class="page-header">
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="page-title">Penilaian Kendaraan</div>
            <div class="page-subtitle">Daftar periksa keselamatan dan pemeliharaan lengkap untuk kinerja kendaraan yang optimal</div>
        </div>

        <div class="form-container">
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-title">
                    <i class="fas fa-chart-line"></i>
                    Kemajuan Penilaian
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <div class="progress-stats">
                    <span id="progressText">0 of 0 completed</span>
                    <span id="progressPercentage">0% complete</span>
                </div>
            </div>

            <form id="assessmentForm" action="{{ route('pages.employee.vehicles.assessment-store') }}" method="POST">
                @csrf
                <!-- Vehicle Selection -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-car section-icon"></i>
                        Informasi Kendaraan
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Pilih Kendaraan <span class="required">*</span></label>
                            <select id="vehicle_id" name="vehicle_id" class="form-select" required>
                                <option value="">Pilih kendaraan...</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Penilaian <span class="required">*</span></label>
                            <input type="date" id="assessment_date" name="assessment_date" class="form-input" required value="{{ date('Y-m-d') }}">
                            @error('assessment_date')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Karyawan</label>
                            <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                            <input type="text" class="form-input" value="{{ auth()->user()->name ?? 'Admin' }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Checklist Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-tasks section-icon"></i>
                        Daftar Periksa Keselamatan & Pemeliharaan
                    </h3>
                    
                    <div class="checklist-grid" id="checklistGrid">
                        @foreach($checklistItems as $index => $item)
                            <div class="checklist-item">
                                <div class="checkbox-wrapper">
                                    <input type="hidden" name="checklist_results[{{ $item->id }}][item_id]" value="{{ $item->id }}">
                                    <input type="checkbox" 
                                           id="item_{{ $item->id }}" 
                                           name="checklist_results[{{ $item->id }}][passed]" 
                                           value="1"
                                           class="checkbox-input" 
                                           onchange="updateProgress()">
                                    <label for="item_{{ $item->id }}" class="checkmark">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    <div class="item-text">{{ $item->name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status Preview -->
                <div class="status-preview">
                    <div class="status-header">
                        <i class="fas fa-info-circle"></i>
                        Pratinjau Status Penilaian
                    </div>
                    <div class="status-content" id="statusContent">
                        <div class="status-pending">
                            <i class="fas fa-clock"></i>
                            Lengkapi daftar periksa untuk melihat status penilaian
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-comment-alt section-icon"></i>
                        Catatan Tambahan
                    </h3>
                    
                    <div class="form-group">
                        <label class="form-label">Komentar</label>
                        <textarea id="comments" name="comments" class="form-textarea" 
                                  placeholder="Tambahkan catatan, observasi, atau rekomendasi..."></textarea>
                    </div>
                </div>

                <!-- Hidden fields for status -->
                <input type="hidden" name="approved" id="approved_status" value="0">
                <input type="hidden" name="status_name" id="status_name" value="PENDING">
                <input type="hidden" name="status_description" id="status_description" value="">

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Kendaraan
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-check-circle"></i>
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Floating Progress Indicator -->
<div class="floating-progress">
    <div class="floating-circle">
        <div class="floating-fill" id="floatingFill"></div>
    </div>
    <span id="floatingText">0/{{ count($checklistItems) }}</span>
</div>

<script>
    let checkedCount = 0;
    const totalItems = {{ count($checklistItems) }};

    // Initialize the form
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers to checklist items
        document.querySelectorAll('.checklist-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    updateProgress();
                }
            });
        });
        
        // Update initial progress
        updateProgress();
    });

    function updateProgress() {
        const checkboxes = document.querySelectorAll('.checkbox-input');
        checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const percentage = totalItems > 0 ? Math.round((checkedCount / totalItems) * 100) : 0;
        
        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = percentage + '%';
        
        // Update progress text
        document.getElementById('progressText').textContent = `${checkedCount} of ${totalItems} completed`;
        document.getElementById('progressPercentage').textContent = `${percentage}% complete`;
        
        // Update floating progress
        document.getElementById('floatingFill').style.width = percentage + '%';
        document.getElementById('floatingText').textContent = `${checkedCount}/${totalItems}`;
        
        // Update checklist item styling
        checkboxes.forEach((checkbox) => {
            const item = checkbox.closest('.checklist-item');
            if (checkbox.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        });
        
        // Update status preview and hidden fields
        updateStatusPreview();
    }

    function updateStatusPreview() {
        const statusContent = document.getElementById('statusContent');
        const approvedStatus = document.getElementById('approved_status');
        const statusName = document.getElementById('status_name');
        const statusDescription = document.getElementById('status_description');
        
        let statusHtml = '';
        
        if (checkedCount === 0) {
            statusHtml = `
                <div class="status-pending">
                    <i class="fas fa-clock"></i>
                    Lengkapi daftar periksa untuk melihat status penilaian
                </div>
            `;
            approvedStatus.value = '0';
            statusName.value = 'PENDING';
            statusDescription.value = 'Assessment belum dimulai';
        } else if (checkedCount === totalItems) {
            statusHtml = `
                <div class="status-approved">
                    <i class="fas fa-check-circle"></i>
                    Penilaian akan ditandai sebagai <strong>DISETUJUI</strong> ✨
                </div>
            `;
            approvedStatus.value = '1';
            statusName.value = 'APPROVED';
            statusDescription.value = 'Semua item checklist berhasil dilewati';
        } else {
            const failedItems = totalItems - checkedCount;
            statusHtml = `
                <div class="status-attention">
                    <i class="fas fa-exclamation-triangle"></i>
                    Penilaian akan membutuhkan <strong>PERHATIAN</strong> (${failedItems} item${failedItems > 1 ? 's' : ''} gagal)
                </div>
            `;
            approvedStatus.value = '0';
            statusName.value = 'NEEDS_ATTENTION';
            statusDescription.value = `${failedItems} dari ${totalItems} item checklist gagal`;
        }
        
        statusContent.innerHTML = statusHtml;
    }

    // Form submission
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        const vehicleSelect = document.getElementById('vehicle_id');
        const submitBtn = document.getElementById('submitBtn');
        
        if (!vehicleSelect.value) {
            e.preventDefault();
            alert('Silakan pilih kendaraan sebelum menyimpan penilaian.');
            vehicleSelect.focus();
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        // Form will be submitted normally to the server
    });

    // Back button functionality
    function goBack() {
        if (confirm('Apakah Anda yakin ingin kembali? Perubahan yang belum disimpan akan hilang.')) {
            window.history.back();
        }
    }

    // Add some interactive animations
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            }
        });
    });

    // Show success/error messages if they exist
    @if(session('success'))
        alert('✅ {{ session('success') }}');
    @endif

    @if(session('error'))
        alert('❌ {{ session('error') }}');
    @endif
</script>

@endsection