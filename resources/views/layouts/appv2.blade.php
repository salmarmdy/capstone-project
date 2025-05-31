<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My App</title>
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
    @include('components.header')
    <div class="menu-wrapper">
        @include('components.sidebar')
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</body>
</html>
