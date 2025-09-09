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
    
    <!-- Import the separate sidebar CSS -->
    <link rel="stylesheet" href="{{ asset(path: 'css/superadmin-sidebar.css') }}">

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

        /* Add these styles to your existing CSS */
        .filter-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0;
        }

        .filter-tab {
            padding: 12px 20px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-tab:hover {
            color: #374151;
            background: #f9fafb;
        }

        .filter-tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            background: #eff6ff;
        }

        .status-rejected {
            background: #fecaca;
            color: #991b1b;
        }

        .status-verified {
            background: #bbf7d0;
            color: #065f46;
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
                <p class="page-subtitle">Review and manage business registrations</p>
            </div>

            <!-- Status Filter Tabs -->
            <div class="filter-tabs" style="margin-bottom: 30px;">
                <a href="{{ route('superadmin.verify-accounts', ['status' => 'pending']) }}" 
                   class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('superadmin.verify-accounts', ['status' => 'rejected']) }}" 
                   class="filter-tab {{ $status === 'rejected' ? 'active' : '' }}">
                    <i class="fas fa-times-circle"></i> Rejected ({{ $rejectedCount }})
                </a>
                <a href="{{ route('superadmin.verify-accounts', ['status' => 'all']) }}" 
                   class="filter-tab {{ $status === 'all' ? 'active' : '' }}">
                    <i class="fas fa-list"></i> All Registrations
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $pendingCount }}</div>
                    <div class="stat-label">Pending Registrations</div>
                </div>
                <div class="stat-card" style="border-left-color: #ef4444;">
                    <div class="stat-value">{{ $rejectedCount }}</div>
                    <div class="stat-label">Rejected Registrations</div>
                </div>
                <div class="stat-card" style="border-left-color: #10b981;">
                    <div class="stat-value">{{ $verifiedCount }}</div>
                    <div class="stat-label">Verified Registrations</div>
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
                    <h2 class="section-title">
                        @if($status === 'pending')
                            Pending Registrations
                        @elseif($status === 'rejected')
                            Rejected Registrations
                        @else
                            All Registrations
                        @endif
                    </h2>
                </div>

                @if($tenants->count() > 0)
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
                        @foreach($tenants as $tenant)
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
                                <span class="status-badge 
                                    @if($tenant->status === 'pending') status-pending
                                    @elseif($tenant->status === 'rejected') status-rejected
                                    @else status-verified
                                    @endif">
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
                    {{ $tenants->appends(['status' => $status])->links() }}
                </div>
                @else
                <div style="text-align: center; padding: 50px; color: #6b7280;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No {{ ucfirst($status) }} Registrations</h3>
                    <p>
                        @if($status === 'pending')
                            All business registrations have been processed.
                        @elseif($status === 'rejected')
                            No rejected registrations found.
                        @else
                            No registrations found.
                        @endif
                    </p>
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

    <!-- Include the Document Viewer Component -->
    @include('Superadmin.components.Viewdocs')

    <script>
        let currentTenantId = null;
        let currentTenantStatus = null;

        function viewTenantDetails(tenantId) {
            console.log('Fetching details for tenant:', tenantId);
            
            fetch(`/superadmin/tenants/${tenantId}/details`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    
                    if (data.success) {
                        displayTenantDetails(data);
                        currentTenantId = tenantId;
                        currentTenantStatus = data.tenant.status;
                        document.getElementById('tenantModal').style.display = 'block';
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading tenant details: ' + error.message);
                });
        }

        function displayTenantDetails(data) {
            const tenant = data.tenant;
            const adminUser = data.admin_user;
            const documents = data.documents;
            const isRejected = tenant.status === 'rejected';
            const isPending = tenant.status === 'pending';

            const modalBody = document.getElementById('tenantModalBody');
            
            modalBody.innerHTML = `
                <div class="detail-section">
                    <h4><i class="fas fa-building"></i> Business Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Business Name</span>
                            <span class="detail-value">${tenant.name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Business Type</span>
                            <span class="detail-value">${tenant.business_type || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact Email</span>
                            <span class="detail-value">${tenant.contact_email || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact Phone</span>
                            <span class="detail-value">${tenant.contact_phone || 'N/A'}</span>
                        </div>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">${tenant.address || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">City</span>
                            <span class="detail-value">${tenant.city || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Country</span>
                            <span class="detail-value">${tenant.country || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Certification Type</span>
                            <span class="detail-value">${tenant.certification_type || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">TIN/VAT Number</span>
                            <span class="detail-value">${tenant.tin_vat_number || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Registration Date</span>
                            <span class="detail-value">${tenant.created_at || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Current Status</span>
                            <span class="detail-value">
                                <span class="status-badge ${getStatusClass(tenant.status)}">
                                    ${tenant.status ? tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1) : 'Unknown'}
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
                            <span class="detail-value">${adminUser.name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Username</span>
                            <span class="detail-value">${adminUser.username || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${adminUser.email || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">${adminUser.phone || 'N/A'}</span>
                        </div>
                    </div>
                </div>
                ` : '<p style="color: #6b7280; padding: 20px; text-align: center;">No admin user information available</p>'}

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
                    ${isPending ? `
                        <button type="button" class="btn btn-danger" onclick="showRejectionModal()">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    ` : ''}
                    ${(isPending || isRejected) ? `
                        <button type="button" class="btn btn-success" onclick="approveTenant()">
                            <i class="fas fa-check"></i> ${isRejected ? 'Re-approve' : 'Approve'}
                        </button>
                    ` : ''}
                </div>
            `;
        }

        function generateDocumentCard(name, filePath, tenantId, documentType) {
            const hasDocument = filePath && filePath.trim() !== '';
            
            return `
                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas ${getDocumentIcon(documentType)}"></i>
                    </div>
                    <div class="document-name">${name}</div>
                    ${hasDocument ? `
                        <button class="btn btn-primary btn-sm" onclick="viewDocument('${tenantId}', '${documentType}', '${name}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="downloadDocument('${tenantId}', '${documentType}')">
                            <i class="fas fa-download"></i> Download
                        </button>
                    ` : `
                        <span style="color: #ef4444; font-size: 12px;">Not uploaded</span>
                    `}
                </div>
            `;
        }

        function getDocumentIcon(documentType) {
            switch(documentType) {
                case 'business_license': return 'fa-certificate';
                case 'tax_certificate': return 'fa-file-invoice';
                case 'owner_id': return 'fa-id-card';
                case 'registration_certificate': return 'fa-file-contract';
                default: return 'fa-file-alt';
            }
        }

        function getStatusClass(status) {
            switch(status) {
                case 'pending': return 'status-pending';
                case 'rejected': return 'status-rejected';
                case 'verified': return 'status-verified';
                default: return 'status-pending';
            }
        }

        function viewDocument(tenantId, documentType, documentName) {
            // Construct the document URL and download URL
            const documentUrl = `/superadmin/tenants/${tenantId}/documents/${documentType}`;
            const downloadUrl = `/superadmin/tenants/${tenantId}/documents/${documentType}/download`;
            
            // Use the document viewer component function
            openDocumentViewer(documentUrl, documentName, downloadUrl);
        }

        function downloadDocument(tenantId, documentType) {
            const downloadUrl = `/superadmin/tenants/${tenantId}/documents/${documentType}/download`;
            
            // Create a temporary link and click it to trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function approveTenant() {
            if (!currentTenantId) return;

            const action = currentTenantStatus === 'rejected' ? 're-approve' : 'approve';
            const confirmMessage = `Are you sure you want to ${action} this business registration?`;

            if (confirm(confirmMessage)) {
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
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing request');
                });
            }
        }

        function showRejectionModal() {
            document.getElementById('rejectionModal').style.display = 'block';
        }

        function closeTenantModal() {
            document.getElementById('tenantModal').style.display = 'none';
            currentTenantId = null;
            currentTenantStatus = null;
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').style.display = 'none';
            document.getElementById('rejectionForm').reset();
        }

        function closeDocumentModal() {
            document.getElementById('documentModal').style.display = 'none';
            document.getElementById('documentViewer').src = '';
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
                    closeRejectionModal();
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
            const documentModal = document.getElementById('documentModal');
            
            if (event.target === tenantModal) {
                closeTenantModal();
            }
            if (event.target === rejectionModal) {
                closeRejectionModal();
            }
            if (event.target === documentModal) {
                closeDocumentModal();
            }
        });
    </script>
</body>
</html>
