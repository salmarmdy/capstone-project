@php
    $currentPath = Request::url();
@endphp

<aside class="sidebar">
    <div class="sidebar-header">
        <h3>SIM Kendaraan</h3>
        <p>Sistem Informasi Manajemen Kendaraan</p>
    </div>
    
    <nav class="menu-items">
        <a href="/admin-cms" class="{{ $currentPath == url('/') ? 'active' : ''; }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="menu-category">Master Data</div>
        <a href="/admin-cms/karyawan" class="{{ $currentPath == url('/admin-cms/karyawan') ? 'active' : ''; }}"><i class="fas fa-users"></i> Karyawan</a>
        <a href="/admin-cms/kendaraan" class="{{ $currentPath == url('/admin-cms/kendaraan') ? 'active' : ''; }}"><i class="fas fa-car"></i> Kendaraan</a>
        <a href="/admin-cms/departemen" class="{{ $currentPath == url('/admin-cms/departemen') ? 'active' : ''; }}"><i class="fas fa-building"></i> Depatemen</a>

        <div class="menu-category">Operasional</div>
        <a href="/admin-cms/pemeriksaan" class="{{ $currentPath == url('/admin-cms/pemeriksaan') ? 'active' : ''; }}"><i class="fas fa-clipboard-check"></i> Pemeriksaan</a>
        <a href="/admin-cms/item-checklist" class="{{ $currentPath == url('/admin-cms/checklist') ? 'active' : ''; }}"><i class="fas fa-tasks"></i> Item Checklist</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <a type="submit" class="" onclick="return confirm('Yakin logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </form>
    </nav>
</aside>
