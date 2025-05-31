<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Vehicle Management System - My Vehicles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header Component -->
        @include('components.header')
        
        <div class="menu-wrapper">
            @include('components.sidebar')
            
            <div class="main-content">
                <!-- Content Area -->
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>