@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header">
        <h4>STNK yang Akan Berakhir</h4>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="main-table">
            <thead>
                <tr>
                    <th>Kendaraan</th>
                    <th>No. Polisi</th>
                    <th>No. STNK</th>
                    <th>Tanggal Berakhir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Toyota Avanza</td>
                    <td>B 1234 KJL</td>
                    <td>08123456789</td>
                    <td>12 Mei 2025</td>
                    <td><span class="status warning">Segera Berakhir</span></td>
                    <td>
                        <button class="btn btn-success btn-sm action-btn">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="btn btn-primary btn-sm action-btn">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Honda HRV</td>
                    <td>B 5678 ABC</td>
                    <td>08198765432</td>
                    <td>25 Mei 2025</td>
                    <td><span class="status warning">Segera Berakhir</span></td>
                    <td>
                        <button class="btn btn-success btn-sm action-btn">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="btn btn-primary btn-sm action-btn">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        <div class="page-item">
            <a href="#" class="page-link"><i class="fas fa-angle-double-left"></i></a>
        </div>
        <div class="page-item active">
            <a href="#" class="page-link">1</a>
        </div>
        <div class="page-item">
            <a href="#" class="page-link">2</a>
        </div>
        <div class="page-item">
            <a href="#" class="page-link">3</a>
        </div>
        <div class="page-item">
            <a href="#" class="page-link"><i class="fas fa-angle-double-right"></i></a>
        </div>
    </div>
</div>
@endsection