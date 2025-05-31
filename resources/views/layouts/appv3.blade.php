<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My App</title>
    <link rel="stylesheet" href="{{ asset('css/employee2.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="menu-wrapper">
        <div class="main-content">
             <div style="text-align: right; margin-bottom: 15px;">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin logout?')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
            @yield('content')
        </div>
    </div>
</body>
</html>
