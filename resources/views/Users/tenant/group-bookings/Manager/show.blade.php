<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Group Booking Details - {{ config('app.name') }}</title>
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
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
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

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-completed {
            background: #dbeafe;
            color: #1e40af;
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
            grid-template-columns: repeat(2, 1fr);
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
                    <h1><i class="fas fa-layer-group"></i> {{ $groupBooking->name }}</h1>
                    <a href="{{ route('tenant.group-bookings.index') }}" class="btn btn-secondary">
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
                    <!-- Group Booking Details -->
                    <div>
                        <div class="details-card">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Group Booking Information
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Group Name</span>
                                <span class="detail-value"><strong>{{ $groupBooking->name }}</strong></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Property</span>
                                <span class="detail-value">
                                    <i class="fas fa-building"></i> {{ $groupBooking->property->name ?? '—' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Arrival Date</span>
                                <span class="detail-value">
                                    <i class="fas fa-calendar-check"></i> 
                                    {{ $groupBooking->arrival_date ? $groupBooking->arrival_date->format('M d, Y') : '—' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Departure Date</span>
                                <span class="detail-value">
                                    <i class="fas fa-calendar-times"></i> 
                                    {{ $groupBooking->departure_date ? $groupBooking->departure_date->format('M d, Y') : '—' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Total Rooms</span>
                                <span class="detail-value">
                                    <i class="fas fa-door-open"></i> {{ $groupBooking->total_rooms }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    @if($groupBooking->status === 'PENDING')
                                        <span class="badge badge-pending"><i class="fas fa-clock"></i> Pending</span>
                                    @elseif($groupBooking->status === 'CONFIRMED')
                                        <span class="badge badge-confirmed"><i class="fas fa-check-circle"></i> Confirmed</span>
                                    @elseif($groupBooking->status === 'CANCELLED')
                                        <span class="badge badge-cancelled"><i class="fas fa-times-circle"></i> Cancelled</span>
                                    @elseif($groupBooking->status === 'COMPLETED')
                                        <span class="badge badge-completed"><i class="fas fa-flag-checkered"></i> Completed</span>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Leader Guest</span>
                                <span class="detail-value">
                                    @if($groupBooking->leaderGuest)
                                        <i class="fas fa-user-tie"></i> {{ $groupBooking->leaderGuest->full_name }}
                                    @else
                                        <span style="color: #9ca3af;">Not assigned</span>
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Corporate Account</span>
                                <span class="detail-value">
                                    @if($groupBooking->corporateAccount)
                                        <i class="fas fa-building"></i> {{ $groupBooking->corporateAccount->name }}
                                    @else
                                        <span style="color: #9ca3af;">Not assigned</span>
                                    @endif
                                </span>
                            </div>

                            @if($groupBooking->notes)
                                <div class="detail-row">
                                    <span class="detail-label">Notes</span>
                                    <span class="detail-value">{{ $groupBooking->notes }}</span>
                                </div>
                            @endif

                            <div class="detail-row">
                                <span class="detail-label">Created On</span>
                                <span class="detail-value">
                                    {{ $groupBooking->created_at->format('M d, Y h:i A') }}
                                    <br><small>by {{ $groupBooking->creator->name ?? 'System' }}</small>
                                </span>
                            </div>

                            @if($groupBooking->updated_at && $groupBooking->updated_at != $groupBooking->created_at)
                                <div class="detail-row">
                                    <span class="detail-label">Last Updated</span>
                                    <span class="detail-value">
                                        {{ $groupBooking->updated_at->format('M d, Y h:i A') }}
                                        @if($groupBooking->updater)
                                            <br><small>by {{ $groupBooking->updater->name }}</small>
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
                                Group Statistics
                            </div>

                            <div class="stats-grid">
                                <div class="stat-card">
                                    <h3>Reservations</h3>
                                    <p>{{ $stats['reservations_count'] }}</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Total Guests</h3>
                                    <p>{{ $stats['total_guests'] }}</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Nights</h3>
                                    <p>{{ $stats['nights'] }}</p>
                                </div>
                                <div class="stat-card">
                                    <h3>Rooms Assigned</h3>
                                    <p>{{ $stats['rooms_assigned'] }}</p>
                                </div>
                                <div class="stat-card" style="grid-column: span 2;">
                                    <h3>Total Amount</h3>
                                    <p>${{ number_format($stats['total_amount'], 2) }}</p>
                                </div>
                            </div>

                            <div class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-cog"></i>
                                Actions
                            </div>

                            <div class="action-group">
                                <a href="{{ route('tenant.group-bookings.edit', $groupBooking->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Group Booking
                                </a>
                            </div>

                            @if(auth()->user()->hasRole(['MANAGER', 'DIRECTOR']))
                                <div class="action-group">
                                    <form action="{{ route('tenant.group-bookings.destroy', $groupBooking->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this group booking? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete Group Booking
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reservations List -->
                <div class="reservations-table">
                    <div class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Reservations in Group
                    </div>

                    @if($reservations->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Reservation ID</th>
                                    <th>Guest</th>
                                    <th>Room</th>
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
                                        <td>{{ $reservation->guest->full_name ?? '—' }}</td>
                                        <td>{{ $reservation->room->number ?? 'Not assigned' }}</td>
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
                            <p>No reservations have been added to this group booking yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
