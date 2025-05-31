@extends('layouts.app')
@section('content')
<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Data STNK</h4>
            <button class="btn btn-primary" onclick="openModal('addStnkModal')">
                <i class="fas fa-plus"></i> Tambah STNK
            </button>
        </div>

        <!-- Filter -->
        <div class="filter-group">
            <div class="filter-item">
                <label for="filter-kendaraan-stnk">Kendaraan:</label>
                <select class="form-control" id="filter-kendaraan-stnk">
                    <option value="">Semua Kendaraan</option>
                    <option value="KD001">Toyota Avanza (B 1234 KJL)</option>
                    <option value="KD002">Honda HRV (B 5678 ABC)</option>
                    <option value="KD003">Mitsubishi Xpander (B 9012 DEF)</option>
                    <option value="KD004">Suzuki Ertiga (B 3456 GHI)</option>
                    <option value="KD005">Daihatsu Terios (B 7890 JKL)</option>
                </select>
            </div>
            <div>
                <label for="filter-status-stnk">Status:</label>
                <select class="form-control" id="filter-status-stnk">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="warning">Segera Habis</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary">
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
                        <th>Nomor STNK</th>
                        <th>Tanggal Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stnks = [
                            ['id' => 'S001', 'kendaraan' => 'Toyota Avanza (B 1234 KJL)', 'nomor' => 'STN-1234-5678', 'berlaku' => '31 Desember 2025', 'status' => 'aktif'],
                            ['id' => 'S002', 'kendaraan' => 'Honda HRV (B 5678 ABC)', 'nomor' => 'STN-2345-6789', 'berlaku' => '15 November 2025', 'status' => 'aktif'],
                            ['id' => 'S003', 'kendaraan' => 'Mitsubishi Xpander (B 9012 DEF)', 'nomor' => 'STN-3456-7890', 'berlaku' => '20 Juni 2025', 'status' => 'warning'],
                        ];
                    @endphp
                    @foreach ($stnks as $s)
                    <tr>
                        <td>{{ $s['id'] }}</td>
                        <td>{{ $s['kendaraan'] }}</td>
                        <td>{{ $s['nomor'] }}</td>
                        <td>{{ $s['berlaku'] }}</td>
                        <td>
                            @php
                                $badgeClass = match($s['status']) {
                                    'aktif' => 'badge-success',
                                    'warning' => 'badge-warning',
                                    'expired' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($s['status']) }}</span>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm action-btn"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-primary btn-sm action-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm action-btn"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah STNK -->
        <div id="addStnkModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah STNK</h3>
                    <span class="close" onclick="closeModal('addStnkModal')">&times;</span>
                </div>
                <form>
                    <div class="form-row">
                        <label for="kendaraan">Kendaraan</label>
                        <select class="custom-dropdown" id="kendaraan">
                            <option value="">Pilih Kendaraan</option>
                            <option value="KD001">Toyota Avanza (B 1234 KJL)</option>
                            <option value="KD002">Honda HRV (B 5678 ABC)</option>
                            <option value="KD003">Mitsubishi Xpander (B 9012 DEF)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="nomor-stnk">Nomor STNK</label>
                        <input type="text" id="nomor-stnk" placeholder="Contoh: STN-1234-5678">
                    </div>
                    <div class="form-row">
                        <label for="tanggal-berlaku">Tanggal Berlaku</label>
                        <input type="date" id="tanggal-berlaku">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="closeModal('addStnkModal')">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <div class="page-item"><a href="#" class="page-link"><i class="fas fa-angle-double-left"></i></a></div>
            <div class="page-item active"><a href="#" class="page-link">1</a></div>
            <div class="page-item"><a href="#" class="page-link">2</a></div>
            <div class="page-item"><a href="#" class="page-link">3</a></div>
            <div class="page-item"><a href="#" class="page-link"><i class="fas fa-angle-double-right"></i></a></div>
        </div>
    </div>
</div>
@endsection
<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>
