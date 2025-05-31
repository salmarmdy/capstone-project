@extends('layouts.appv3')

@section('content')
<div class="menu-wrapper">
    <div class="main-content">
        <div class="page-header">
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="page-title">Penilaian Kendaraan</div>
            <div class="page-subtitle">Buat hasil penilaian keselamatan dan pemeliharaan kendaraan</div>
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

            <form id="assessmentForm" 
                  action="{{ isset($assessment) ? route('pages.employee.vehicles.assessment-update', $assessment->id) : route('pages.employee.vehicles.assessment-store') }}" 
                  method="POST">
                @csrf
                @if(isset($assessment))
                    @method('PUT')
                @endif
                
                <!-- Vehicle Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-car section-icon"></i>
                        Informasi Kendaraan
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Kendaraan <span class="required">*</span></label>
                            @if(isset($assessment))
                                <input type="hidden" name="vehicle_id" value="{{ $assessment->vehicle_id }}">
                                <input type="text" class="form-input" 
                                       value="{{ $assessment->vehicle->license_plate }} - {{ $assessment->vehicle->brand }} {{ $assessment->vehicle->model }}" 
                                       readonly>
                            @else
                                <select name="vehicle_id" class="form-input" required>
                                    <option value="">Pilih Kendaraan</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Penilaian <span class="required">*</span></label>
                            <input type="date" id="assessment_date" name="assessment_date" class="form-input" required 
                                   value="{{ isset($assessment) && $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : old('assessment_date', date('Y-m-d')) }}">
                            @error('assessment_date')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Karyawan</label>
                            @if(isset($assessment))
                                <input type="hidden" name="employee_id" value="{{ $assessment->employee_id }}">
                                <input type="text" class="form-input" 
                                       value="{{ $assessment->employee->user->name ?? 'Admin' }}" readonly>
                            @else
                                <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                                <input type="text" class="form-input" 
                                       value="{{ $userName }}" readonly>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Assessment Info (only for edit mode) -->
                @if(isset($assessment))
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle section-icon"></i>
                        Informasi Penilaian Saat Ini
                    </h3>
                    
                    <div class="current-status-card">
                        <div class="status-row">
                            <span class="status-label">Status:</span>
                            <span class="status-badge status-{{ strtolower($assessment->status_name) }}">
                                {{ $assessment->status_name }}
                            </span>
                        </div>
                        <div class="status-row">
                            <span class="status-label">Dibuat:</span>
                            <span>{{ $assessment->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($assessment->updated_at != $assessment->created_at)
                        <div class="status-row">
                            <span class="status-label">Terakhir Diperbarui:</span>
                            <span>{{ $assessment->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Checklist Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-tasks section-icon"></i>
                        Daftar Periksa Keselamatan & Pemeliharaan
                    </h3>
                    
                    <div class="checklist-grid" id="checklistGrid">
                        @foreach($checklistItems as $index => $item)
                            @php
                                // Check if this item is passed in existing assessment
                                $isPassed = false;
                                if (isset($assessment) && $assessment->checklistResults) {
                                    $result = $assessment->checklistResults->where('checklist_items_id', $item->id)->first();
                                    $isPassed = $result ? $result->passed == 1 : false;
                                }
                            @endphp
                            <div class="checklist-item {{ $isPassed ? 'checked' : '' }}">
                                <div class="checkbox-wrapper">
                                    <input type="hidden" name="checklist_results[{{ $index }}][item_id]" value="{{ $item->id }}">
                                    <input type="checkbox" 
                                           id="item_{{ $item->id }}" 
                                           name="checklist_results[{{ $index }}][passed]" 
                                           value="1"
                                           class="checkbox-input" 
                                           {{ $isPassed ? 'checked' : '' }}
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
                            Memuat status penilaian...
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
                                  placeholder="Tambahkan catatan, observasi, atau rekomendasi...">{{ isset($assessment) ? $assessment->comments : old('comments') }}</textarea>
                    </div>
                </div>

                <!-- Hidden fields for status -->
                <input type="hidden" name="approved" id="approved_status" value="{{ isset($assessment) ? ($assessment->approved ? '1' : '0') : '0' }}">
                <input type="hidden" name="status_name" id="status_name" value="{{ isset($assessment) ? $assessment->status_name : 'PENDING' }}">
                <input type="hidden" name="status_description" id="status_description" value="{{ isset($assessment) ? $assessment->status_description : 'Assessment belum dimulai' }}">

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        {{ isset($assessment) ? 'Perbarui' : 'Simpan' }} Penilaian
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
    const isEditMode = {{ isset($assessment) ? 'true' : 'false' }};

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
        const submitBtn = document.getElementById('submitBtn');
        
        // Show loading state
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${isEditMode ? 'Memperbarui' : 'Menyimpan'}...`;
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

<style>
.current-status-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.status-row:last-child {
    margin-bottom: 0;
}

.status-label {
    font-weight: 600;
    color: #495057;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-approved {
    background-color: #d4edda;
    color: #155724;
}

.status-needs_attention {
    background-color: #fff3cd;
    color: #856404;
}

.status-pending {
    background-color: #d1ecf1;
    color: #0c5460;
}

.error-text {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.required {
    color: #dc3545;
}

/* Additional styles for form elements */
.form-input, .form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-input:focus, .form-textarea:focus {
    outline: 0;
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #212529;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection