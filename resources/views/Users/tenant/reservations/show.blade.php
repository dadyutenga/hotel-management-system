<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { margin-left: 280px; flex: 1; min-height: 100vh; }
        .header { background: white; padding: 20px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .header-left h1 { font-size: 28px; font-weight: 600; color: #1a237e; margin-bottom: 5px; }
        .breadcrumb { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; }
        .breadcrumb a { color: #1a237e; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .content { padding: 30px; }

        .btn { padding: 12px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s ease; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-1px); }
        .btn-link { background: transparent; color: #1a237e; padding: 0; text-decoration: none; font-weight: 600; }
        .btn-link:hover { text-decoration: underline; }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 22px; }
        .card-header { background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; padding: 18px 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .card-body { padding: 22px; }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 700; letter-spacing: .03em; }
        .value { font-weight: 700; color: #333; margin-top: 6px; }
        .muted { color: #666; font-size: 14px; }

        .pill { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; border: 1px solid transparent; }
        .pill.pending { background: #fff3cd; border-color: #ffeeba; color: #856404; }
        .pill.confirmed { background: #d1e7dd; border-color: #badbcc; color: #0f5132; }
        .pill.checked_in { background: #cff4fc; border-color: #b6effb; color: #055160; }
        .pill.checked_out { background: #e2e3e5; border-color: #d6d8db; color: #41464b; }
        .pill.cancelled { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .pill.no_show { background: #e2e3e5; border-color: #d6d8db; color: #41464b; }
        .pill.hold { background: #e7e0ff; border-color: #d7ccff; color: #3d2c7a; }

        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
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
        .form-actions { display: flex; gap: 12px; justify-content: end; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e9ecef; }

        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; padding: 14px 16px; font-size: 12px; letter-spacing: .03em; text-transform: uppercase; color: #666; background: #f8f9fa; border-bottom: 1px solid #e9ecef; }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #e9ecef; vertical-align: top; font-size: 14px; }
        tbody tr:hover { background: #fcfcfd; }

        @media (max-width: 1000px) { .grid-3 { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .main-content { margin-left: 0; } .content { padding: 20px; } .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @php
        $statusKey = strtoupper((string) ($reservation->status ?? ''));
        $statusClassMap = [
            'PENDING' => 'pending',
            'CONFIRMED' => 'confirmed',
            'CHECKED_IN' => 'checked_in',
            'CHECKED_OUT' => 'checked_out',
            'CANCELLED' => 'cancelled',
            'NO_SHOW' => 'no_show',
            'HOLD' => 'hold',
        ];
        $pillClass = $statusClassMap[$statusKey] ?? 'pending';
    @endphp

    <div class="dashboard-container">
        @include('Users.shared.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Reservation {{ $reservation->reference ?? $reservation->id }}</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.reservations.index') }}">Reservations</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>{{ $reservation->reference ?? $reservation->id }}</span>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap: 10px; flex-wrap: wrap;">
                    <span class="pill {{ $pillClass }}">{{ str_replace('_',' ', $statusKey ?: 'N/A') }}</span>
                    @if($reservation->folio)
                        <a class="btn btn-secondary" href="{{ route('tenant.folios.show', $reservation->folio) }}"><i class="fas fa-file-invoice"></i> View Folio</a>
                    @endif
                    <a class="btn btn-secondary" href="{{ route('tenant.reservations.index') }}"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header"><i class="fas fa-circle-info"></i> Stay Overview</div>
                    <div class="card-body">
                        <div class="grid-3">
                            <div>
                                <div class="label">Guest</div>
                                <div class="value">{{ $reservation->guest?->full_name ?? '—' }}</div>
                                <div class="muted">{{ $reservation->guest?->email ?? '' }}</div>
                                <div class="muted">{{ $reservation->guest?->phone ?? '' }}</div>
                            </div>
                            <div>
                                <div class="label">Stay Dates</div>
                                <div class="value">{{ $reservation->arrival_date?->format('d M Y') }} → {{ $reservation->departure_date?->format('d M Y') }}</div>
                                <div class="muted">{{ ($nights ?? null) !== null ? $nights.' nights' : '—' }} • {{ $reservation->adults ?? 1 }} adults, {{ $reservation->children ?? 0 }} children</div>
                            </div>
                            <div>
                                <div class="label">Financials</div>
                                <div class="muted">Total: <span class="value">{{ number_format((float) ($reservation->total_amount ?? 0), 2) }}</span></div>
                                <div class="muted">Balance: <span class="value">{{ number_format((float) ($balance ?? 0), 2) }}</span></div>
                            </div>
                        </div>

                        <div style="margin-top: 18px;">
                            <div class="label" style="margin-bottom: 8px;">Special Requests</div>
                            <div class="muted">{{ $reservation->special_requests ?: 'No special requests noted.' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="fas fa-bed"></i> Reserved Rooms</div>
                    <div class="card-body" style="padding:0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Room Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($reservation->reservationRooms ?? []) as $reservationRoom)
                                    @php
                                        $roomStatus = strtoupper((string) ($reservationRoom->status ?? ''));
                                        $roomPill = $statusClassMap[$roomStatus] ?? 'pending';
                                    @endphp
                                    <tr>
                                        <td style="font-weight:700;">
                                            {{ $reservationRoom->room?->number ?? 'Unassigned' }}
                                        </td>
                                        <td>
                                            {{ $reservationRoom->roomType?->name ?? '—' }}
                                        </td>
                                        <td>
                                            <span class="pill {{ $roomPill }}">{{ str_replace('_',' ', $roomStatus ?: 'N/A') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 20px; text-align:center; color:#666;">No rooms linked to this reservation.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @can('update', $reservation)
                    <div class="card">
                        <div class="card-header"><i class="fas fa-arrows-rotate"></i> Update Status</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('tenant.reservations.update-status', $reservation) }}">
                                @csrf
                                @method('PUT')

                                <div class="grid">
                                    <div>
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            @foreach(['PENDING','CONFIRMED','CHECKED_IN','CHECKED_OUT','CANCELLED','NO_SHOW','HOLD'] as $s)
                                                <option value="{{ $s }}" {{ ($reservation->status === $s) ? 'selected' : '' }}>{{ str_replace('_',' ', $s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label" for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" style="min-height: 110px;"></textarea>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</body>
</html>
