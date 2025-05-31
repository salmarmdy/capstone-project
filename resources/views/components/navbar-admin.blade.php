<div class="navbar">
    <button class="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="user-info">
        <div>
            <h4>{{ auth()->user()->name ?? 'Admin Sistem' }}</h4>
            <small>{{ auth()->user()->role ?? 'Administrator' }}</small>
        </div>
    </div>
</div>