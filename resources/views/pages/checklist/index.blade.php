@extends('layouts.app')
@section('content')

<!-- Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h4>Data Item Checklist</h4>
        <button class="btn btn-primary" onclick="openModal('addCheckModal')">
            <i class="fas fa-plus"></i> Tambah Item
        </button>
    </div>
    
    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklistItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm action-btn" 
                                onclick="editItem({{ $item->id }}, '{{ $item->name }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm action-btn" 
                                onclick="confirmDelete({{ $item->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        {{ $checklistItems->links() }}
    </div>
</div>

<!-- Add Item Modal -->
<div id="addCheckModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Item</h3>
            <span class="close" onclick="closeModal('addCheckModal')">&times;</span>
        </div>
        <form action="{{ route('pages.checklist.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <label for="add_name">Item</label>
                <input type="text" id="add_name" name="name" placeholder="Masukkan item" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('addCheckModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="editCheckModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Item</h3>
            <span class="close" onclick="closeModal('editCheckModal')">&times;</span>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('POST')
            <input type="hidden" id="edit_item_id" name="id">
            <div class="form-row">
                <label for="edit_name">Item</label>
                <input type="text" id="edit_name" name="name" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeModal('editCheckModal')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Fungsi untuk membuka modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }
    
    // Fungsi untuk menutup modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
        // Reset form
        if (modalId === 'addCheckModal') {
            document.getElementById('add_name').value = '';
        }
    }
    
    // Fungsi untuk edit item
    function editItem(id, name) {
        document.getElementById('edit_item_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('editForm').action = `/admin-cms/item-checklist/${id}`;
        openModal('editCheckModal');
    }
    
    // Fungsi konfirmasi hapus
    function confirmDelete(id) {
        if (confirm("Apakah Anda yakin ingin menghapus item checklist ini?")) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin-cms/item-checklist/${id}`;
            form.submit();
        }
    }
    
    // Menutup modal saat mengklik di luar modal
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }

    // Tutup alert setelah 3 detik
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
</script>

@endsection