<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Guest Management - {{ config('app.name') }}</title>
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

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
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

        .badge-male {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-female {
            background: #fce7f3;
            color: #be185d;
        }

        .badge-other {
            background: #f3f4f6;
            color: #4b5563;
        }

        .badge-info {
            background: #e0f2fe;
            color: #075985;
        }

        .badge-archived {
            background: #fef3c7;
            color: #92400e;
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
        @include('Users.shared.sidebars.manager')

        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1><i class="fas fa-users"></i> Guest Management</h1>
                    <a href="{{ route('tenant.guests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Register New Guest
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
                        <h3>Total Guests</h3>
                        <p>{{ $stats['total'] }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>With Reservations</h3>
                        <p>{{ $stats['with_reservations'] }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>Currently Checked In</h3>
                        <p>{{ $stats['checked_in'] }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route('tenant.guests.index') }}" class="filters-container">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Name, email, phone, ID..." 
                                   value="{{ request('search') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <select id="nationality" name="nationality" class="form-control">
                                <option value="">All Nationalities</option>
                                @foreach($nationalities as $nationality)
                                    <option value="{{ $nationality }}" {{ request('nationality') === $nationality ? 'selected' : '' }}>
                                        {{ $nationality }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">All Genders</option>
                                <option value="MALE" {{ request('gender') === 'MALE' ? 'selected' : '' }}>Male</option>
                                <option value="FEMALE" {{ request('gender') === 'FEMALE' ? 'selected' : '' }}>Female</option>
                                <option value="OTHER" {{ request('gender') === 'OTHER' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="with_trashed" class="form-control">
                                <option value="0" {{ !request('with_trashed') ? 'selected' : '' }}>Active Only</option>
                                <option value="1" {{ request('with_trashed') ? 'selected' : '' }}>Include Archived</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('tenant.guests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>

                <!-- Guests Table -->
                <div class="table-container">
                    @if($guests->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Guest Name</th>
                                    <th>Contact</th>
                                    <th>ID Info</th>
                                    <th>Nationality</th>
                                    <th>Gender</th>
                                    <th>Reservations</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guests as $guest)
                                    <tr style="{{ $guest->trashed() ? 'opacity: 0.6;' : '' }}">
                                        <td>
                                            <strong>{{ $guest->full_name }}</strong>
                                            @if($guest->trashed())
                                                <span class="badge badge-archived" style="margin-left: 0.5rem;">Archived</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($guest->email)
                                                <div><i class="fas fa-envelope"></i> {{ $guest->email }}</div>
                                            @endif
                                            @if($guest->phone)
                                                <div><i class="fas fa-phone"></i> {{ $guest->phone }}</div>
                                            @endif
                                            @if(!$guest->email && !$guest->phone)
                                                <span style="color: #9ca3af;">No contact info</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($guest->id_type && $guest->id_number)
                                                <div>{{ str_replace('_', ' ', $guest->id_type) }}</div>
                                                <div style="color: #6b7280;">{{ $guest->id_number }}</div>
                                            @else
                                                <span style="color: #9ca3af;">Not provided</span>
                                            @endif
                                        </td>
                                        <td>{{ $guest->nationality ?? '—' }}</td>
                                        <td>
                                            @if($guest->gender === 'MALE')
                                                <span class="badge badge-male"><i class="fas fa-mars"></i> Male</span>
                                            @elseif($guest->gender === 'FEMALE')
                                                <span class="badge badge-female"><i class="fas fa-venus"></i> Female</span>
                                            @elseif($guest->gender === 'OTHER')
                                                <span class="badge badge-other">Other</span>
                                            @else
                                                <span style="color: #9ca3af;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $guest->reservations->count() }} 
                                                {{ Str::plural('reservation', $guest->reservations->count()) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $guest->created_at->format('M d, Y') }}</div>
                                            <div style="font-size: 0.75rem; color: #6b7280;">
                                                by {{ $guest->creator->name ?? 'System' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('tenant.guests.show', $guest->id) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                                @if(!$guest->trashed())
                                                    <a href="{{ route('tenant.guests.edit', $guest->id) }}" 
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif

                                                @if($guest->trashed())
                                                    <form action="{{ route('tenant.guests.destroy', $guest->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="restore" value="1">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-undo"></i> Restore
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('tenant.guests.destroy', $guest->id) }}" 
                                                          method="POST" style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to archive this guest?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-archive"></i> Archive
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="pagination">
                            {{ $guests->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>No Guests Found</h3>
                            <p>Start by registering a new guest or adjust your filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
