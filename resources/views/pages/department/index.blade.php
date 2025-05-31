@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Data Departemen</h4>
        <button class="btn btn-primary" onclick="openModal('addDepartmentModal')">
            <i class="fas fa-plus"></i> Tambah Departemen
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama Departemen</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Karyawan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $dept)
                <tr>
                    <td>{{ $dept->id }}</td>
                    <td><code>{{ $dept->code }}</code></td>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->description ?? '-' }}</td>
                    <td>
                        <span class="badge badge-info">
                            {{ $dept->employees_count }} orang
                        </span>
                    </td>
                    <td>
                        <button class="badge {{ $dept->status ? 'badge-success' : 'badge-secondary' }} status-toggle" 
                                onclick="toggleStatus({{ $dept->id }})"
                                style="border: none; cursor: pointer;">
                            <i class="fas {{ $dept->status ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $dept->status ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm action-btn" 
                                onclick="editDepartment('{{ $dept->id }}', '{{ $dept->code }}', '{{ $dept->name }}', '{{ $dept->description }}', {{ $dept->status ? 'true' : 'false' }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <!-- <a href="{{ route('department-show', $dept->id) }}" class="btn btn-info btn-sm action-btn" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a> -->
                        <a href="{{ route('department-delete', $dept->id) }}" 
                           class="btn btn-danger btn-sm action-btn" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus departemen ini?{{ $dept->employees_count > 0 ? ' Departemen ini memiliki ' . $dept->employees_count . ' karyawan.' : '' }}')"
                           {{ $dept->employees_count > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Modal Tambah Departemen -->
<div id="addDepartmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Departemen</h3>
            <span class="close" onclick="closeModal('addDepartmentModal')">&times;</span>
        </div>
        <form action="{{ route('department-create') }}" method="post">
            @csrf
            <div class="form-row">
                <label for="code">Kode Departemen</label>
                <input type="text" id="code" name="code" placeholder="Contoh: IT, HR, FIN" maxlength="10" required>
                <small class="text-muted">Maksimal 10 karakter</small>
            </div>
            <div class="form-row">
                <label for="name">Nama Departemen</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama departemen" required>
            </div>
            <div class="form-row">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" placeholder="Deskripsi departemen (opsional)"></textarea>
            </div>
            <div class="form-row">
                <label>
                    <input type="checkbox" id="status" name="status" value="1" checked>
                    Status Aktif
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('addDepartmentModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Departemen -->
<div id="editDepartmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Departemen</h3>
            <span class="close" onclick="closeModal('editDepartmentModal')">&times;</span>
        </div>
        <form id="editDepartmentForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id">
            <div class="form-row">
                <label for="edit_code">Kode Departemen</label>
                <input type="text" id="edit_code" name="code" maxlength="10" required>
            </div>
            <div class="form-row">
                <label for="edit_name">Nama Departemen</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div class="form-row">
                <label for="edit_description">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3"></textarea>
            </div>
            <div class="form-row">
                <label>
                    <input type="checkbox" id="edit_status" name="status" value="1">
                    Status Aktif
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('editDepartmentModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $departments->links() }}
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

.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
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

.badge-secondary {
    background-color: #6c757d;
}

.badge-info {
    background-color: #5bc0de;
}

.status-toggle:hover {
    opacity: 0.8;
}

.text-muted {
    color: #6c757d;
    font-size: 0.875em;
}

textarea {
    resize: vertical;
    min-height: 80px;
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

    function editDepartment(id, code, name, description, status) {
        // Set the form action URL with the department ID
        document.getElementById('editDepartmentForm').action = '/admin-cms/departemen/' + id;
        
        // Fill the form fields with department data
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_code').value = code;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_status').checked = status;
        
        // Open the modal
        openModal('editDepartmentModal');
    }

    function toggleStatus(id) {
        if (confirm('Apakah Anda yakin ingin mengubah status departemen ini?')) {
            fetch(`/admin-cms/departemen/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat mengubah status');
                console.error('Error:', error);
            });
        }
    }

    // Auto hide alerts after 8 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 8000);

    // Auto uppercase code input
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    document.getElementById('edit_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endsection