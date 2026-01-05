<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { margin-left: 280px; flex: 1; min-height: 100vh; }

        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-left h1 { font-size: 28px; font-weight: 600; color: #1a237e; margin-bottom: 5px; }
        .breadcrumb { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; }
        .breadcrumb a { color: #1a237e; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .content { padding: 30px; }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .btn {
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .btn-primary { background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-1px); }
        .btn-link { background: transparent; color: #1a237e; padding: 0; }
        .btn-link:hover { text-decoration: underline; }

        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .section-header {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
            color: white;
            padding: 18px 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-body { padding: 22px; }

        .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-label { font-weight: 600; color: #333; font-size: 14px; }
        .form-control, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Figtree', sans-serif;
            background: white;
        }
        .form-control:focus, .form-select:focus { outline: none; border-color: #ff9800; box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 25px; }
        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 18px 20px;
            border-left: 5px solid #ff9800;
        }
        .stat-title { font-size: 13px; font-weight: 700; color: #666; text-transform: uppercase; }
        .stat-value { font-size: 30px; font-weight: 700; color: #333; margin-top: 8px; }
        .stat-sub { font-size: 13px; color: #666; }

        .table-wrap {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left;
            padding: 14px 16px;
            font-size: 12px;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #666;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #e9ecef; vertical-align: top; font-size: 14px; }
        tbody tr:hover { background: #fcfcfd; }

        .pill { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; border: 1px solid transparent; }
        .pill.pending { background: #fff3cd; border-color: #ffeeba; color: #856404; }
        .pill.confirmed { background: #d1e7dd; border-color: #badbcc; color: #0f5132; }
        .pill.checked_in { background: #cff4fc; border-color: #b6effb; color: #055160; }
        .pill.checked_out { background: #e2e3e5; border-color: #d6d8db; color: #41464b; }
        .pill.cancelled { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .pill.no_show { background: #e2e3e5; border-color: #d6d8db; color: #41464b; }
        .pill.hold { background: #e7e0ff; border-color: #d7ccff; color: #3d2c7a; }

        .actions { display: flex; gap: 10px; }
        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 2px solid #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .icon-btn:hover { border-color: #ff9800; color: #ff9800; transform: translateY(-1px); }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebars.receptionist')

        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Reservations</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('user.dashboard') }}">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Reservations</span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('tenant.reservations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        New Reservation
                    </a>
                </div>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-container">
                    <div class="section-header">
                        <i class="fas fa-filter"></i>
                        Filter Reservations
                    </div>
                    <div class="section-body">
                        @php
                            $statusOptions = ['ALL', 'PENDING', 'CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT', 'CANCELLED', 'NO_SHOW', 'HOLD'];
                        @endphp
                        <form method="GET" action="{{ route('tenant.reservations.index') }}" class="filters-grid">
                            <div class="form-group">
                                <label class="form-label" for="guest">Guest</label>
                                <input id="guest" type="text" name="guest" value="{{ request('guest') }}" class="form-control" placeholder="Search guest name or email" />
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-select">
                                    @foreach($statusOptions as $option)
                                        <option value="{{ $option }}" {{ request('status', 'ALL') === $option ? 'selected' : '' }}>{{ str_replace('_', ' ', $option) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="from">From</label>
                                <input id="from" type="date" name="from" value="{{ request('from') }}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="to">To</label>
                                <input id="to" type="date" name="to" value="{{ request('to') }}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="property">Property</label>
                                <select id="property" name="property" class="form-select">
                                    <option value="">All</option>
                                    @foreach(($properties ?? []) as $property)
                                        <option value="{{ $property->id }}" {{ request('property') == $property->id ? 'selected' : '' }}>{{ $property->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="justify-content: end; gap: 10px;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                <a href="{{ route('tenant.reservations.index') }}" class="btn btn-secondary"><i class="fas fa-rotate-left"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title">Arrivals Today</div>
                        <div class="stat-value">{{ $metrics['arrivals_today'] ?? 0 }}</div>
                        <div class="stat-sub">Guests scheduled to arrive today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Departures Today</div>
                        <div class="stat-value">{{ $metrics['departures_today'] ?? 0 }}</div>
                        <div class="stat-sub">Guests checking out today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">In-House</div>
                        <div class="stat-value">{{ $metrics['in_house'] ?? 0 }}</div>
                        <div class="stat-sub">Current checked-in guests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Occupancy</div>
                        <div class="stat-value">{{ $metrics['occupancy_rate'] ?? '0%' }}</div>
                        <div class="stat-sub">Based on rooms vs in-house</div>
                    </div>
                </div>

                <div class="table-wrap">
                    <div class="section-header">
                        <i class="fas fa-list"></i>
                        Reservation List
                    </div>
                    <div class="section-body" style="padding: 0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Reservation</th>
                                    <th>Guest</th>
                                    <th>Stay</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusClassMap = [
                                        'PENDING' => 'pending',
                                        'CONFIRMED' => 'confirmed',
                                        'CHECKED_IN' => 'checked_in',
                                        'CHECKED_OUT' => 'checked_out',
                                        'CANCELLED' => 'cancelled',
                                        'NO_SHOW' => 'no_show',
                                        'HOLD' => 'hold',
                                    ];
                                @endphp

                                @forelse($reservations as $reservation)
                                    @php
                                        $statusKey = strtoupper((string) ($reservation->status ?? ''));
                                        $pillClass = $statusClassMap[$statusKey] ?? 'pending';
                                        $nights = ($reservation->arrival_date && $reservation->departure_date)
                                            ? $reservation->arrival_date->diffInDays($reservation->departure_date)
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700;">
                                                <a class="btn-link" href="{{ route('tenant.reservations.show', $reservation) }}">{{ $reservation->reference ?? $reservation->id }}</a>
                                            </div>
                                            <div style="font-size: 12px; color: #666;">Source: {{ $reservation->source ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $reservation->guest?->full_name ?? '—' }}</div>
                                            <div style="font-size: 12px; color: #666;">{{ $reservation->guest?->email ?? '' }}</div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $reservation->arrival_date?->format('d M Y') }} → {{ $reservation->departure_date?->format('d M Y') }}
                                            </div>
                                            <div style="font-size: 12px; color: #666;">
                                                {{ $nights !== null ? $nights . ' nights' : '—' }} • {{ $reservation->adults ?? 1 }} adults
                                            </div>
                                        </td>
                                        <td>
                                            <span class="pill {{ $pillClass }}">{{ str_replace('_', ' ', $statusKey ?: 'N/A') }}</span>
                                        </td>
                                        <td style="font-weight: 700;">
                                            {{ number_format((float) ($reservation->total_amount ?? 0), 2) }}
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a class="icon-btn" href="{{ route('tenant.reservations.show', $reservation) }}" title="View"><i class="fas fa-eye"></i></a>
                                                <a class="icon-btn" href="{{ route('tenant.reservations.edit', $reservation) }}" title="Edit"><i class="fas fa-pen"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 28px; text-align: center; color: #666;">
                                            <i class="fas fa-calendar-xmark" style="font-size: 26px; margin-bottom: 10px;"></i>
                                            <div>No reservations match the current filters.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 18px;">
                    {{ $reservations->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
