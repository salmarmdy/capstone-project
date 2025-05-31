@extends('layouts.appv3')

@section('content')
    <div class="menu-wrapper">
        <div class="main-content">
            <div class="page-header">
                <div class="page-title">Manajemen kendaraan</div>
                <div class="page-subtitle">Kelola dan pantau armada kendaraan Anda</div>
            </div>

            <div class="page-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search vehicles..." id="searchInput">
                </div>
                <a href="/vehicles/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Register New Vehicle
                </a>
            </div>

            <div class="tab-menu">
                <div class="tab-item active">All Vehicles</div>
                <!-- <div class="tab-item">Assessment Due (1)</div>
                <div class="tab-item">Active (1)</div> -->
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Type & Specs</th>
                            <th>Status</th>
                            <th>Assessment</th>
                            <th>License Expiry</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $v)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-motorcycle vehicle-icon"></i>
                                    <div>
                                        <div class="vehicle-plate">{{ $v->license_plate }}</div>
                                        <div class="vehicle-model">{{ $v->brand }} (2019)</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $v->model }}</div>
                                <div style="color: #6b7280; font-size: 13px;">{{ $v->engine_capacity }} cc • {{ $v->brand }}</div>
                            </td>
                            <td>
                                <span class="status-badge status-active">
                                    <span class="status-indicator status-green"></span>
                                    Active
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="status-indicator status-green"></span>
                                    <span style="font-size: 13px; color: #059669;">Approved</span>
                                </div>
                            </td>
                            <td>
                                <div class="expiry-info expiry-normal">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <div>{{ $v->license_expiry->format('d M Y') }}</div>
                                        <!-- <div style="font-size: 12px; color: #6b7280;">10 months left</div> -->
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('/vehicles/assessment/'.$v->id.'/edit') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-clipboard-check"></i> Assessment
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        <!-- <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-car vehicle-icon"></i>
                                    <div>
                                        <div class="vehicle-plate">B 1234 XYZ</div>
                                        <div class="vehicle-model">Toyota Corolla (2019)</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>Sedan</div>
                                <div style="color: #6b7280; font-size: 13px;">1800 cc • Toyota</div>
                            </td>
                            <td>
                                <span class="status-badge status-active">
                                    <span class="status-indicator status-green"></span>
                                    Active
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="status-indicator status-green"></span>
                                    <span style="font-size: 13px; color: #059669;">Approved</span>
                                </div>
                            </td>
                            <td>
                                <div class="expiry-info expiry-normal">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <div>March 15, 2026</div>
                                        <div style="font-size: 12px; color: #6b7280;">10 months left</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-clipboard-check"></i> Assessment
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-file-alt"></i> Docs
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-motorcycle vehicle-icon"></i>
                                    <div>
                                        <div class="vehicle-plate">B 5678 ABC</div>
                                        <div class="vehicle-model">Honda PCX (2021)</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>Motorcycle</div>
                                <div style="color: #6b7280; font-size: 13px;">150 cc • Honda</div>
                            </td>
                            <td>
                                <span class="status-badge status-pending">
                                    <span class="status-indicator status-yellow"></span>
                                    Assessment Due
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="status-indicator status-yellow"></span>
                                    <span style="font-size: 13px; color: #d97706;">Due in 3 days</span>
                                </div>
                            </td>
                            <td>
                                <div class="expiry-info expiry-warning">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <div>June 30, 2025</div>
                                        <div style="font-size: 12px;">1 month left</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    <button class="btn btn-warning btn-sm">
                                        <i class="fas fa-exclamation-circle"></i> Self-Check
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-file-alt"></i> Docs
                                    </button>
                                </div>
                            </td>
                        </tr> -->
                    </tbody>
                </table>

                <!-- <div class="upcoming-expirations">
                    <div class="expiry-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        Kedaluwarsa Mendatang
                    </div>
                    
                    <div class="expiry-item">
                        <div class="expiry-details">
                            <i class="fas fa-calendar-alt expiry-icon"></i>
                            <div>
                                <div class="expiry-text">STNK (Honda Scoopy)</div>
                                <div class="expiry-date">Expires on June 30, 2025 (1 month left)</div>
                            </div>
                        </div>
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-upload"></i> Update
                        </button>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            
            tableRows.forEach(row => {
                const vehicleInfo = row.textContent.toLowerCase();
                if (vehicleInfo.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Tab functionality
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Here you can add filtering logic based on the selected tab
                // For now, it just changes the visual state
            });
        });

        // Button click handlers
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function() {
                // Add a small animation effect
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
@endsection
