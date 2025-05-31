@extends('layouts.appv2')

@section('content')
<div class="main-content">
    <div>
        <ul class="reminders-list">
            <li class="reminder-item">
                <div class="reminder-icon orange">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="reminder-content">
                    <div class="reminder-title">Assessment Due: Honda PCX</div>
                    <div class="reminder-date">Due in 3 days (May 20, 2025)</div>
                </div>
                <div class="reminder-action">
                    <button class="btn btn-primary"><i class="fas fa-check"></i> Start</button>
                </div>
            </li>
            <li class="reminder-item">
                <div class="reminder-icon red">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="reminder-content">
                    <div class="reminder-title">Driving License Expiring Soon</div>
                    <div class="reminder-date">Expires on Jun 15, 2025 (29 days left)</div>
                </div>
                <div class="reminder-action">
                    <button class="btn btn-outline"><i class="fas fa-upload"></i> Update</button>
                </div>
            </li>
            <li class="reminder-item">
                <div class="reminder-icon blue">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="reminder-content">
                    <div class="reminder-title">Assessment Approved: Toyota Corolla</div>
                    <div class="reminder-date">Approved on May 10, 2025</div>
                </div>
                <div class="reminder-action">
                    <button class="btn btn-outline"><i class="fas fa-eye"></i> View</button>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection