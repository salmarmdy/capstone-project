@php
    $currentPath = Request::url();
@endphp

<div class="sidebar">
    <div class="menu-section">
        <div class="menu-title">Main Menu</div>
        <ul class="menu-items">
            <li class="menu-item {{ $currentPath == url('/') ? 'active' : ''; }}">
                <a href="/">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="menu-item {{ $currentPath == url('/vehincles') ? 'active' : ''; }}">
                <a href="/vehicles" >
                    <i class="fas fa-car"></i> My Vehicles
                    <!-- <span class="badge badge-primary">2</span> -->
                </a>
            </li>
            <li class="menu-item">
                <a href="assessments.html">
                    <i class="fas fa-clipboard-check"></i> Assessments
                    <!-- <span class="badge badge-warning">1</span> -->
                </a>
            </li>
            <li class="menu-item {{ $currentPath == url('/notification') ? 'active' : ''; }}">
                <a href="/notification">
                    <i class="fas fa-bell"></i> Notifications
                    <!-- <span class="badge badge-danger">3</span> -->
                </a>
            </li>
        </ul>
    </div>
    
    <!-- <div class="menu-section">
        <div class="menu-title">Vehicle Management</div>
        <ul class="menu-items">
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-file-upload"></i> Upload Documents
                </a>
            </li>
        </ul>
    </div> -->
    
    <div class="menu-section">
        <div class="menu-title">Assessments</div>
        <ul class="menu-items">
            <li class="menu-item {{ $currentPath == url('/self-check') ? 'active' : ''; }}">
                <a href="/self-check">
                    <i class="fas fa-check-circle"></i> New Self-Check
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-history"></i> Assessment History
                </a>
            </li>
            <!-- <li class="menu-item">
                <a href="#">
                    <i class="fas fa-clock"></i> Scheduled Assessments
                </a>
            </li> -->
        </ul>
    </div>
    
    <div class="menu-section">
        <div class="menu-title"></div>
        <ul class="menu-items">
            <!-- <li class="menu-item">
                <a href="#">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </li> -->
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>