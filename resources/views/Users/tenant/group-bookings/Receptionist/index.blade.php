<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Group Booking Management - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        .container {
            padding: 2rem;
            max-width: 1400px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            color: #1f2937;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card p {
            font-size: 1.875rem;
            font-weight: 600;
            color: #1f2937;
        }

        .filters-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .form-control {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4caf50;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #4caf50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-info {
            background: #0ea5e9;
            color: white;
        }

        .btn-info:hover {
            background: #0284c7;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-confirmed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-completed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-info {
            background: #e0f2fe;
            color: #075985;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 1.5rem;
            gap: 0.5rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-sm {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebars.receptionist')

        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1><i class="fas fa-layer-group"></i> Group Booking Management</h1>
                    <a href="{{ route('tenant.group-bookings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Group Booking
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Statistics -->
                <div class="stats-container">
                    <div class="stat-card">
                        <h3>Total Group Bookings</h3>
                        <p>{{ $stats['total'] }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>Pending</h3>
                        <p>{{ $stats['pending'] }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>Confirmed</h3>
                        <p>{{ $stats['confirmed'] }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Rooms Reserved</h3>
                        <p>{{ $stats['total_rooms'] }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route('tenant.group-bookings.index') }}" class="filters-container">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Group name, leader, corporate..." 
                                   value="{{ request('search') }}">
                        </div>
                        
                        @if($properties->count() > 1)
                        <div class="form-group">
                            <label for="property_id">Property</label>
                            <select id="property_id" name="property_id" class="form-control">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') === $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                                <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="arrival_date">Arrival Date From</label>
                            <input type="date" id="arrival_date" name="arrival_date" class="form-control" 
                                   value="{{ request('arrival_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="departure_date">Departure Date To</label>
                            <input type="date" id="departure_date" name="departure_date" class="form-control" 
                                   value="{{ request('departure_date') }}">
                        </div>
                    </div>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('tenant.group-bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>

                <!-- Group Bookings Table -->
                <div class="table-container">
                    @if($groupBookings->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Property</th>
                                    <th>Leader/Corporate</th>
                                    <th>Dates</th>
                                    <th>Rooms</th>
                                    <th>Reservations</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupBookings as $groupBooking)
                                    <tr>
                                        <td><strong>{{ $groupBooking->name }}</strong></td>
                                        <td>{{ $groupBooking->property->name ?? '—' }}</td>
                                        <td>
                                            @if($groupBooking->leaderGuest)
                                                <div><i class="fas fa-user"></i> {{ $groupBooking->leaderGuest->full_name }}</div>
                                            @endif
                                            @if($groupBooking->corporateAccount)
                                                <div><i class="fas fa-building"></i> {{ $groupBooking->corporateAccount->name }}</div>
                                            @endif
                                            @if(!$groupBooking->leaderGuest && !$groupBooking->corporateAccount)
                                                <span style="color: #9ca3af;">No leader/corporate</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div><i class="fas fa-calendar-alt"></i> {{ $groupBooking->arrival_date->format('M d, Y') }}</div>
                                            <div style="color: #6b7280; font-size: 0.875rem;">to {{ $groupBooking->departure_date->format('M d, Y') }}</div>
                                            <div style="color: #6b7280; font-size: 0.75rem;">{{ $groupBooking->arrival_date->diffInDays($groupBooking->departure_date) }} nights</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $groupBooking->total_rooms }} {{ Str::plural('room', $groupBooking->total_rooms) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $groupBooking->reservations->count() }} {{ Str::plural('reservation', $groupBooking->reservations->count()) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($groupBooking->status === 'PENDING')
                                                <span class="badge badge-pending">Pending</span>
                                            @elseif($groupBooking->status === 'CONFIRMED')
                                                <span class="badge badge-confirmed">Confirmed</span>
                                            @elseif($groupBooking->status === 'CANCELLED')
                                                <span class="badge badge-cancelled">Cancelled</span>
                                            @elseif($groupBooking->status === 'COMPLETED')
                                                <span class="badge badge-completed">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $groupBooking->created_at->format('M d, Y') }}</div>
                                            <div style="font-size: 0.75rem; color: #6b7280;">
                                                by {{ $groupBooking->creator->full_name ?? 'System' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('tenant.group-bookings.show', $groupBooking->id) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('tenant.group-bookings.edit', $groupBooking->id) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="pagination">
                            {{ $groupBookings->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-layer-group"></i>
                            <h3>No Group Bookings Found</h3>
                            <p>Start by creating a new group booking or adjust your filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
