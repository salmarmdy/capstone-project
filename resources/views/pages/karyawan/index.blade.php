@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Data Karyawan</h4>
        <button class="btn btn-primary" onclick="openModal('addKaryawanModal')">
            <i class="fas fa-plus"></i> Tambah Karyawan
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('login_info'))
        <div class="alert alert-info">
            <h5><i class="fas fa-key"></i> Akun Login Berhasil Dibuat</h5>
            <hr>
            <strong>Nama:</strong> {{ session('login_info.employee_name') }}<br>
            <strong>Username:</strong> <code>{{ session('login_info.username') }}</code><br>
            <strong>Password:</strong> <code>{{ session('login_info.password') }}</code><br>
            <small class="text-muted">Harap catat informasi login ini.</small>
        </div>
    @endif

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Akun Login</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $k)
                <tr>
                    <td>{{ $k->id }}</td>
                    <td>{{ $k->name }}</td>
                    <td>{{ $k->department }}</td>
                    <td>{{ $k->address }}</td>
                    <td>{{ $k->email }}</td>
                    <td>{{ $k->phone_number }}</td>
                    <td>
                        <span class="badge {{ $k->employment_status === 'Tetap' ? 'badge-success' : 'badge-warning' }}">
                            {{ $k->employment_status }}
                        </span>
                    </td>
                    <td>
                        @php
                            $loginInfo = $k->getLoginCredentials();
                        @endphp
                        @if($loginInfo['has_account'])
                            <span class="badge badge-success">
                                <i class="fas fa-user-check"></i> {{ $loginInfo['username'] }}
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-user-times"></i> Belum ada
                            </span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm action-btn" onclick="editKaryawan('{{ $k->id }}', '{{ $k->name }}', '{{ $k->department }}', '{{ $k->address }}', '{{ $k->email }}', '{{ $k->phone_number }}', '{{ $k->sim_number }}', '{{ $k->sim_expiry_date }}', '{{ $k->employment_status }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="{{ route('employee-delete', $k->id) }}" class="btn btn-danger btn-sm action-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        @if(!$loginInfo['has_account'])
                            <button class="btn btn-success btn-sm action-btn" title="Buat Akun Login" onclick="createUserAccount({{ $k->id }}, '{{ $k->name }}')">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Modal Tambah Karyawan -->
<div id="addKaryawanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Karyawan</h3>
            <span class="close" onclick="closeModal('addKaryawanModal')">&times;</span>
        </div>
        <form action="{{ route('employee-create') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama karyawan" required>
            </div>
            <div class="form-row">
                <label for="department">Departemen</label>
                <select id="department" name="department" class="custom-dropdown" required>
                    <option value="IT">IT</option>
                    <option value="Finance">Finance</option>
                    <option value="HR">HR</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operations">Operations</option>
                </select>
            </div>
            <div class="form-row">
                <label for="address">Alamat</label>
                <input type="text" id="address" name="address" placeholder="Masukkan alamat" required>
            </div>
            <div class="form-row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-row">
                <label for="phone_number">Nomor Telepon</label>
                <input type="text" id="phone_number" name="phone_number" placeholder="Masukkan nomor telepon" required>
            </div>
            <div class="form-row">
                <label for="sim_number">Nomor SIM</label>
                <input type="text" id="sim_number" name="sim_number" placeholder="Masukkan nomor SIM">
            </div>
            <div class="form-row">
                <label for="sim_expiry_date">Tanggal Kadaluarsa SIM</label>
                <input type="date" id="sim_expiry_date" name="sim_expiry_date">
            </div>
            <div class="form-row">
                <label for="employment_status">Status</label>
                <select id="employment_status" name="employment_status" required>
                    <option value="Tetap">Tetap</option>
                    <option value="Kontrak">Kontrak</option>
                </select>
            </div>
            <div class="form-row">
                <label>
                    <input type="checkbox" id="create_account" name="create_account" value="1">
                    Buat akun login untuk karyawan
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('addKaryawanModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="editKaryawanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Karyawan</h3>
            <span class="close" onclick="closeModal('editKaryawanModal')">&times;</span>
        </div>
        <form id="editKaryawanForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id">
            <div class="form-row">
                <label for="edit_name">Nama</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div class="form-row">
                <label for="edit_department">Departemen</label>
                <select id="edit_department" name="department" required>
                    <option value="IT">IT</option>
                    <option value="Finance">Finance</option>
                    <option value="HR">HR</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operations">Operations</option>
                </select>
            </div>
            <div class="form-row">
                <label for="edit_address">Alamat</label>
                <input type="text" id="edit_address" name="address" required>
            </div>
            <div class="form-row">
                <label for="edit_email">Email</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-row">
                <label for="edit_phone_number">Nomor Telepon</label>
                <input type="text" id="edit_phone_number" name="phone_number" required>
            </div>
            <div class="form-row">
                <label for="edit_sim_number">Nomor SIM</label>
                <input type="text" id="edit_sim_number" name="sim_number">
            </div>
            <div class="form-row">
                <label for="edit_sim_expiry_date">Tanggal Kadaluarsa SIM</label>
                <input type="date" id="edit_sim_expiry_date" name="sim_expiry_date">
            </div>
            <div class="form-row">
                <label for="edit_employment_status">Status</label>
                <select id="edit_employment_status" name="employment_status" required>
                    <option value="Tetap">Tetap</option>
                    <option value="Kontrak">Kontrak</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('editKaryawanModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $employees->links() }}
    </div>
</div>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

.alert-info {
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
}

.badge {
    padding: 3px 7px;
    font-size: 75%;
    border-radius: 10px;
    color: white;
}

.badge-success {
    background-color: #5cb85c;
}

.badge-warning {
    background-color: #f0ad4e;
}
</style>

<script>
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

    function editKaryawan(id, name, department, address, email, phone_number, sim_number, sim_expiry_date, employment_status) {
        // Set the form action URL with the employee ID
        document.getElementById('editKaryawanForm').action = '/admin-cms/karyawan/' + id;
        
        // Fill the form fields with employee data
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_department').value = department;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_phone_number').value = phone_number;
        document.getElementById('edit_sim_number').value = sim_number;
        document.getElementById('edit_sim_expiry_date').value = sim_expiry_date;
        document.getElementById('edit_employment_status').value = employment_status;
        
        // Open the modal
        openModal('editKaryawanModal');
    }

    function createUserAccount(id, name) {
        if (confirm(`Buat akun login untuk ${name}?`)) {
            fetch(`/admin-cms/karyawan/${id}/create-account`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Akun berhasil dibuat!\nUsername: ${data.username}\nPassword: ${data.password}`);
                    location.reload();
                } else {
                    alert('Gagal membuat akun: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat membuat akun');
            });
        }
    }

    // Auto hide alerts after 10 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 10000);
</script>
@endsection