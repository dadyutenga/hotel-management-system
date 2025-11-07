<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Guest Details - {{ config('app.name') }}</title>
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
            background: #f8f9fa;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: #4caf50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
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

        .badge-archived {
            background: #fef3c7;
            color: #92400e;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .details-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            color: #333;
            text-align: right;
        }

        .actions-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }

        .action-group {
            margin-bottom: 20px;
        }

        .action-group:last-child {
            margin-bottom: 0;
        }

        .action-group .btn {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card p {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .reservations-table {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-top: 20px;
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
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        tbody tr:hover {
            background: #f9fafb;
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

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .pagination {
            display: flex;
            justify-content: center;
            padding: 1.5rem;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .reservations-table {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
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
                    <h1>
                        <i class="fas fa-user"></i> {{ $guest->full_name }}
                        @if($guest->trashed())
                            <span class="badge badge-archived">Archived</span>
                        @endif
                    </h1>
                    <a href="{{ route('tenant.guests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
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

                <div class="content-grid">
                    <!-- Guest Details -->
                    <div>
                        <div class="details-card">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Guest Information
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Full Name</span>
                                <span class="detail-value"><strong>{{ $guest->full_name }}</strong></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Email</span>
                                <span class="detail-value">
                                    @if($guest->email)
                                        <i class="fas fa-envelope"></i> {{ $guest->email }}
                                    @else
                                        <span style="color: #9ca3af;">Not provided</span>
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value">
                                    @if($guest->phone)
                                        <i class="fas fa-phone"></i> {{ $guest->phone }}
                                    @else
                                        <span style="color: #9ca3af;">Not provided</span>
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">ID Type</span>
                                <span class="detail-value">
                                    {{ $guest->id_type ? str_replace('_', ' ', $guest->id_type) : '—' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">ID Number</span>
                                <span class="detail-value">{{ $guest->id_number ?? '—' }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Date of Birth</span>
                                <span class="detail-value">
                                    {{ $guest->date_of_birth ? $guest->date_of_birth->format('M d, Y') : '—' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Gender</span>
                                <span class="detail-value">
                                    @if($guest->gender === 'MALE')
                                        <span class="badge badge-male"><i class="fas fa-mars"></i> Male</span>
                                    @elseif($guest->gender === 'FEMALE')
                                        <span class="badge badge-female"><i class="fas fa-venus"></i> Female</span>
                                    @elseif($guest->gender === 'OTHER')
                                        <span class="badge badge-other">Other</span>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Nationality</span>
                                <span class="detail-value">{{ $guest->nationality ?? '—' }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Address</span>
                                <span class="detail-value">{{ $guest->address ?? '—' }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Marketing Consent</span>
                                <span class="detail-value">
                                    @if($guest->marketing_consent)
                                        <i class="fas fa-check-circle" style="color: #4caf50;"></i> Yes
                                    @else
                                        <i class="fas fa-times-circle" style="color: #ef4444;"></i> No
                                    @endif
                                </span>
                            </div>

                            @if($guest->notes)
                                <div class="detail-row">
                                    <span class="detail-label">Notes</span>
                                    <span class="detail-value">{{ $guest->notes }}</span>
                                </div>
                            @endif

                            <div class="detail-row">
                                <span class="detail-label">Registered On</span>
                                <span class="detail-value">
                                    {{ $guest->created_at->format('M d, Y h:i A') }}
                                    <br><small>by {{ $guest->creator->name ?? 'System' }}</small>
                                </span>
                            </div>

                            @if($guest->updated_at && $guest->updated_at != $guest->created_at)
                                <div class="detail-row">
                                    <span class="detail-label">Last Updated</span>
                                    <span class="detail-value">
                                        {{ $guest->updated_at->format('M d, Y h:i A') }}
                                        @if($guest->updater)
                                            <br><small>by {{ $guest->updater->name }}</small>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats & Actions Sidebar -->
                    <div>
                        <div class="actions-card">
                            <div class="section-title">
                                <i class="fas fa-chart-line"></i>
                                Guest Statistics
                            </div>

                            <div class="stats-grid">
                                <div class="stat-card">
                                    <h3>Reservations</h3>
                                    <p>{{ $stats['reservations_count'] }}</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Nights</h3>
                                    <p>{{ $stats['nights'] }}</p>
                                </div>
                                <div class="stat-card" style="grid-column: span 2;">
                                    <h3>Lifetime Value</h3>
                                    <p>${{ number_format($stats['lifetime_value'], 2) }}</p>
                                </div>
                            </div>

                            <div class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-cog"></i>
                                Actions
                            </div>

                            @if(!$guest->trashed())
                                <div class="action-group">
                                    <a href="{{ route('tenant.guests.edit', $guest->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Guest
                                    </a>
                                </div>

                                <div class="action-group">
                                    <form action="{{ route('tenant.guests.destroy', $guest->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to archive this guest? This action can be reversed later.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-archive"></i> Archive Guest
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="action-group">
                                    <form action="{{ route('tenant.guests.destroy', $guest->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="restore" value="1">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-undo"></i> Restore Guest
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reservation History -->
                <div class="reservations-table">
                    <div class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Reservation History
                    </div>

                    @if($reservations->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Reservation ID</th>
                                    <th>Property</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Nights</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $reservation)
                                    <tr>
                                        <td><strong>#{{ $reservation->id }}</strong></td>
                                        <td>{{ $reservation->property->name ?? '—' }}</td>
                                        <td>{{ $reservation->arrival_date ? $reservation->arrival_date->format('M d, Y') : '—' }}</td>
                                        <td>{{ $reservation->departure_date ? $reservation->departure_date->format('M d, Y') : '—' }}</td>
                                        <td>
                                            @if($reservation->arrival_date && $reservation->departure_date)
                                                {{ $reservation->arrival_date->diffInDays($reservation->departure_date) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>${{ number_format($reservation->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge" style="
                                                @if($reservation->status === 'CONFIRMED') background: #4caf50; color: white;
                                                @elseif($reservation->status === 'CHECKED_IN') background: #0ea5e9; color: white;
                                                @elseif($reservation->status === 'CHECKED_OUT') background: #6b7280; color: white;
                                                @elseif($reservation->status === 'CANCELLED') background: #ef4444; color: white;
                                                @else background: #f59e0b; color: white; @endif">
                                                {{ str_replace('_', ' ', $reservation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="pagination">
                            {{ $reservations->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Reservations</h3>
                            <p>This guest has no reservation history yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
