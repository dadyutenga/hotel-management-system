@extends('layouts.app')

@php
    $pageTitle = 'Reports';
    $breadcrumbs = [
        ['label' => 'Reports'],
    ];
@endphp

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <a href="{{ route('tenant.reports.index') }}" class="group">
            <x-card title="Dashboard" subtitle="Hotel-wide performance snapshot" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">View dashboard <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
        <a href="{{ route('tenant.reports.occupancy') }}" class="group">
            <x-card title="Occupancy" subtitle="Availability and occupancy analytics" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">Open report <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
        <a href="{{ route('tenant.reports.revenue') }}" class="group">
            <x-card title="Revenue" subtitle="Financial performance across channels" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">Open report <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
        <a href="{{ route('tenant.reports.guests') }}" class="group">
            <x-card title="Guests" subtitle="Demographics and loyalty insights" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">Open report <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
        <a href="{{ route('tenant.reports.reservations') }}" class="group">
            <x-card title="Reservations" subtitle="Booking trends and conversion" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">Open report <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
        <a href="{{ route('tenant.reports.housekeeping') }}" class="group">
            <x-card title="Housekeeping" subtitle="Productivity and service quality" class="h-full">
                <div class="text-emerald-600 text-sm font-semibold">Open report <i class="fa-solid fa-arrow-right-long ml-2 transition-transform group-hover:translate-x-1"></i></div>
            </x-card>
        </a>
    </div>
@endsection
