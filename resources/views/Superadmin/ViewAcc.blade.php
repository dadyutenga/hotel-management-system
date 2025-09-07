<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Accounts - HotelPro Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6b7280;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #60a5fa;
        }

        .stat-card.verified {
            border-left-color: #10b981;
        }

        .stat-card.pending {
            border-left-color: #f59e0b;
        }

        .stat-card.rejected {
            border-left-color: #ef4444;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        /* Filters */
        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin-right: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        /* Tenants Table */
        .tenants-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 25px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .tenants-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tenants-table th {
            background: #f9fafb;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .tenants-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .tenants-table tbody tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-verified {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .activity-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .activity-active {
            background: #dcfdf7;
            color: #0d9488;
        }

        .activity-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .close {
            color: #6b7280;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #374151;
        }

        .modal-body {
            padding: 25px;
        }

        .detail-section {
            margin-bottom: 25px;
        }

        .detail-section h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            color: #1f2937;
            word-break: break-word;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .document-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .document-icon {
            font-size: 32px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .document-name {
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 0 0 12px 12px;
            text-align: right;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            padding: 20px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #d1d5db;
            color: #374151;
            border-radius: 4px;
        }

        .pagination a:hover {
            background: #f3f4f6;
        }

        .pagination .current {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .documents-grid {
                grid-template-columns: 1fr;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        @include('Superadmin.shared.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">All Tenant Accounts</h1>
                <p class="page-subtitle">View and manage all registered business accounts</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card verified">
                    <div class="stat-value">{{ $tenants->where('status', 'verified')->count() }}</div>
                    <div class="stat-label">Verified Accounts</div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-value">{{ $tenants->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">Pending Accounts</div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-value">{{ $tenants->where('status', 'rejected')->count() }}</div>
                    <div class="stat-label">Rejected Accounts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $tenants->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Active Accounts</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <form method="GET" action="{{ route('superadmin.view') }}">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Business Type</label>
                            <select name="business_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="HOTEL" {{ request('business_type') == 'HOTEL' ? 'selected' : '' }}>Hotel</option>
                                <option value="LODGE" {{ request('business_type') == 'LODGE' ? 'selected' : '' }}>Lodge</option>
                                <option value="RESTAURANT" {{ request('business_type') == 'RESTAURANT' ? 'selected' : '' }}>Restaurant</option>
                                <option value="BAR" {{ request('business_type') == 'BAR' ? 'selected' : '' }}>Bar</option>
                                <option value="PUB" {{ request('business_type') == 'PUB' ? 'selected' : '' }}>Pub</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Business name or email..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('superadmin.view') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tenants List -->
            <div class="tenants-section">
                <div class="section-header">
                    <h2 class="section-title">Tenant Accounts ({{ $tenants->total() }})</h2>
                </div>

                @if($tenants->count() > 0)
                <table class="tenants-table">
                    <thead>
                        <tr>
                            <th>Business Info</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Activity</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                        <tr>
                            <td>
                                <div>
                                    <div style="font-weight: 600; margin-bottom: 3px;">{{ $tenant->name }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ Str::limit($tenant->address, 40) }}</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div style="font-size: 14px;">{{ $tenant->contact_email }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $tenant->contact_phone }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge" style="background: #dbeafe; color: #1e40af;">
                                    {{ $tenant->business_type }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $tenant->status }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="activity-badge activity-{{ $tenant->is_active ? 'active' : 'inactive' }}">
                                    {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-primary" onclick="viewTenantDetails('{{ $tenant->id }}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @if($tenant->status === 'pending')
                                <a href="{{ route('superadmin.verify') }}?tenant={{ $tenant->id }}" class="btn btn-secondary">
                                    <i class="fas fa-check-circle"></i> Review
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    {{ $tenants->appends(request()->query())->links() }}
                </div>
                @else
                <div style="text-align: center; padding: 50px; color: #6b7280;">
                    <i class="fas fa-building" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No Tenant Accounts Found</h3>
                    <p>{{ request()->hasAny(['status', 'business_type', 'search']) ? 'Try adjusting your filters.' : 'No businesses have registered yet.' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tenant Details Modal -->
    <div id="tenantModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Tenant Account Details</h2>
                <span class="close" onclick="closeTenantModal()">&times;</span>
            </div>
            <div class="modal-body" id="tenantModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Include the Document Viewer Component -->
    @include('Superadmin.components.Viewdocs')

    <script>
        let currentTenantId = null;

        function viewTenantDetails(tenantId) {
            fetch(`/superadmin/tenants/${tenantId}/details`)
                .then(response => response.json())
                .then(data => {
                    displayTenantDetails(data);
                    currentTenantId = tenantId;
                    document.getElementById('tenantModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading tenant details');
                });
        }

        function displayTenantDetails(data) {
            const tenant = data.tenant;
            const adminUser = data.admin_user;
            const documents = data.documents;

            const modalBody = document.getElementById('tenantModalBody');
            
            modalBody.innerHTML = `
                <div class="detail-section">
                    <h4><i class="fas fa-building"></i> Business Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Business Name</span>
                            <span class="detail-value">${tenant.name}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Business Type</span>
                            <span class="detail-value">${tenant.business_type}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact Email</span>
                            <span class="detail-value">${tenant.contact_email}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact Phone</span>
                            <span class="detail-value">${tenant.contact_phone}</span>
                        </div>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">${tenant.address}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Certification Type</span>
                            <span class="detail-value">${tenant.certification_type}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">TIN/VAT Number</span>
                            <span class="detail-value">${tenant.tin_vat_number || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <span class="status-badge status-${tenant.status}">${tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1)}</span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Account Activity</span>
                            <span class="detail-value">
                                <span class="activity-badge activity-${tenant.is_active ? 'active' : 'inactive'}">
                                    ${tenant.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                ${adminUser ? `
                <div class="detail-section">
                    <h4><i class="fas fa-user-tie"></i> Admin User Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Full Name</span>
                            <span class="detail-value">${adminUser.name}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Username</span>
                            <span class="detail-value">${adminUser.username}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${adminUser.email}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">${adminUser.phone || 'N/A'}</span>
                        </div>
                    </div>
                </div>
                ` : ''}

                <div class="detail-section">
                    <h4><i class="fas fa-file-alt"></i> Uploaded Documents</h4>
                    <div class="documents-grid">
                        ${generateDocumentCard('Business License', documents.business_license, tenant.id, 'business_license')}
                        ${generateDocumentCard('Tax Certificate', documents.tax_certificate, tenant.id, 'tax_certificate')}
                        ${generateDocumentCard('Owner ID', documents.owner_id, tenant.id, 'owner_id')}
                        ${generateDocumentCard('Registration Certificate', documents.registration_certificate, tenant.id, 'registration_certificate')}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeTenantModal()">Close</button>
                    ${tenant.status === 'pending' ? `
                        <a href="/superadmin/verify-accounts?tenant=${tenant.id}" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Review Registration
                        </a>
                    ` : ''}
                </div>
            `;
        }

        function generateDocumentCard(name, url, tenantId, type) {
            if (url) {
                const downloadUrl = `/superadmin/tenants/${tenantId}/documents/${type}/download`;
                return `
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="document-name">${name}</div>
                        <button class="btn btn-primary btn-sm" onclick="openDocumentViewer('${url}', '${name}', '${downloadUrl}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <a href="${downloadUrl}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            } else {
                return `
                    <div class="document-card" style="opacity: 0.5;">
                        <div class="document-icon">
                            <i class="fas fa-file-times"></i>
                        </div>
                        <div class="document-name">${name}</div>
                        <span style="color: #6b7280; font-size: 12px;">Not uploaded</span>
                    </div>
                `;
            }
        }

        function closeTenantModal() {
            document.getElementById('tenantModal').style.display = 'none';
            currentTenantId = null;
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const tenantModal = document.getElementById('tenantModal');
            
            if (event.target === tenantModal) {
                closeTenantModal();
            }
        });
    </script>
</body>
</html>
