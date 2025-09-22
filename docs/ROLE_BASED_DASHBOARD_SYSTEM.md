# Role-Based Dashboard System

## Overview
This hotel management system implements a comprehensive role-based dashboard system with distinct interfaces and functionality for each user role. The system ensures proper separation of concerns and prevents code entanglement by organizing views, sidebars, and functionality by role.

## Role Structure

### 1. DIRECTOR
- **Access Level**: Highest - Full system access
- **Dashboard**: Main dashboard (`Users.Dashboard`)
- **Sidebar**: Director-specific (`Users.shared.sidebars.director`)
- **Color Theme**: Blue gradient (#1a237e)
- **Key Features**:
  - Property management
  - User management across all roles
  - System-wide analytics
  - Financial oversight
  - Business operations

### 2. MANAGER
- **Access Level**: Property-level management
- **Dashboard**: Manager dashboard (`Users.tenant.users.dashboards.manager`)
- **Sidebar**: Manager-specific (`Users.shared.sidebars.manager`)
- **Color Theme**: Green gradient (#4caf50)
- **Key Features**:
  - Property operations management
  - Staff management (excluding directors/managers)
  - Building management
  - Operational reporting
  - Guest services oversight

### 3. SUPERVISOR
- **Access Level**: Team coordination
- **Dashboard**: Supervisor dashboard (`Users.tenant.users.dashboards.supervisor`)
- **Sidebar**: Supervisor-specific (`Users.shared.sidebars.supervisor`)
- **Color Theme**: Orange gradient (#ff9800)
- **Key Features**:
  - Team coordination
  - Task assignment and monitoring
  - Quality control
  - Staff attendance
  - Performance tracking

### 4. ACCOUNTANT
- **Access Level**: Financial operations
- **Dashboard**: Accountant dashboard (`Users.tenant.users.dashboards.accountant`)
- **Sidebar**: Accountant-specific (`Users.shared.sidebars.accountant`)
- **Color Theme**: Blue gradient (#1565c0)
- **Key Features**:
  - Financial management
  - Invoice generation
  - Payment processing
  - Expense tracking
  - Financial reporting
  - Tax management

### 5. BAR_TENDER
- **Access Level**: Bar operations
- **Dashboard**: Bar tender dashboard (`Users.tenant.users.dashboards.bar_tender`)
- **Sidebar**: Bar tender-specific (`Users.shared.sidebars.bar_tender`)
- **Color Theme**: Purple gradient (#7b1fa2)
- **Key Features**:
  - Order management
  - Inventory tracking
  - Sales processing
  - Menu management
  - Customer service

### 6. RECEPTIONIST
- **Access Level**: Front desk operations
- **Dashboard**: Receptionist dashboard (`Users.tenant.users.dashboards.receptionist`)
- **Sidebar**: Receptionist-specific (`Users.shared.sidebars.receptionist`)
- **Color Theme**: Pink gradient (#e91e63)
- **Key Features**:
  - Guest check-in/check-out
  - Reservation management
  - Guest services
  - Room status management
  - Customer communication

### 7. HOUSEKEEPER
- **Access Level**: Housekeeping operations
- **Dashboard**: Housekeeper dashboard (`Users.tenant.users.dashboards.housekeeper`)
- **Sidebar**: Housekeeper-specific (`Users.shared.sidebars.housekeeper`)
- **Color Theme**: Brown gradient (#795548)
- **Key Features**:
  - Room cleaning schedules
  - Inventory management
  - Maintenance reporting
  - Quality inspections
  - Task tracking

## Technical Implementation

### Authentication Flow
1. User logs in through `AuthController`
2. System checks user role
3. Directors are routed to main dashboard
4. Other roles are redirected to `user.dashboard` route
5. `UserController::dashboard()` determines appropriate view based on role

### Dashboard Architecture

#### Shared Layout
- Base layout: `Users.shared.layouts.dashboard`
- Provides common structure, styles, and functionality
- Supports role-specific customization through sections and stacks

#### Role-Specific Components
- **Sidebars**: Located in `Users.shared.sidebars.{role}`
- **Dashboards**: Located in `Users.tenant.users.dashboards.{role}`
- **Styles**: Role-specific color themes and UI elements

### File Organization
```
resources/views/
├── Users/
│   ├── Dashboard.blade.php (Director main dashboard)
│   ├── shared/
│   │   ├── layouts/
│   │   │   └── dashboard.blade.php (Base dashboard layout)
│   │   └── sidebars/
│   │       ├── director.blade.php
│   │       ├── manager.blade.php
│   │       ├── supervisor.blade.php
│   │       ├── accountant.blade.php
│   │       ├── bar_tender.blade.php
│   │       ├── receptionist.blade.php
│   │       └── housekeeper.blade.php
│   └── tenant/
│       └── users/
│           └── dashboards/
│               ├── manager.blade.php
│               ├── supervisor.blade.php
│               ├── accountant.blade.php
│               ├── bar_tender.blade.php
│               ├── receptionist.blade.php
│               └── housekeeper.blade.php
```

### Controller Methods

#### AuthController
- `showDashboard()`: Routes directors to main dashboard, others to role-specific dashboards

#### UserController
- `dashboard()`: Determines and renders appropriate role-specific dashboard
- `getDashboardStats()`: AJAX endpoint for real-time dashboard statistics
- `getDashboardData()`: Compiles role-specific data for dashboard display
- `getDashboardView()`: Maps roles to their respective view files

### Routes
```php
// Main dashboard (directors)
Route::get('/dashboard', [AuthController::class, 'showDashboard'])->name('dashboard');

// Role-specific dashboard
Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

// Dashboard statistics API
Route::get('/user-dashboard/stats', [UserController::class, 'getDashboardStats'])->name('user.dashboard.stats');
```

## Features

### Real-time Updates
- Dashboard statistics update every 30 seconds
- AJAX-based stat refreshing
- Role-specific data points

### Responsive Design
- Mobile-friendly layouts
- Adaptive sidebar behavior
- Touch-optimized interfaces

### Security
- Role-based access control
- Property-level data isolation
- Tenant-aware queries

### User Experience
- Role-appropriate color schemes
- Intuitive navigation
- Context-aware functionality
- Loading states and feedback

## Future Enhancements

### Planned Features
1. **Real-time Notifications**: Role-specific notification systems
2. **Advanced Analytics**: Role-based reporting and insights
3. **Task Management**: Integrated task assignment and tracking
4. **Inventory Integration**: Real-time inventory tracking
5. **Reservation System**: Complete booking management
6. **Financial Integration**: Full accounting and payment processing

### Scalability Considerations
- Modular component architecture
- API-first dashboard data
- Cacheable statistics
- Lazy loading for large datasets

## Development Guidelines

### Adding New Roles
1. Create role-specific sidebar in `Users.shared.sidebars.{role}.blade.php`
2. Create role-specific dashboard in `Users.tenant.users.dashboards.{role}.blade.php`
3. Add role mapping in `UserController::getDashboardView()`
4. Add role data logic in `UserController::getDashboardData()`
5. Update role hierarchy in permission checking methods

### Customizing Dashboards
1. Extend the base layout (`Users.shared.layouts.dashboard`)
2. Override sections for role-specific styling
3. Add role-specific JavaScript in `@push('scripts')`
4. Include role-specific CSS in `@push('styles')`

### Maintaining Separation
- Keep role-specific logic in respective controllers
- Use shared components for common functionality
- Avoid cross-role dependencies
- Document role permissions clearly

## Testing
- Unit tests for role-based routing
- Integration tests for dashboard data
- UI tests for role-specific interfaces
- Permission tests for access control

## Deployment
- Ensure all role views are deployed
- Verify route configurations
- Test role-based redirections
- Validate permission hierarchies