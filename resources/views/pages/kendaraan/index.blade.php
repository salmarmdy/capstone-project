@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Data Kendaraan</h4>
        <button class="btn btn-primary" onclick="openModal('addKendaraanModal')">
            <i class="fas fa-plus"></i> Tambah Kendaraan
        </button>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plat Nomor</th>
                    <th>Karyawan</th>
                    <th>Tipe Kendaraan</th>
                    <th>Merek</th>
                    <th>Model</th>
                    <th>Kapasitas Mesin</th>
                    <th>Tgl Exp. STNK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vehicles as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->license_plate }}</td>
                    <td>{{ $v->employee->name ?? 'Tidak Ada' }}</td>
                    <td>{{ $v->vehicle_type }}</td>
                    <td>{{ $v->brand }}</td>
                    <td>{{ $v->model }}</td>
                    <td>{{ number_format($v->engine_capacity) }} cc</td>
                    <td>{{ $v->license_expiry->format('d/m/Y') }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm action-btn" 
                                onclick="confirmDelete({{ $v->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                       <!-- <a href="{{ route('pages.kendaraan.destroy', $v->id) }}" class="btn btn-sm btn-danger">Hapus</a> -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Modal Tambah Kendaraan -->
<div id="addKendaraanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Kendaraan</h3>
            <span class="close" onclick="closeModal('addKendaraanModal')">&times;</span>
        </div>
        <form action="{{ route('pages.kendaraan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <label for="employee_id">Karyawan</label>
                <select id="employee_id" name="employee_id" class="custom-dropdown" required>
                    <option value="">Pilih Karyawan</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label for="license_plate">Plat Nomor</label>
                <input type="text" id="license_plate" name="license_plate" placeholder="Masukkan plat nomor" required>
            </div>
            <div class="form-row">
                <label for="vehicle_type">Tipe Kendaraan</label>
                <select id="vehicle_type" name="vehicle_type" class="custom-dropdown" required>
                    <option value="Mobil">Mobil</option>
                    <option value="Motor">Motor</option>
                    <option value="Truk">Truk</option>
                    <option value="Bus">Bus</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-row">
                <label for="brand">Merek</label>
                <input type="text" id="brand" name="brand" placeholder="Masukkan merek kendaraan" required>
            </div>
            <div class="form-row">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" placeholder="Masukkan model kendaraan" required>
            </div>
            <div class="form-row">
                <label for="engine_capacity">Kapasitas Mesin (cc)</label>
                <input type="number" id="engine_capacity" name="engine_capacity" placeholder="Masukkan kapasitas mesin" required>
            </div>
            <div class="form-row">
                <label for="license_expiry">Tanggal Kadaluarsa STNK</label>
                <input type="date" id="license_expiry" name="license_expiry" required>
            </div>
            <div class="form-row">
                <label for="license_document">Dokumen STNK</label>
                <input type="file" id="license_document" name="license_document">
                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maks: 2MB</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('addKendaraanModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kendaraan -->
<div id="editKendaraanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Kendaraan</h3>
            <span class="close" onclick="closeModal('editKendaraanModal')">&times;</span>
        </div>
        <form id="editKendaraanForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_vehicle_id" name="id">
            <div class="form-row">
                <label for="edit_employee_id">Karyawan</label>
                <select id="edit_employee_id" name="employee_id" class="custom-dropdown" required>
                    <option value="">Pilih Karyawan</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label for="edit_license_plate">Plat Nomor</label>
                <input type="text" id="edit_license_plate" name="license_plate" required>
            </div>
            <div class="form-row">
                <label for="edit_vehicle_type">Tipe Kendaraan</label>
                <select id="edit_vehicle_type" name="vehicle_type" class="custom-dropdown" required>
                    <option value="Mobil">Mobil</option>
                    <option value="Motor">Motor</option>
                    <option value="Truk">Truk</option>
                    <option value="Bus">Bus</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-row">
                <label for="edit_brand">Merek</label>
                <input type="text" id="edit_brand" name="brand" required>
            </div>
            <div class="form-row">
                <label for="edit_model">Model</label>
                <input type="text" id="edit_model" name="model" required>
            </div>
            <div class="form-row">
                <label for="edit_engine_capacity">Kapasitas Mesin (cc)</label>
                <input type="number" id="edit_engine_capacity" name="engine_capacity" required>
            </div>
            <div class="form-row">
                <label for="edit_license_expiry">Tanggal Kadaluarsa STNK</label>
                <input type="date" id="edit_license_expiry" name="license_expiry" required>
            </div>
            <div class="form-row">
                <label for="edit_license_document">Dokumen STNK</label>
                <input type="file" id="edit_license_document" name="license_document">
                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maks: 2MB</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('editKendaraanModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(id) {
        if (confirm("Apakah Anda yakin ingin menghapus kendaraan ini?")) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin-cms/kendaraan/${id}`;
            form.submit();
        }
    }
    
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Klik di luar modal untuk menutup
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }

    function editKendaraan(id, employee_id, license_plate, vehicle_type, brand, model, engine_capacity, license_expiry) {
        // Set the form action URL with the vehicle ID
        document.getElementById('editKendaraanForm').action = '/admin-cms/kendaraan/' + id;
        
        // Fill the form fields with vehicle data
        document.getElementById('edit_vehicle_id').value = id;
        document.getElementById('edit_employee_id').value = employee_id;
        document.getElementById('edit_license_plate').value = license_plate;
        document.getElementById('edit_vehicle_type').value = vehicle_type;
        document.getElementById('edit_brand').value = brand;
        document.getElementById('edit_model').value = model;
        document.getElementById('edit_engine_capacity').value = engine_capacity;
        document.getElementById('edit_license_expiry').value = license_expiry;
        
        // Open the modal
        openModal('editKendaraanModal');
    }
</script>