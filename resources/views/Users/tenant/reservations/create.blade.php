<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reservation - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { margin-left: 280px; flex: 1; min-height: 100vh; }
        .header { background: white; padding: 20px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header-left h1 { font-size: 28px; font-weight: 600; color: #1a237e; margin-bottom: 5px; }
        .breadcrumb { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; }
        .breadcrumb a { color: #1a237e; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .content { padding: 30px; }

        .form-container { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden; max-width: 1000px; margin: 0 auto; }
        .form-header { background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; padding: 25px 30px; text-align: center; }
        .form-header h2 { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
        .form-header p { opacity: 0.9; font-size: 16px; }
        .form-content { padding: 40px 30px; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .form-group { margin-bottom: 25px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
        .form-label .required { color: #f44336; margin-left: 4px; }

        .form-control, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Figtree', sans-serif;
            background-color: white;
        }
        .form-control:focus, .form-select:focus { outline: none; border-color: #ff9800; box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1); }

        .helper { font-size: 13px; color: #666; margin-top: 6px; }
        .error-message { color: #f44336; font-size: 14px; margin-top: 5px; }

        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s ease; font-size: 16px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-1px); }
        .btn-link { color: #1a237e; text-decoration: none; font-weight: 600; }
        .btn-link:hover { text-decoration: underline; }

        .form-actions { display: flex; gap: 15px; justify-content: end; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e9ecef; }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .checkbox-list { border: 2px solid #e9ecef; border-radius: 10px; padding: 14px 15px; max-height: 220px; overflow: auto; background: #fff; }
        .checkbox-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f1f3f5; }
        .checkbox-item:last-child { border-bottom: none; }
        .checkbox-item input { width: 18px; height: 18px; accent-color: #ff9800; margin-top: 2px; }
        .checkbox-item .meta { font-size: 13px; color: #666; }

        .search-wrap { position: relative; }
        .search-results {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 6px);
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            max-height: 260px;
            overflow: auto;
            z-index: 10;
            display: none;
        }
        .search-results.open { display: block; }
        .search-item { padding: 12px 14px; cursor: pointer; border-bottom: 1px solid #f1f3f5; }
        .search-item:last-child { border-bottom: none; }
        .search-item:hover { background: #f8f9fa; }
        .search-item strong { display: block; }
        .search-item span { display: block; font-size: 13px; color: #666; margin-top: 2px; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .content { padding: 20px; }
            .form-content { padding: 30px 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Create Reservation</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.reservations.index') }}">Reservations</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Create</span>
                    </div>
                </div>
            </div>

            <div class="content">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-container">
                    <div class="form-header">
                        <h2><i class="fas fa-calendar-plus"></i> New Reservation</h2>
                        <p>Create a reservation and assign guest details.</p>
                    </div>

                    <form action="{{ route('tenant.reservations.store') }}" method="POST" class="form-content" id="reservation-form">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="property_id"><i class="fas fa-building"></i> Property <span class="required">*</span></label>
                                <select id="property_id" name="property_id" class="form-select" required>
                                    <option value="">Select Property</option>
                                    @foreach(($properties ?? []) as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>{{ $property->name }}</option>
                                    @endforeach
                                </select>
                                @error('property_id')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="status"><i class="fas fa-flag"></i> Status <span class="required">*</span></label>
                                <select id="status" name="status" class="form-select" required>
                                    @foreach(['PENDING','CONFIRMED','HOLD'] as $status)
                                        <option value="{{ $status }}" {{ old('status','PENDING') === $status ? 'selected' : '' }}>{{ str_replace('_',' ', $status) }}</option>
                                    @endforeach
                                </select>
                                @error('status')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label" for="guest_search"><i class="fas fa-user"></i> Guest <span class="required">*</span></label>
                                <div class="search-wrap">
                                    <input type="text" id="guest_search" class="form-control" placeholder="Search guest name, email, or phone" autocomplete="off" value="{{ old('guest_query') }}" />
                                    <div id="guest_results" class="search-results"></div>
                                </div>
                                <input type="hidden" name="guest_id" id="guest_id" value="{{ old('guest_id') }}" />
                                <div class="helper">
                                    <a class="btn-link" href="{{ route('tenant.guests.create') }}"><i class="fas fa-user-plus"></i> Create new guest</a>
                                </div>
                                @error('guest_id')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="arrival_date"><i class="fas fa-calendar-check"></i> Arrival Date <span class="required">*</span></label>
                                <input type="date" id="arrival_date" name="arrival_date" class="form-control" required value="{{ old('arrival_date') }}" />
                                @error('arrival_date')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="departure_date"><i class="fas fa-calendar-xmark"></i> Departure Date <span class="required">*</span></label>
                                <input type="date" id="departure_date" name="departure_date" class="form-control" required value="{{ old('departure_date') }}" />
                                @error('departure_date')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="adults"><i class="fas fa-users"></i> Adults <span class="required">*</span></label>
                                <input type="number" id="adults" name="adults" class="form-control" min="1" required value="{{ old('adults', 1) }}" />
                                @error('adults')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="children"><i class="fas fa-child"></i> Children</label>
                                <input type="number" id="children" name="children" class="form-control" min="0" value="{{ old('children', 0) }}" />
                                @error('children')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="total_amount"><i class="fas fa-money-bill"></i> Total Amount <span class="required">*</span></label>
                                <input type="number" step="0.01" id="total_amount" name="total_amount" class="form-control" required value="{{ old('total_amount', 0) }}" />
                                @error('total_amount')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="discount_amount"><i class="fas fa-percent"></i> Discount Amount</label>
                                <input type="number" step="0.01" id="discount_amount" name="discount_amount" class="form-control" value="{{ old('discount_amount', 0) }}" />
                                @error('discount_amount')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label" for="discount_reason"><i class="fas fa-tag"></i> Discount Reason</label>
                                <input type="text" id="discount_reason" name="discount_reason" class="form-control" value="{{ old('discount_reason') }}" placeholder="Optional reason for discount" />
                                @error('discount_reason')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label"><i class="fas fa-bed"></i> Room Types <span class="required">*</span></label>
                                <div class="checkbox-list">
                                    @forelse(($roomTypes ?? []) as $roomType)
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="room_type_ids[]" value="{{ $roomType->id }}" {{ in_array($roomType->id, old('room_type_ids', [])) ? 'checked' : '' }}>
                                            <div>
                                                <div style="font-weight: 600;">{{ $roomType->name }}</div>
                                                <div class="meta">{{ $roomType->property?->name ?? '' }}</div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="helper">No room types found.</div>
                                    @endforelse
                                </div>
                                @error('room_type_ids')<div class="error-message">{{ $message }}</div>@enderror
                                @error('room_type_ids.*')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label" for="special_requests"><i class="fas fa-comment"></i> Special Requests</label>
                                <textarea id="special_requests" name="special_requests" rows="3" class="form-control" style="min-height: 110px;">{{ old('special_requests') }}</textarea>
                                @error('special_requests')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label" for="notes"><i class="fas fa-note-sticky"></i> Notes</label>
                                <textarea id="notes" name="notes" rows="3" class="form-control" style="min-height: 110px;">{{ old('notes') }}</textarea>
                                @error('notes')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="source"><i class="fas fa-globe"></i> Source</label>
                                <input type="text" id="source" name="source" class="form-control" value="{{ old('source', 'FRONT_DESK') }}" placeholder="FRONT_DESK / ONLINE / PHONE" />
                                @error('source')<div class="error-message">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="external_reference"><i class="fas fa-hashtag"></i> External Reference</label>
                                <input type="text" id="external_reference" name="external_reference" class="form-control" value="{{ old('external_reference') }}" />
                                @error('external_reference')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('tenant.reservations.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Reservation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const endpoint = @json(route('tenant.guests.search'));
            const input = document.getElementById('guest_search');
            const hidden = document.getElementById('guest_id');
            const results = document.getElementById('guest_results');
            let timer = null;

            function closeResults() {
                results.classList.remove('open');
                results.innerHTML = '';
            }

            function render(items) {
                if (!items.length) {
                    results.innerHTML = '<div class="search-item"><strong>No results</strong><span>Try a different search</span></div>';
                    results.classList.add('open');
                    return;
                }

                results.innerHTML = items.map(item => {
                    const meta = [item.email, item.phone].filter(Boolean).join(' • ');
                    return `
                        <div class="search-item" data-id="${item.id}" data-label="${(item.full_name || '').replace(/"/g, '&quot;')}">
                            <strong>${item.full_name || '—'}</strong>
                            <span>${meta || ''}</span>
                        </div>
                    `;
                }).join('');
                results.classList.add('open');
            }

            async function search(q) {
                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('q', q);
                    const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    render(Array.isArray(data) ? data : []);
                } catch (e) {
                    closeResults();
                }
            }

            input.addEventListener('input', function () {
                hidden.value = '';
                const q = (input.value || '').trim();
                if (q.length < 2) {
                    closeResults();
                    return;
                }
                clearTimeout(timer);
                timer = setTimeout(() => search(q), 250);
            });

            results.addEventListener('click', function (e) {
                const item = e.target.closest('.search-item');
                if (!item || !item.dataset.id) return;
                hidden.value = item.dataset.id;
                input.value = item.dataset.label || input.value;
                closeResults();
            });

            document.addEventListener('click', function (e) {
                if (e.target.closest('.search-wrap')) return;
                closeResults();
            });
        })();
    </script>
</body>
</html>
