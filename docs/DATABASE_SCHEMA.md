# Hotel Management System - Database Schema Documentation

## Overview

This document describes the database schema for the hotel management system, focusing on the property, building, floor, and room management modules. The system follows a multi-tenant architecture where each tenant can manage multiple properties.

## Database Design Principles

- **Multi-tenant Architecture**: Data is isolated by `tenant_id` 
- **Hierarchical Structure**: Property → Building → Floor → Room
- **UUID Primary Keys**: All tables use UUID for better scalability
- **Soft Deletes**: Important entities support soft deletion
- **Foreign Key Constraints**: Proper referential integrity
- **Indexes**: Optimized for common query patterns

## Table Structure

### Core Tables Overview

```
tenants (existing)
├── properties (existing)
    ├── buildings (new)
    │   └── floors (new)
    │       └── rooms (new)
    ├── room_types (new)
    └── room_features (new)
        └── room_features_map (new)
```

---

## 1. Buildings Table

**Table Name**: `buildings`  
**Purpose**: Stores building information within properties  
**Migration**: `2025_09_21_100001_create_buildings_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique building identifier |
| `property_id` | UUID | NOT NULL, FOREIGN KEY | Reference to properties table |
| `name` | VARCHAR(100) | NOT NULL | Building name (e.g., "Main Building", "Tower A") |
| `description` | TEXT | NULLABLE | Optional building description |
| `created_at` | TIMESTAMP | NOT NULL | Record creation time |
| `updated_at` | TIMESTAMP | NOT NULL | Record last update time |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Constraints & Indexes

- **Foreign Keys**:
  - `property_id` → `properties(id)` ON DELETE CASCADE
- **Unique Constraints**:
  - `(property_id, name)` - Unique building name per property
- **Indexes**:
  - `property_id` - For property-based queries

### Business Rules

- Each property can have multiple buildings
- Building names must be unique within a property
- Deleting a property cascades to delete all buildings
- Buildings support soft deletion

---

## 2. Floors Table

**Table Name**: `floors`  
**Purpose**: Stores floor information within buildings  
**Migration**: `2025_09_21_100002_create_floors_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique floor identifier |
| `building_id` | UUID | NOT NULL, FOREIGN KEY | Reference to buildings table |
| `number` | INTEGER | NOT NULL, CHECK > 0 | Floor number (1, 2, 3, etc.) |
| `description` | TEXT | NULLABLE | Optional floor description |
| `created_at` | TIMESTAMP | NOT NULL | Record creation time |
| `updated_at` | TIMESTAMP | NOT NULL | Record last update time |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Constraints & Indexes

- **Foreign Keys**:
  - `building_id` → `buildings(id)` ON DELETE CASCADE
- **Unique Constraints**:
  - `(building_id, number)` - Unique floor number per building
- **Check Constraints**:
  - `number > 0` - Floor numbers must be positive
- **Indexes**:
  - `building_id` - For building-based queries

### Business Rules

- Each building can have multiple floors
- Floor numbers must be unique within a building
- Floor numbers must be positive integers
- Deleting a building cascades to delete all floors
- Floors support soft deletion

---

## 3. Room Types Table

**Table Name**: `room_types`  
**Purpose**: Defines different types of rooms available in properties  
**Migration**: `2025_09_21_100003_create_room_types_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique room type identifier |
| `property_id` | UUID | NOT NULL, FOREIGN KEY | Reference to properties table |
| `name` | VARCHAR(100) | NOT NULL | Room type name (e.g., "Standard", "Deluxe") |
| `description` | TEXT | NULLABLE | Room type description |
| `capacity` | INTEGER | NOT NULL, CHECK > 0 | Maximum occupancy |
| `base_rate` | DECIMAL(18,4) | NOT NULL, CHECK >= 0 | Base room rate |
| `created_at` | TIMESTAMP | NOT NULL | Record creation time |
| `updated_at` | TIMESTAMP | NOT NULL | Record last update time |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Constraints & Indexes

- **Foreign Keys**:
  - `property_id` → `properties(id)` ON DELETE CASCADE
- **Unique Constraints**:
  - `(property_id, name)` - Unique room type name per property
- **Check Constraints**:
  - `capacity > 0` - Capacity must be positive
  - `base_rate >= 0` - Rate cannot be negative
- **Indexes**:
  - `property_id` - For property-based queries

### Business Rules

- Each property can define its own room types
- Room type names must be unique within a property
- Capacity and rates must be positive values
- Room types support soft deletion

---

## 4. Room Features Table

**Table Name**: `room_features`  
**Purpose**: Defines available features/amenities for rooms  
**Migration**: `2025_09_21_100004_create_room_features_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique feature identifier |
| `tenant_id` | UUID | NOT NULL, FOREIGN KEY | Reference to tenants table |
| `name` | VARCHAR(100) | NOT NULL | Feature name (e.g., "WiFi", "AC") |
| `description` | TEXT | NULLABLE | Feature description |
| `created_at` | TIMESTAMP | NOT NULL, DEFAULT CURRENT | Record creation time |

### Constraints & Indexes

- **Foreign Keys**:
  - `tenant_id` → `tenants(id)` ON DELETE CASCADE
- **Unique Constraints**:
  - `(tenant_id, name)` - Unique feature name per tenant
- **Indexes**:
  - `tenant_id` - For tenant-based queries

### Business Rules

- Features are defined at tenant level (shared across properties)
- Feature names must be unique within a tenant
- Features can be assigned to multiple rooms
- No soft deletion (permanent features)

---

## 5. Rooms Table

**Table Name**: `rooms`  
**Purpose**: Stores individual room information  
**Migration**: `2025_09_21_100005_create_rooms_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique room identifier |
| `property_id` | UUID | NOT NULL, FOREIGN KEY | Reference to properties table |
| `floor_id` | UUID | NULLABLE, FOREIGN KEY | Reference to floors table |
| `room_type_id` | UUID | NOT NULL, FOREIGN KEY | Reference to room_types table |
| `room_number` | VARCHAR(50) | NOT NULL | Room number/identifier |
| `status` | ENUM | NOT NULL, DEFAULT 'CLEAN' | Current room status |
| `current_rate` | DECIMAL(18,4) | NOT NULL, CHECK >= 0 | Current room rate |
| `notes` | TEXT | NULLABLE | Additional room notes |
| `created_at` | TIMESTAMP | NOT NULL | Record creation time |
| `updated_at` | TIMESTAMP | NOT NULL | Record last update time |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

### Room Status Values

- `OCCUPIED` - Room is currently occupied
- `VACANT` - Room is empty and ready
- `DIRTY` - Room needs cleaning
- `CLEAN` - Room is clean and ready
- `OUT_OF_ORDER` - Room has maintenance issues
- `CLEANING_IN_PROGRESS` - Room is being cleaned
- `INSPECTED` - Room has been inspected

### Constraints & Indexes

- **Foreign Keys**:
  - `property_id` → `properties(id)` ON DELETE CASCADE
  - `floor_id` → `floors(id)` ON DELETE SET NULL
  - `room_type_id` → `room_types(id)` ON DELETE RESTRICT
- **Unique Constraints**:
  - `(property_id, room_number)` - Unique room number per property
- **Check Constraints**:
  - `current_rate >= 0` - Rate cannot be negative
- **Indexes**:
  - `property_id` - Property-based queries
  - `floor_id` - Floor-based queries  
  - `room_type_id` - Room type queries
  - `status` - Status-based filtering

### Business Rules

- Room numbers must be unique within a property
- Rooms can optionally be assigned to floors
- Room type cannot be deleted if rooms exist (RESTRICT)
- Current rate must be non-negative
- Rooms support soft deletion

---

## 6. Room Features Map Table

**Table Name**: `room_features_map`  
**Purpose**: Many-to-many relationship between rooms and features  
**Migration**: `2025_09_21_100006_create_room_features_map_table.php`

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `room_id` | UUID | NOT NULL, FOREIGN KEY | Reference to rooms table |
| `feature_id` | UUID | NOT NULL, FOREIGN KEY | Reference to room_features table |

### Constraints & Indexes

- **Primary Key**:
  - `(room_id, feature_id)` - Composite primary key
- **Foreign Keys**:
  - `room_id` → `rooms(id)` ON DELETE CASCADE
  - `feature_id` → `room_features(id)` ON DELETE CASCADE
- **Indexes**:
  - `room_id` - Room-based queries
  - `feature_id` - Feature-based queries

### Business Rules

- Many-to-many relationship between rooms and features
- A room can have multiple features
- A feature can be assigned to multiple rooms
- Deleting a room or feature removes all mappings

---

## Relationships Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   tenants   │◄─────┤ properties  │◄─────┤  buildings  │
└─────────────┘       └─────────────┘       └─────────────┘
                              │                     │
                              │                     ▼
                              │             ┌─────────────┐
                              │             │   floors    │
                              │             └─────────────┘
                              │                     │
                              ▼                     │
                      ┌─────────────┐               │
                      │ room_types  │               │
                      └─────────────┘               │
                              │                     │
                              ▼                     ▼
                      ┌─────────────┐         ┌─────────────┐
                      │    rooms    │◄────────┤   floors    │
                      └─────────────┘         └─────────────┘
                              │
                              ▼
                    ┌─────────────────┐       ┌─────────────┐
                    │room_features_map│◄─────┤room_features│
                    └─────────────────┘       └─────────────┘
                                                      ▲
                                                      │
                                              ┌─────────────┐
                                              │   tenants   │
                                              └─────────────┘
```

## Migration Order

The migrations must be run in this order due to foreign key dependencies:

1. `2025_09_21_100001_create_buildings_table.php`
2. `2025_09_21_100002_create_floors_table.php`
3. `2025_09_21_100003_create_room_types_table.php`
4. `2025_09_21_100004_create_room_features_table.php`
5. `2025_09_21_100005_create_rooms_table.php`
6. `2025_09_21_100006_create_room_features_map_table.php`

## Common Queries

### Get all rooms in a property with their details:
```sql
SELECT r.*, rt.name as room_type, f.number as floor_number, b.name as building_name
FROM rooms r
JOIN room_types rt ON r.room_type_id = rt.id
LEFT JOIN floors f ON r.floor_id = f.id
LEFT JOIN buildings b ON f.building_id = b.id
WHERE r.property_id = ? AND r.deleted_at IS NULL;
```

### Get room occupancy statistics:
```sql
SELECT 
    COUNT(*) as total_rooms,
    COUNT(CASE WHEN status = 'OCCUPIED' THEN 1 END) as occupied,
    COUNT(CASE WHEN status IN ('CLEAN', 'VACANT') THEN 1 END) as available
FROM rooms 
WHERE property_id = ? AND deleted_at IS NULL;
```

### Get rooms with specific features:
```sql
SELECT r.*, GROUP_CONCAT(rf.name) as features
FROM rooms r
LEFT JOIN room_features_map rfm ON r.id = rfm.room_id
LEFT JOIN room_features rf ON rfm.feature_id = rf.id
WHERE r.property_id = ?
GROUP BY r.id;
```

## Performance Considerations

- All foreign key columns are indexed
- Composite unique constraints prevent duplicate data
- Soft deletes allow data recovery while maintaining performance
- Status column is indexed for quick room availability queries
- Consider partitioning by tenant_id for very large datasets

## Security & Data Integrity

- UUID primary keys prevent enumeration attacks
- Foreign key constraints ensure referential integrity
- Check constraints validate business rules at database level
- Soft deletes preserve audit trails
- Unique constraints prevent duplicate entries

---

*Last Updated: September 21, 2025*
*Version: 1.0*