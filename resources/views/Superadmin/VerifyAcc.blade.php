<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Accounts - HotelPro Admin</title>
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .btn {
            padding: 8px 16px;
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

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            resize: vertical;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
                <h1 class="page-title">Verify Accounts</h1>
                <p class="page-subtitle">Review and approve pending business registrations</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $pendingTenants->total() }}</div>
                    <div class="stat-label">Pending Registrations</div>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
            @endif

            <!-- Tenants List -->
            <div class="tenants-section">
                <div class="section-header">
                    <h2 class="section-title">Pending Registrations</h2>
                </div>

                @if($pendingTenants->count() > 0)
                <table class="tenants-table">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Contact Email</th>
                            <th>Business Type</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingTenants as $tenant)
                        <tr>
                            <td>
                                <div>
                                    <div style="font-weight: 600;">{{ $tenant->name }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $tenant->contact_phone }}</div>
                                </div>
                            </td>
                            <td>{{ $tenant->contact_email }}</td>
                            <td>
                                <span class="status-badge" style="background: #dbeafe; color: #1e40af;">
                                    {{ $tenant->business_type }}
                                </span>
                            </td>
                            <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-pending">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="viewTenantDetails('{{ $tenant->id }}')">
                                    <i class="fas fa-eye"></i> Review
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    {{ $pendingTenants->links() }}
                </div>
                @else
                <div style="text-align: center; padding: 50px; color: #6b7280;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No Pending Registrations</h3>
                    <p>All business registrations have been processed.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tenant Details Modal -->
    <div id="tenantModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Business Registration Details</h2>
                <span class="close" onclick="closeTenantModal()">&times;</span>
            </div>
            <div class="modal-body" id="tenantModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div id="rejectionModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2 class="modal-title">Reject Registration</h2>
                <span class="close" onclick="closeRejectionModal()">&times;</span>
            </div>
            <form id="rejectionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" name="rejection_reason" rows="4" placeholder="Please provide a detailed reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectionModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Registration
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                    <button type="button" class="btn btn-danger" onclick="showRejectionModal()">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success" onclick="approveTenant()">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </div>
            `;
        }

        function generateDocumentCard(name, url, tenantId, type) {
            if (url) {
                return `
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="document-name">${name}</div>
                        <a href="${url}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="/superadmin/tenants/${tenantId}/documents/${type}/download" class="btn btn-secondary btn-sm">
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

        function showRejectionModal() {
            document.getElementById('rejectionModal').style.display = 'block';
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').style.display = 'none';
            document.getElementById('rejectionForm').reset();
        }

        function approveTenant() {
            if (!currentTenantId) return;

            if (confirm('Are you sure you want to approve this business registration?')) {
                fetch(`/superadmin/tenants/${currentTenantId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tenant approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error approving tenant');
                });
            }
        }

        // Handle rejection form submission
        document.getElementById('rejectionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentTenantId) return;

            const formData = new FormData(this);
            const rejectionReason = formData.get('rejection_reason');

            fetch(`/superadmin/tenants/${currentTenantId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    rejection_reason: rejectionReason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Tenant rejected successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting tenant');
            });
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const tenantModal = document.getElementById('tenantModal');
            const rejectionModal = document.getElementById('rejectionModal');
            
            if (event.target === tenantModal) {
                closeTenantModal();
            }
            if (event.target === rejectionModal) {
                closeRejectionModal();
            }
        });
    </script>
</body>
</html>
