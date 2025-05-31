@extends('layouts.appv3')

@section('content')
    <div class="menu-wrapper">
        <div class="main-content">
            <div class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <div class="page-title">Daftarkan Kendaraan Baru</div>
                <div class="page-subtitle">Tambahkan kendaraan baru ke sistem manajemen</div>
            </div>

            <div class="form-container">
                <div class="progress-bar2">
                    <div class="progress-fill" id="progressFill"></div>
                </div>

                <form id="vehicleForm" action="{{ route('pages.employee.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Vehicle Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-car section-icon"></i>
                            Informasi Kendaraan
                        </h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Nomer Plat <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" class="form-input" id="licensePlate" name="license_plate" placeholder="e.g., B 1234 XYZ" required>
                                </div>
                                <div class="form-hint">Masukkan nomor plat kendaraan</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jenis Kendaraan <span class="required">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-input" id="vehicleType" name="vehicle_type" placeholder="Jenis Kendaraan" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Merek <span class="required">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-input" id="brand" placeholder="Merek" name="brand" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Model <span class="required">*</span></label>
                                <input type="text" class="form-input" id="model" name="model" placeholder="e.g., Corolla, Civic, PCX" required>
                            </div>

                            <!-- <div class="form-group">
                                <label class="form-label">Tahun <span class="required">*</span></label>
                                <input type="number" class="form-input" id="year" min="1990" max="2025" placeholder="e.g., 2021" required>
                            </div> -->

                            <!-- <div class="form-group">
                                <label class="form-label">Kapasitas Mesin (CC)</label>
                                <input type="number" class="form-input" id="engineCC" name="engine_capacity" placeholder="e.g., 1500, 150">
                            </div> -->
                        </div>
                    </div>

                    <!-- Registration Documents Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-file-alt section-icon"></i>
                            Dokumen Pendaftaran
                        </h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Vehicle Registration (STNK)</label>
                                <input type="date" class="form-input" name="license_expiry">
                                <div class="form-hint">STNK expiration date</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Jatuh Tempo Pajak Kendaraan Bermotor</label>
                                <input type="date" class="form-input" id="taxDue">
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">Unggah Dokumen</label>
                                <div class="file-upload">
                                    <input type="file" class="file-input" id="documents" name="license_document_path" accept="image/*,.pdf" onchange="handleFileUpload(this)">
                                    <label for="documents" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Click to upload or drag and drop</span>
                                    </label>
                                </div>
                                <div class="form-hint">Unggah STNK (PDF, JPG, PNG)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes Section -->
                    <!-- <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-sticky-note section-icon"></i>
                            Additional Information
                        </h3>
                        
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" id="notes" placeholder="Any additional notes about the vehicle..."></textarea>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" class="checkbox" id="termsAccepted" required>
                            <label for="termsAccepted" class="checkbox-label">
                                I confirm that all information provided is accurate and I agree to the terms and conditions <span class="required">*</span>
                            </label>
                        </div>
                    </div> -->

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Daftarkan Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form progress tracking
        function updateProgress() {
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            const filledFields = Array.from(requiredFields).filter(field => field.value.trim() !== '');
            const progress = (filledFields.length / requiredFields.length) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }

        // Add event listeners to required fields
        document.addEventListener('DOMContentLoaded', function() {
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(field => {
                field.addEventListener('input', updateProgress);
                field.addEventListener('change', updateProgress);
            });
        });

        // Vehicle type icon update
        function updateVehicleIcon() {
            // This could update an icon preview based on selected vehicle type
            updateProgress();
        }

        // File upload handler
        function handleFileUpload(input) {
            const files = input.files;
            const preview = input.parentElement.nextElementSibling;
            
            if (files.length > 0) {
                let fileNames = Array.from(files).map(f => f.name).join(', ');
                if (!preview.querySelector('.file-preview')) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'file-preview';
                    previewDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${files.length} file(s) selected: ${fileNames}`;
                    preview.appendChild(previewDiv);
                } else {
                    preview.querySelector('.file-preview').innerHTML = `<i class="fas fa-check-circle"></i> ${files.length} file(s) selected: ${fileNames}`;
                }
                preview.querySelector('.file-preview').style.display = 'block';
            }
        }

        // Form submission handler
        function handleSubmit(event) {
            event.preventDefault();
            
            // Simulate form submission
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert('Vehicle registered successfully!');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                // Here you would typically redirect or reset the form
            }, 2000);
        }

        // Reset form functionality
        function resetForm() {
            if (confirm('Are you sure you want to reset all form data?')) {
                document.getElementById('vehicleForm').reset();
                document.getElementById('progressFill').style.width = '0%';
                
                // Clear file previews
                document.querySelectorAll('.file-preview').forEach(preview => {
                    preview.style.display = 'none';
                });
            }
        }

        // Back button functionality
        function goBack() {
            if (confirm('Are you sure you want to go back? Any unsaved changes will be lost.')) {
                // In a real application, this would navigate back
                window.history.back();
            }
        }

        // Auto-format phone number
        document.getElementById('phoneNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('62')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+62 ' + value.substring(1);
            }
            e.target.value = value;
        });

        // License plate formatting
        document.getElementById('licensePlate').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
@endsection
