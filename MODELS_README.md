# Hotel Management System - Laravel Eloquent Models

This repository contains comprehensive Laravel Eloquent models for a multi-tenant hotel management system. The models are designed to handle all aspects of hotel operations including reservations, room management, services, payments, and reporting.

## Overview

The system is built with multi-tenancy in mind, allowing multiple hotel chains or individual hotels to operate independently within the same application instance. Each tenant has isolated data and customizable settings.

## Core Features

- **Multi-tenant architecture** using Spatie Laravel Multitenancy
- **UUID primary keys** for enhanced security and scalability
- **Soft deletes** for data integrity
- **Audit logging** with Owen-it Laravel Auditing
- **Role-based permissions** with Spatie Laravel Permission
- **Comprehensive relationships** between all entities
- **Business logic methods** for common operations
- **Reporting and analytics** capabilities
- **Status management** with constants and display methods

## Model Architecture

### Core Models

#### 1. Tenant
The root model for multi-tenancy support.
- **Purpose**: Represents a hotel chain, individual hotel, or management company
- **Key Features**: Subscription management, settings, timezone and currency configuration
- **Relationships**: 
  - Has many Hotels
  - Has many Users
  - Has many TenantSettings

#### 2. User
Enhanced user model supporting staff and guests.
- **Purpose**: Represents all system users (admins, managers, staff, guests)
- **Key Features**: Role management, employee information, guest profiles
- **User Types**: Admin, Manager, Staff, Guest
- **Relationships**:
  - Belongs to Tenant
  - Has many Reservations (as guest)
  - Has many Payments
  - Has many ServiceOrders

#### 3. Hotel
Represents individual hotel properties.
- **Purpose**: Hotel property management with operational details
- **Key Features**: Location data, policies, operating hours, star rating
- **Relationships**:
  - Belongs to Tenant
  - Belongs to User (manager)
  - Has many Rooms
  - Has many Reservations
  - Has many Services
  - Belongs to many Amenities

#### 4. Room
Individual room management.
- **Purpose**: Represents physical rooms with status tracking
- **Key Features**: Room status, maintenance tracking, accessibility options
- **Statuses**: Available, Occupied, Out of Order, Maintenance, Cleaning, Dirty
- **Relationships**:
  - Belongs to Hotel
  - Belongs to RoomType
  - Has many Reservations
  - Has many RoomMaintenance records

#### 5. RoomType
Room categorization and pricing.
- **Purpose**: Defines room categories with base pricing and amenities
- **Key Features**: Capacity, bed configuration, pricing rules
- **Relationships**:
  - Belongs to Hotel
  - Has many Rooms
  - Has many RoomTypePricing rules
  - Belongs to many Amenities

#### 6. Reservation
Booking and reservation management.
- **Purpose**: Handles guest bookings with complete lifecycle management
- **Key Features**: Check-in/out dates, guest information, payment tracking
- **Statuses**: Pending, Confirmed, Checked In, Checked Out, Cancelled, No Show
- **Relationships**:
  - Belongs to Hotel, Room, RoomType
  - Belongs to User (guest)
  - Has many Payments
  - Has many ServiceOrders
  - Has many ReservationGuests

#### 7. Payment
Payment processing and tracking.
- **Purpose**: Manages all payment transactions and refunds
- **Key Features**: Multiple payment methods, gateway integration, refund tracking
- **Methods**: Cash, Credit Card, Bank Transfer, PayPal, Stripe, etc.
- **Relationships**:
  - Belongs to Reservation
  - Belongs to User

### Supporting Models

#### Service Management
- **Service**: Hotel services (room service, spa, restaurant, etc.)
- **ServiceOrder**: Service bookings and requests
- **ServiceItem**: Individual service components (menu items, treatments)
- **ServiceOrderItem**: Line items for service orders

#### Amenities and Features
- **Amenity**: Hotel and room amenities with categorization
- **RoomTypePricing**: Dynamic pricing rules for seasonal/special rates

#### Maintenance and Operations
- **RoomMaintenance**: Maintenance scheduling and tracking
- **ReservationGuest**: Additional guests for group bookings

#### Configuration
- **TenantSetting**: Configurable tenant-specific settings

## Utility Traits

### AuditableScope
Provides common scopes for date-based filtering:
- `createdToday()`, `createdThisWeek()`, `createdThisMonth()`
- `updatedToday()`, `recentlyCreated()`, `recentlyUpdated()`
- `createdBetween()`, `updatedBetween()`
- `latest()`, `oldest()`

### ReportingHelpers
Analytics and reporting functionality:
- Daily, monthly, and yearly statistics
- Revenue tracking and performance metrics
- Growth rate calculations
- Distribution analysis
- Time-based analytics

## Key Relationships

```
Tenant
├── Hotels
│   ├── Rooms
│   │   ├── Reservations
│   │   └── RoomMaintenance
│   ├── RoomTypes
│   │   ├── Rooms
│   │   └── RoomTypePricing
│   ├── Services
│   │   ├── ServiceOrders
│   │   └── ServiceItems
│   └── Amenities
├── Users
│   ├── Reservations (as guest)
│   ├── Payments
│   └── ServiceOrders
└── TenantSettings

Reservations
├── Payments
├── ServiceOrders
└── ReservationGuests
```

## Business Logic Features

### Availability Management
- Room availability checking for date ranges
- Conflict detection for reservations
- Maintenance scheduling coordination

### Pricing and Revenue
- Dynamic pricing based on seasonality and demand
- Revenue calculation and reporting
- Payment tracking and reconciliation

### Service Management
- Service ordering and fulfillment tracking
- Menu/catalog management for services
- Priority and status management

### Reporting and Analytics
- Occupancy rate calculations
- Revenue analysis
- Performance metrics
- Growth tracking

## Security Features

- **UUID Primary Keys**: Enhanced security and prevents enumeration attacks
- **Multi-tenant Isolation**: Complete data separation between tenants
- **Soft Deletes**: Data integrity and audit trail maintenance
- **Audit Logging**: Complete change tracking for compliance
- **Role-based Access**: Granular permission system

## Usage Examples

### Check Room Availability
```php
$hotel = Hotel::find($hotelId);
$availableRooms = $hotel->availableRooms($checkIn, $checkOut);
```

### Calculate Hotel Occupancy
```php
$occupancyRate = $hotel->getOccupancyRate($startDate, $endDate);
```

### Get Revenue Statistics
```php
$stats = Reservation::getRevenueStats($startDate, $endDate);
```

### Process Service Order
```php
$order = ServiceOrder::create([
    'reservation_id' => $reservation->id,
    'service_id' => $service->id,
    'status' => ServiceOrder::STATUS_PENDING,
    // ... other fields
]);
```

## Installation Requirements

Add these packages to your `composer.json`:

```json
{
    "require": {
        "spatie/laravel-multitenancy": "^3.0",
        "ramsey/uuid": "^4.0",
        "spatie/laravel-permission": "^6.0",
        "owen-it/laravel-auditing": "^13.0"
    }
}
```

## Database Considerations

- All models use UUID primary keys
- Foreign key relationships maintain referential integrity
- Indexes should be created on frequently queried fields
- Soft deletes are implemented where data retention is important
- Audit tables will be created automatically for change tracking

## Customization

The models are designed to be extensible:
- Add custom scopes for specific business logic
- Extend relationships as needed
- Implement custom validation rules
- Add tenant-specific configuration through TenantSetting

## Support for Future Features

The architecture supports future enhancements:
- Online booking integration
- Revenue management systems
- Property management system integration
- Mobile application APIs
- Third-party service integrations
- Advanced reporting and business intelligence