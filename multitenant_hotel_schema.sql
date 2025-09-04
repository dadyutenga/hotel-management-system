-- Enable extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- Create schemas
CREATE SCHEMA IF NOT EXISTS core;    -- Core tenant and property data
CREATE SCHEMA IF NOT EXISTS auth;    -- Authentication and user management
CREATE SCHEMA IF NOT EXISTS res;     -- Reservations and bookings
CREATE SCHEMA IF NOT EXISTS fin;     -- Financial transactions and accounting
CREATE SCHEMA IF NOT EXISTS inv;     -- Inventory and stock management
CREATE SCHEMA IF NOT EXISTS pos;     -- Point of sale functionality
CREATE SCHEMA IF NOT EXISTS ops;     -- Operations (housekeeping, maintenance)
CREATE SCHEMA IF NOT EXISTS report;  -- Reporting views and functions

-----------------------------------------------------
-- CORE SCHEMA - Tenant and property management
-----------------------------------------------------

-- Superadmin users who manage the entire system
CREATE TABLE auth.superadmins (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- core.tenants - Represents businesses using the system
CREATE TABLE core.tenants (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(50) NOT NULL,
    certification_proof VARCHAR(255),
    business_type VARCHAR(50) NOT NULL CHECK (business_type IN ('HOTEL', 'LODGE', 'RESTAURANT', 'BAR', 'PUB')),
    subscription_level VARCHAR(50) NOT NULL DEFAULT 'BASIC',
    subscription_expires_at TIMESTAMPTZ,
    base_currency CHAR(3) NOT NULL DEFAULT 'USD' CHECK (LENGTH(base_currency) = 3),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.superadmins(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ
);

-- core.tenant_staff_limits - Defines the maximum number of staff allowed per role
CREATE TABLE core.tenant_staff_limits (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    role VARCHAR(50) NOT NULL CHECK (role IN ('DIRECTOR', 'MANAGER', 'SUPERVISOR', 'ACCOUNTANT', 'BAR_TENDER', 'RECEPTIONIST', 'HOUSEKEEPER')),
    max_count INT NOT NULL CHECK (max_count > 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, role)
);

-- core.properties - Represents physical properties managed by the tenant
CREATE TABLE core.properties (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    contact_phone VARCHAR(50),
    email VARCHAR(255),
    website VARCHAR(255),
    star_rating INT CHECK (star_rating BETWEEN 1 AND 5),
    checkin_time TIME NOT NULL DEFAULT '14:00',
    checkout_time TIME NOT NULL DEFAULT '11:00',
    timezone VARCHAR(50) DEFAULT 'UTC',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, name)
);

-- core.buildings - Buildings within a property
CREATE TABLE core.buildings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (property_id, name)
);

-- core.floors - Floors within buildings
CREATE TABLE core.floors (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    building_id UUID NOT NULL REFERENCES core.buildings(id) ON DELETE CASCADE ON UPDATE CASCADE,
    number INT NOT NULL CHECK (number > 0),
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (building_id, number)
);

-- core.room_types - Types of rooms offered
CREATE TABLE core.room_types (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT NOT NULL CHECK (capacity > 0),
    base_rate NUMERIC(18,4) NOT NULL CHECK (base_rate >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (property_id, name)
);

-- core.room_features - Features available in rooms
CREATE TABLE core.room_features (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, name)
);

-- core.rooms - Individual rooms
CREATE TABLE core.rooms (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE ON UPDATE CASCADE,
    floor_id UUID REFERENCES core.floors(id) ON DELETE SET NULL ON UPDATE CASCADE,
    room_type_id UUID NOT NULL REFERENCES core.room_types(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    room_number VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('OCCUPIED', 'VACANT', 'DIRTY', 'CLEAN', 'OUT_OF_ORDER', 'CLEANING_IN_PROGRESS', 'INSPECTED')),
    current_rate NUMERIC(18,4) NOT NULL CHECK (current_rate >= 0),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (property_id, room_number)
);

-- core.room_features_map - Mapping between rooms and features
CREATE TABLE core.room_features_map (
    room_id UUID NOT NULL REFERENCES core.rooms(id) ON DELETE CASCADE ON UPDATE CASCADE,
    feature_id UUID NOT NULL REFERENCES core.room_features(id) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (room_id, feature_id)
);

-- core.rate_plans - Different rate plans for room types
CREATE TABLE core.rate_plans (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    room_type_id UUID NOT NULL REFERENCES core.room_types(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    rate NUMERIC(18,4) NOT NULL CHECK (rate >= 0),
    start_date DATE NOT NULL,
    end_date DATE CHECK (end_date IS NULL OR end_date > start_date),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (room_type_id, name)
);

-- core.seasonal_rates - Seasonal adjustments to rates
CREATE TABLE core.seasonal_rates (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    rate_plan_id UUID NOT NULL REFERENCES core.rate_plans(id) ON DELETE CASCADE ON UPDATE CASCADE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL CHECK (end_date > start_date),
    adjustment_factor NUMERIC(5,2) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-----------------------------------------------------
-- AUTH SCHEMA - User management and authentication
-----------------------------------------------------

-- auth.roles - Predefined system roles
CREATE TABLE auth.roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(50) NOT NULL UNIQUE CHECK (name IN ('DIRECTOR', 'MANAGER', 'SUPERVISOR', 'ACCOUNTANT', 'BAR_TENDER', 'RECEPTIONIST', 'HOUSEKEEPER')),
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- auth.permissions - Available permissions in the system
CREATE TABLE auth.permissions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    code VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- auth.role_permissions - Mapping roles to permissions
CREATE TABLE auth.role_permissions (
    role_id UUID NOT NULL REFERENCES auth.roles(id) ON DELETE CASCADE,
    permission_id UUID NOT NULL REFERENCES auth.permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

-- auth.users - All system users (employees)
CREATE TABLE auth.users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    property_id UUID REFERENCES core.properties(id) ON DELETE SET NULL ON UPDATE CASCADE,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role_id UUID NOT NULL REFERENCES auth.roles(id) ON DELETE RESTRICT,
    phone VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    mfa_enabled BOOLEAN DEFAULT FALSE,
    mfa_secret VARCHAR(255),
    last_login_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, username),
    UNIQUE (tenant_id, email)
);

-- auth.user_custom_permissions - Override or add permissions for specific users
CREATE TABLE auth.user_custom_permissions (
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    permission_id UUID NOT NULL REFERENCES auth.permissions(id) ON DELETE CASCADE,
    is_granted BOOLEAN NOT NULL DEFAULT TRUE,
    granted_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    granted_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (user_id, permission_id)
);

-- auth.login_attempts - Track login attempts for security
CREATE TABLE auth.login_attempts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    username VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN NOT NULL,
    attempted_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- auth.password_reset_tokens - Store password reset tokens
CREATE TABLE auth.password_reset_tokens (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMPTZ NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    used_at TIMESTAMPTZ
);

-----------------------------------------------------
-- RES SCHEMA - Reservation management
-----------------------------------------------------

-- res.guests - Guest profiles
CREATE TABLE res.guests (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    nationality VARCHAR(50),
    id_type VARCHAR(20),
    id_number VARCHAR(50),
    date_of_birth DATE,
    gender VARCHAR(10),
    preferences JSONB,
    loyalty_program_info JSONB,
    marketing_consent BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, email) WHERE email IS NOT NULL
);

-- res.guest_contacts - Additional contact details for guests
CREATE TABLE res.guest_contacts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    guest_id UUID NOT NULL REFERENCES res.guests(id) ON DELETE CASCADE ON UPDATE CASCADE,
    type VARCHAR(20) NOT NULL CHECK (type IN ('PHONE', 'EMAIL', 'ADDRESS')),
    value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- res.corporate_accounts - Corporate clients
CREATE TABLE res.corporate_accounts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    billing_address TEXT,
    tax_id VARCHAR(50),
    credit_limit NUMERIC(18,4) CHECK (credit_limit >= 0),
    payment_terms VARCHAR(50),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, name)
);

-- res.group_bookings - Group reservations
CREATE TABLE res.group_bookings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    leader_guest_id UUID REFERENCES res.guests(id) ON DELETE SET NULL,
    corporate_account_id UUID REFERENCES res.corporate_accounts(id) ON DELETE SET NULL,
    total_rooms INT NOT NULL CHECK (total_rooms > 0),
    arrival_date DATE NOT NULL,
    departure_date DATE NOT NULL CHECK (departure_date > arrival_date),
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING' CHECK (status IN ('PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED')),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ
);

-- res.reservations - Individual reservations
CREATE TABLE res.reservations (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE ON UPDATE CASCADE,
    guest_id UUID NOT NULL REFERENCES res.guests(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    group_booking_id UUID REFERENCES res.group_bookings(id) ON DELETE SET NULL ON UPDATE CASCADE,
    corporate_account_id UUID REFERENCES res.corporate_accounts(id) ON DELETE SET NULL ON UPDATE CASCADE,
    status VARCHAR(20) NOT NULL CHECK (status IN ('PENDING', 'CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT', 'CANCELLED', 'NO_SHOW', 'HOLD')),
    arrival_date DATE NOT NULL,
    departure_date DATE NOT NULL CHECK (departure_date > arrival_date),
    adults INT NOT NULL DEFAULT 1 CHECK (adults > 0),
    children INT NOT NULL DEFAULT 0 CHECK (children >= 0),
    total_amount NUMERIC(18,4) NOT NULL CHECK (total_amount >= 0),
    discount_amount NUMERIC(18,4) DEFAULT 0 CHECK (discount_amount >= 0),
    discount_reason TEXT,
    special_requests TEXT,
    notes TEXT,
    source VARCHAR(50), -- Source of booking (website, phone, walk-in, etc)
    external_reference VARCHAR(255),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ
);

-- res.reservation_rooms - Rooms assigned to reservations
CREATE TABLE res.reservation_rooms (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    reservation_id UUID NOT NULL REFERENCES res.reservations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    room_id UUID REFERENCES core.rooms(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    room_type_id UUID NOT NULL REFERENCES core.room_types(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    rate_plan_id UUID NOT NULL REFERENCES core.rate_plans(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL DEFAULT 'RESERVED' CHECK (status IN ('RESERVED', 'ASSIGNED', 'OCCUPIED', 'COMPLETED')),
    guest_name VARCHAR(255),
    check_in_time TIMESTAMPTZ,
    check_out_time TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- res.reservation_room_rates - Daily rates for reserved rooms
CREATE TABLE res.reservation_room_rates (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    reservation_room_id UUID NOT NULL REFERENCES res.reservation_rooms(id) ON DELETE CASCADE ON UPDATE CASCADE,
    date DATE NOT NULL,
    rate NUMERIC(18,4) NOT NULL CHECK (rate >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    UNIQUE (reservation_room_id, date)
);

-- res.reservation_status_history - Track reservation status changes
CREATE TABLE res.reservation_status_history (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    reservation_id UUID NOT NULL REFERENCES res.reservations(id) ON DELETE CASCADE,
    old_status VARCHAR(20),
    new_status VARCHAR(20) NOT NULL,
    changed_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    changed_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    notes TEXT
);

-----------------------------------------------------
-- FIN SCHEMA - Financial transactions
-----------------------------------------------------

-- fin.folios - Financial records for reservations
CREATE TABLE fin.folios (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    reservation_id UUID NOT NULL REFERENCES res.reservations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    status VARCHAR(20) NOT NULL CHECK (status IN ('OPEN', 'CLOSED')),
    balance NUMERIC(18,4) NOT NULL DEFAULT 0,
    currency CHAR(3) NOT NULL CHECK (LENGTH(currency) = 3),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ,
    UNIQUE (reservation_id)
);

-- fin.folio_items - Line items in folios
CREATE TABLE fin.folio_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    folio_id UUID NOT NULL REFERENCES fin.folios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    description VARCHAR(255) NOT NULL,
    amount NUMERIC(18,4) NOT NULL CHECK (amount != 0),
    tax_amount NUMERIC(18,4) DEFAULT 0 CHECK (tax_amount >= 0),
    service_charge NUMERIC(18,4) DEFAULT 0 CHECK (service_charge >= 0),
    type VARCHAR(20) NOT NULL CHECK (type IN ('ROOM', 'F&B', 'SPA', 'OTHER', 'DEPOSIT', 'REFUND')),
    reference_id UUID, -- Generic reference to any relevant entity
    reference_type VARCHAR(50), -- Type of entity referenced
    posting_date TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- fin.taxes - Tax configuration
CREATE TABLE fin.taxes (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(100) NOT NULL,
    rate NUMERIC(5,2) NOT NULL CHECK (rate >= 0),
    type VARCHAR(20) NOT NULL CHECK (type IN ('VAT', 'SERVICE', 'OTHER')),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, name)
);

-- fin.invoices - Invoices generated from folios
CREATE TABLE fin.invoices (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    folio_id UUID NOT NULL REFERENCES fin.folios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    type VARCHAR(20) NOT NULL CHECK (type IN ('PROFORMA', 'ACTUAL')),
    invoice_number VARCHAR(50) NOT NULL,
    amount NUMERIC(18,4) NOT NULL CHECK (amount >= 0),
    tax_amount NUMERIC(18,4) DEFAULT 0 CHECK (tax_amount >= 0),
    service_charge NUMERIC(18,4) DEFAULT 0 CHECK (service_charge >= 0),
    total_amount NUMERIC(18,4) NOT NULL CHECK (total_amount >= 0),
    notes TEXT,
    generated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    due_date DATE,
    pdf_path VARCHAR(255),
    currency CHAR(3) NOT NULL,
    base_amount NUMERIC(18,4),
    fx_rate NUMERIC(10,6),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ,
    UNIQUE (folio_id, type)
);

-- fin.payments - Payments received
CREATE TABLE fin.payments (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    folio_id UUID NOT NULL REFERENCES fin.folios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    invoice_id UUID REFERENCES fin.invoices(id) ON DELETE SET NULL ON UPDATE CASCADE,
    amount NUMERIC(18,4) NOT NULL CHECK (amount > 0),
    method VARCHAR(20) NOT NULL CHECK (method IN ('CASH', 'BANK', 'MOBILE', 'CARD')),
    status VARCHAR(20) NOT NULL CHECK (status IN ('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED')),
    transaction_id VARCHAR(255),
    payment_date TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    payment_details JSONB, -- Store card last digits, bank info, etc
    currency CHAR(3) NOT NULL,
    base_amount NUMERIC(18,4),
    fx_rate NUMERIC(10,6),
    notes TEXT,
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ
);

-- fin.refunds - Track refunds issued
CREATE TABLE fin.refunds (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    payment_id UUID NOT NULL REFERENCES fin.payments(id) ON DELETE RESTRICT,
    amount NUMERIC(18,4) NOT NULL CHECK (amount > 0),
    reason TEXT NOT NULL,
    refund_date TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    status VARCHAR(20) NOT NULL CHECK (status IN ('PENDING', 'PROCESSED', 'FAILED')),
    refund_transaction_id VARCHAR(255),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- fin.fx_rates - Exchange rates
CREATE TABLE fin.fx_rates (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    from_currency CHAR(3) NOT NULL CHECK (LENGTH(from_currency) = 3),
    to_currency CHAR(3) NOT NULL CHECK (LENGTH(to_currency) = 3),
    rate NUMERIC(10,6) NOT NULL CHECK (rate > 0),
    effective_date DATE NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    UNIQUE (from_currency, to_currency, effective_date)
);

-- fin.accounts - Chart of accounts
CREATE TABLE fin.accounts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(20) NOT NULL CHECK (type IN ('ASSET', 'LIABILITY', 'EQUITY', 'REVENUE', 'EXPENSE')),
    parent_id UUID REFERENCES fin.accounts(id) ON DELETE SET NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, code)
);

-- fin.journals - Accounting journal entries
CREATE TABLE fin.journals (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    date DATE NOT NULL,
    reference_type VARCHAR(50),
    reference_id UUID,
    description TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'POSTED' CHECK (status IN ('DRAFT', 'POSTED', 'REVERSED')),
    total_debit NUMERIC(18,4) NOT NULL CHECK (total_debit >= 0),
    total_credit NUMERIC(18,4) NOT NULL CHECK (total_credit >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ,
    CHECK (total_debit = total_credit)
);

-- fin.journal_items - Journal entry line items
CREATE TABLE fin.journal_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    journal_id UUID NOT NULL REFERENCES fin.journals(id) ON DELETE CASCADE ON UPDATE CASCADE,
    account_id UUID NOT NULL REFERENCES fin.accounts(id) ON DELETE RESTRICT,
    debit NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (debit >= 0),
    credit NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (credit >= 0),
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CHECK ((debit > 0 AND credit = 0) OR (debit = 0 AND credit > 0))
);

-----------------------------------------------------
-- INV SCHEMA - Inventory management
-----------------------------------------------------

-- inv.uoms - Units of measurement
CREATE TABLE inv.uoms (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, name)
);

-- inv.item_categories - Item categories
CREATE TABLE inv.item_categories (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    parent_id UUID REFERENCES inv.item_categories(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, name)
);

-- inv.items - Inventory items
CREATE TABLE inv.items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id UUID REFERENCES inv.item_categories(id) ON DELETE SET NULL,
    uom_id UUID NOT NULL REFERENCES inv.uoms(id) ON DELETE RESTRICT,
    cost_price NUMERIC(18,4) NOT NULL CHECK (cost_price >= 0),
    selling_price NUMERIC(18,4) NOT NULL CHECK (selling_price >= 0),
    is_stockable BOOLEAN DEFAULT TRUE,
    is_sellable BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    low_stock_threshold NUMERIC(10,2) DEFAULT 10 CHECK (low_stock_threshold >= 0),
    barcode VARCHAR(50),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, code)
);

-- inv.warehouses - Inventory storage locations
CREATE TABLE inv.warehouses (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    location TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (property_id, name)
);

-- inv.stock_levels - Current stock levels by warehouse
CREATE TABLE inv.stock_levels (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    warehouse_id UUID NOT NULL REFERENCES inv.warehouses(id) ON DELETE CASCADE,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE CASCADE,
    quantity NUMERIC(18,4) NOT NULL DEFAULT 0,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (warehouse_id, item_id)
);

-- inv.vendors - Suppliers
CREATE TABLE inv.vendors (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    tax_id VARCHAR(50),
    payment_terms VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    UNIQUE (tenant_id, name)
);

-- inv.purchase_orders - Orders to vendors
CREATE TABLE inv.purchase_orders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    vendor_id UUID NOT NULL REFERENCES inv.vendors(id) ON DELETE RESTRICT,
    po_number VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('DRAFT', 'APPROVED', 'SENT', 'RECEIVED', 'CANCELLED')),
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    total_amount NUMERIC(18,4) NOT NULL CHECK (total_amount >= 0),
    delivery_address TEXT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    approved_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    deleted_at TIMESTAMPTZ
);

-- inv.purchase_order_items - Items in purchase orders
CREATE TABLE inv.purchase_order_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    po_id UUID NOT NULL REFERENCES inv.purchase_orders(id) ON DELETE CASCADE,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    quantity NUMERIC(18,4) NOT NULL CHECK (quantity > 0),
    unit_price NUMERIC(18,4) NOT NULL CHECK (unit_price >= 0),
    discount_percent NUMERIC(5,2) DEFAULT 0 CHECK (discount_percent >= 0 AND discount_percent <= 100),
    tax_percent NUMERIC(5,2) DEFAULT 0 CHECK (tax_percent >= 0),
    line_total NUMERIC(18,4) NOT NULL CHECK (line_total >= 0),
    received_quantity NUMERIC(18,4) DEFAULT 0 CHECK (received_quantity >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- inv.goods_receipts - Received goods
CREATE TABLE inv.goods_receipts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    po_id UUID NOT NULL REFERENCES inv.purchase_orders(id) ON DELETE RESTRICT,
    receipt_number VARCHAR(50) NOT NULL,
    receipt_date DATE NOT NULL,
    warehouse_id UUID NOT NULL REFERENCES inv.warehouses(id) ON DELETE RESTRICT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- inv.goods_receipt_items - Items received
CREATE TABLE inv.goods_receipt_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    goods_receipt_id UUID NOT NULL REFERENCES inv.goods_receipts(id) ON DELETE CASCADE,
    po_item_id UUID NOT NULL REFERENCES inv.purchase_order_items(id) ON DELETE RESTRICT,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    quantity NUMERIC(18,4) NOT NULL CHECK (quantity > 0),
    unit_cost NUMERIC(18,4) NOT NULL CHECK (unit_cost >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- inv.stock_movements - Track all inventory movements
CREATE TABLE inv.stock_movements (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    warehouse_id UUID NOT NULL REFERENCES inv.warehouses(id) ON DELETE RESTRICT,
    movement_type VARCHAR(20) NOT NULL CHECK (movement_type IN ('INCOMING', 'OUTGOING', 'TRANSFER', 'ADJUSTMENT')),
    quantity NUMERIC(18,4) NOT NULL CHECK (quantity != 0),
    unit_cost NUMERIC(18,4) CHECK (unit_cost >= 0),
    reference_type VARCHAR(50),
    reference_id UUID,
    notes TEXT,
    movement_date TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- inv.stocktakes - Physical inventory counts
CREATE TABLE inv.stocktakes (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    warehouse_id UUID NOT NULL REFERENCES inv.warehouses(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL CHECK (status IN ('DRAFT', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED')),
    start_date TIMESTAMPTZ NOT NULL,
    end_date TIMESTAMPTZ,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    completed_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- inv.stocktake_items - Items counted in stocktake
CREATE TABLE inv.stocktake_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    stocktake_id UUID NOT NULL REFERENCES inv.stocktakes(id) ON DELETE CASCADE,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    system_quantity NUMERIC(18,4) NOT NULL DEFAULT 0,
    counted_quantity NUMERIC(18,4),
    variance NUMERIC(18,4) GENERATED ALWAYS AS (COALESCE(counted_quantity, 0) - system_quantity) STORED,
    notes TEXT,
    counted_at TIMESTAMPTZ,
    counted_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-----------------------------------------------------
-- POS SCHEMA - Point of sale functionality
-----------------------------------------------------

-- pos.outlets - Sales outlets
CREATE TABLE pos.outlets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL CHECK (type IN ('RESTAURANT', 'BAR', 'CAFE', 'SPA', 'GIFT_SHOP')),
    location VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (property_id, name)
);

-- pos.terminals - POS terminals
CREATE TABLE pos.terminals (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    outlet_id UUID NOT NULL REFERENCES pos.outlets(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    terminal_code VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (outlet_id, terminal_code)
);

-- pos.menus - Menus for outlets
CREATE TABLE pos.menus (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    outlet_id UUID NOT NULL REFERENCES pos.outlets(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_time TIME,
    end_time TIME,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (outlet_id, name)
);

-- pos.menu_categories - Menu categories
CREATE TABLE pos.menu_categories (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    menu_id UUID NOT NULL REFERENCES pos.menus(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (menu_id, name)
);

-- pos.menu_items - Items on menus
CREATE TABLE pos.menu_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    menu_id UUID NOT NULL REFERENCES pos.menus(id) ON DELETE CASCADE,
    category_id UUID NOT NULL REFERENCES pos.menu_categories(id) ON DELETE CASCADE,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(18,4) NOT NULL CHECK (price >= 0),
    discount_price NUMERIC(18,4) CHECK (discount_price IS NULL OR (discount_price >= 0 AND discount_price < price)),
    is_available BOOLEAN DEFAULT TRUE,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- pos.orders - POS orders
CREATE TABLE pos.orders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    outlet_id UUID NOT NULL REFERENCES pos.outlets(id) ON DELETE CASCADE,
    terminal_id UUID NOT NULL REFERENCES pos.terminals(id) ON DELETE CASCADE,
    folio_id UUID REFERENCES fin.folios(id) ON DELETE SET NULL,
    room_id UUID REFERENCES core.rooms(id) ON DELETE RESTRICT,
    order_number VARCHAR(50) NOT NULL,
    server_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL CHECK (status IN ('OPEN', 'COMPLETED', 'CANCELLED')),
    order_type VARCHAR(20) NOT NULL DEFAULT 'DINE_IN' CHECK (type IN ('DINE_IN', 'TAKE_AWAY', 'ROOM_SERVICE')),
    guest_count INT NOT NULL DEFAULT 1 CHECK (guest_count > 0),
    table_number VARCHAR(20),
    subtotal NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (subtotal >= 0),
    tax_amount NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (tax_amount >= 0),
    service_charge NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (service_charge >= 0),
    discount_amount NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (discount_amount >= 0),
    total_amount NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (total_amount >= 0),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    completed_at TIMESTAMPTZ,
    cancelled_at TIMESTAMPTZ,
    cancelled_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    cancel_reason TEXT
);

-- pos.order_items - Items in orders
CREATE TABLE pos.order_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id UUID NOT NULL REFERENCES pos.orders(id) ON DELETE CASCADE,
    menu_item_id UUID NOT NULL REFERENCES pos.menu_items(id) ON DELETE RESTRICT,
    item_id UUID NOT NULL REFERENCES inv.items(id) ON DELETE RESTRICT,
    quantity NUMERIC(18,4) NOT NULL CHECK (quantity > 0),
    unit_price NUMERIC(18,4) NOT NULL CHECK (unit_price >= 0),
    discount_amount NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (discount_amount >= 0),
    tax_amount NUMERIC(18,4) NOT NULL DEFAULT 0 CHECK (tax_amount >= 0),
    total_amount NUMERIC(18,4) NOT NULL CHECK (total_amount >= 0),
    notes TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING' CHECK (status IN ('PENDING', 'PREPARING', 'SERVED', 'CANCELLED')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- pos.payments - POS payments
CREATE TABLE pos.payments (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id UUID NOT NULL REFERENCES pos.orders(id) ON DELETE CASCADE,
    payment_method VARCHAR(20) NOT NULL CHECK (payment_method IN ('CASH', 'CARD', 'MOBILE', 'ROOM_CHARGE')),
    amount NUMERIC(18,4) NOT NULL CHECK (amount > 0),
    transaction_reference VARCHAR(255),
    payment_details JSONB,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-----------------------------------------------------
-- OPS SCHEMA - Operations (housekeeping, maintenance)
-----------------------------------------------------

-- ops.departments - Hotel departments
CREATE TABLE ops.departments (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    head_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (property_id, name)
);

-- ops.employee_departments - Map employees to departments
CREATE TABLE ops.employee_departments (
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    department_id UUID NOT NULL REFERENCES ops.departments(id) ON DELETE CASCADE,
    is_primary BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (user_id, department_id)
);

-- ops.shifts - Employee work shifts
CREATE TABLE ops.shifts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (property_id, name)
);

-- ops.employee_shifts - Assign employees to shifts
CREATE TABLE ops.employee_shifts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    shift_id UUID NOT NULL REFERENCES ops.shifts(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    UNIQUE (user_id, date)
);

-- ops.time_clock - Track employee attendance
CREATE TABLE ops.time_clock (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    clock_in TIMESTAMPTZ NOT NULL,
    clock_out TIMESTAMPTZ,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ops.housekeeping_tasks - Housekeeping assignments
CREATE TABLE ops.housekeeping_tasks (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    room_id UUID NOT NULL REFERENCES core.rooms(id) ON DELETE CASCADE,
    assigned_to UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    task_type VARCHAR(20) NOT NULL CHECK (task_type IN ('DAILY_CLEAN', 'DEEP_CLEAN', 'TURNDOWN', 'INSPECTION', 'OTHER')),
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING' CHECK (status IN ('PENDING', 'IN_PROGRESS', 'COMPLETED', 'VERIFIED', 'CANCELLED')),
    priority VARCHAR(10) NOT NULL DEFAULT 'MEDIUM' CHECK (priority IN ('LOW', 'MEDIUM', 'HIGH')),
    notes TEXT,
    scheduled_date DATE NOT NULL,
    scheduled_time TIME,
    started_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    verified_at TIMESTAMPTZ,
    verified_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL
);

-- ops.room_inspections - Room inspection records
CREATE TABLE ops.room_inspections (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    room_id UUID NOT NULL REFERENCES core.rooms(id) ON DELETE CASCADE,
    inspector_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE RESTRICT,
    inspection_date DATE NOT NULL DEFAULT CURRENT_DATE,
    cleanliness_score INT CHECK (cleanliness_score BETWEEN 1 AND 5),
    maintenance_score INT CHECK (maintenance_score BETWEEN 1 AND 5),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ops.maintenance_requests - Maintenance tickets
CREATE TABLE ops.maintenance_requests (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    room_id UUID REFERENCES core.rooms(id) ON DELETE SET NULL,
    location_details TEXT,
    issue_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    reported_by UUID NOT NULL REFERENCES auth.users(id) ON DELETE RESTRICT,
    assigned_to UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'OPEN' CHECK (status IN ('OPEN', 'ASSIGNED', 'IN_PROGRESS', 'ON_HOLD', 'COMPLETED', 'CANCELLED')),
    priority VARCHAR(10) NOT NULL DEFAULT 'MEDIUM' CHECK (priority IN ('LOW', 'MEDIUM', 'HIGH', 'URGENT')),
    reported_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    assigned_at TIMESTAMPTZ,
    started_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    resolution_notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ops.lost_found - Lost and found items
CREATE TABLE ops.lost_found (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    property_id UUID NOT NULL REFERENCES core.properties(id) ON DELETE CASCADE,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    location_found VARCHAR(100),
    room_id UUID REFERENCES core.rooms(id) ON DELETE SET NULL,
    found_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    found_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'FOUND' CHECK (status IN ('FOUND', 'CLAIMED', 'DISPOSED')),
    guest_id UUID REFERENCES res.guests(id) ON DELETE SET NULL,
    claimed_date DATE,
    claimed_by VARCHAR(255),
    claimed_id_type VARCHAR(50),
    claimed_id_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ops.audit_logs - System audit trail
CREATE TABLE ops.audit_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id UUID,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ops.system_settings - System configuration
CREATE TABLE ops.system_settings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    category VARCHAR(50) NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    data_type VARCHAR(20) NOT NULL CHECK (data_type IN ('STRING', 'NUMBER', 'BOOLEAN', 'JSON')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    UNIQUE (tenant_id, category, setting_key)
);

-- ops.notifications - System notifications
CREATE TABLE ops.notifications (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES core.tenants(id) ON DELETE CASCADE,
    user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-----------------------------------------------------
-- REPORT SCHEMA - Views for reporting
-----------------------------------------------------

-- Create materialized views for reporting

-- report.occupancy_daily - Daily occupancy rates
CREATE MATERIALIZED VIEW report.occupancy_daily AS
SELECT 
    r.property_id,
    rr.room_type_id,
    r.arrival_date::date AS date,
    COUNT(DISTINCT rr.room_id) AS rooms_occupied,
    (SELECT COUNT(*) FROM core.rooms rm WHERE rm.property_id = r.property_id AND rm.room_type_id = rr.room_type_id) AS total_rooms,
    CASE 
        WHEN (SELECT COUNT(*) FROM core.rooms rm WHERE rm.property_id = r.property_id AND rm.room_type_id = rr.room_type_id) > 0 
        THEN ROUND((COUNT(DISTINCT rr.room_id)::numeric / 
             (SELECT COUNT(*) FROM core.rooms rm WHERE rm.property_id = r.property_id AND rm.room_type_id = rr.room_type_id)::numeric) * 100, 2)
        ELSE 0
    END AS occupancy_rate
FROM res.reservations r
JOIN res.reservation_rooms rr ON r.id = rr.reservation_id
WHERE r.status IN ('CONFIRMED', 'CHECKED_IN')
AND r.deleted_at IS NULL
GROUP BY r.property_id, rr.room_type_id, r.arrival_date::date;

-- report.revenue_daily - Daily revenue
CREATE MATERIALIZED VIEW report.revenue_daily AS
SELECT
    p.id AS property_id,
    p.name AS property_name,
    fi.posting_date::date AS date,
    SUM(CASE WHEN fi.type = 'ROOM' THEN fi.amount ELSE 0 END) AS room_revenue,
    SUM(CASE WHEN fi.type = 'F&B' THEN fi.amount ELSE 0 END) AS fnb_revenue,
    SUM(CASE WHEN fi.type NOT IN ('ROOM', 'F&B', 'DEPOSIT', 'REFUND') THEN fi.amount ELSE 0 END) AS other_revenue,
    SUM(CASE WHEN fi.type NOT IN ('DEPOSIT', 'REFUND') THEN fi.amount ELSE 0 END) AS total_revenue,
    SUM(fi.tax_amount) AS total_tax
FROM fin.folio_items fi
JOIN fin.folios f ON fi.folio_id = f.id
JOIN res.reservations r ON f.reservation_id = r.id
JOIN core.properties p ON r.property_id = p.id
WHERE fi.amount > 0
GROUP BY p.id, p.name, fi.posting_date::date;

-- report.adr_daily - Average Daily Rate
CREATE MATERIALIZED VIEW report.adr_daily AS
SELECT
    p.id AS property_id,
    p.name AS property_name,
    rrr.date,
    rt.id AS room_type_id,
    rt.name AS room_type,
    AVG(rrr.rate) AS average_daily_rate,
    COUNT(DISTINCT rr.id) AS room_nights
FROM res.reservation_room_rates rrr
JOIN res.reservation_rooms rr ON rrr.reservation_room_id = rr.id
JOIN res.reservations r ON rr.reservation_id = r.id
JOIN core.properties p ON r.property_id = p.id
JOIN core.room_types rt ON rr.room_type_id = rt.id
WHERE r.status IN ('CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT')
AND r.deleted_at IS NULL
GROUP BY p.id, p.name, rrr.date, rt.id, rt.name;

-- report.inventory_status - Current inventory status
CREATE MATERIALIZED VIEW report.inventory_status AS
SELECT
    p.id AS property_id,
    p.name AS property_name,
    w.id AS warehouse_id,
    w.name AS warehouse_name,
    i.id AS item_id,
    i.code AS item_code,
    i.name AS item_name,
    ic.name AS category_name,
    u.symbol AS uom,
    sl.quantity AS current_stock,
    i.low_stock_threshold,
    CASE WHEN sl.quantity <= i.low_stock_threshold THEN TRUE ELSE FALSE END AS is_low_stock,
    i.cost_price * sl.quantity AS inventory_value
FROM inv.stock_levels sl
JOIN inv.items i ON sl.item_id = i.id
JOIN inv.uoms u ON i.uom_id = u.id
JOIN inv.warehouses w ON sl.warehouse_id = w.id
JOIN core.properties p ON w.property_id = p.id
LEFT JOIN inv.item_categories ic ON i.category_id = ic.id
WHERE i.deleted_at IS NULL;

-- report.maintenance_summary - Maintenance request summary
CREATE MATERIALIZED VIEW report.maintenance_summary AS
SELECT
    p.id AS property_id,
    p.name AS property_name,
    DATE_TRUNC('month', mr.reported_at) AS month,
    mr.issue_type,
    mr.status,
    COUNT(*) AS request_count,
    AVG(EXTRACT(EPOCH FROM (mr.completed_at - mr.reported_at))/3600)::numeric(10,2) AS avg_resolution_hours
FROM ops.maintenance_requests mr
JOIN core.properties p ON mr.property_id = p.id
GROUP BY p.id, p.name, DATE_TRUNC('month', mr.reported_at), mr.issue_type, mr.status;

-----------------------------------------------------
-- Functions, Triggers, and Indexes
-----------------------------------------------------

-- Function to update timestamps
CREATE OR REPLACE FUNCTION core.update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger for updated_at fields (apply to all relevant tables)
CREATE TRIGGER update_tenant_timestamp
BEFORE UPDATE ON core.tenants
FOR EACH ROW EXECUTE FUNCTION core.update_timestamp();

-- Room status update on reservation status change
CREATE OR REPLACE FUNCTION res.update_room_status()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.status = 'CHECKED_IN' THEN
        UPDATE core.rooms r
        SET status = 'OCCUPIED'
        FROM res.reservation_rooms rr
        WHERE rr.reservation_id = NEW.id AND rr.room_id = r.id;
    ELSIF NEW.status = 'CHECKED_OUT' THEN
        UPDATE core.rooms r
        SET status = 'DIRTY'
        FROM res.reservation_rooms rr
        WHERE rr.reservation_id = NEW.id AND rr.room_id = r.id;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER reservation_status_change
AFTER UPDATE OF status ON res.reservations
FOR EACH ROW
WHEN (OLD.status IS DISTINCT FROM NEW.status)
EXECUTE FUNCTION res.update_room_status();

-- Create audit log entries
CREATE OR REPLACE FUNCTION ops.audit_trigger_function()
RETURNS TRIGGER AS $$
DECLARE
    tenant_id UUID;
    user_id UUID;
BEGIN
    -- Get current tenant_id and user_id from session variables if available
    tenant_id := NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID;
    user_id := NULLIF(current_setting('app.current_user_id', TRUE), '')::UUID;
    
    IF tenant_id IS NULL THEN
        -- Try to determine tenant_id from the record if available
        IF TG_TABLE_NAME = 'tenants' THEN
            tenant_id := NEW.id;
        ELSIF TG_OP = 'DELETE' THEN
            IF OLD.tenant_id IS NOT NULL THEN
                tenant_id := OLD.tenant_id;
            END IF;
        ELSE
            IF NEW.tenant_id IS NOT NULL THEN
                tenant_id := NEW.tenant_id;
            END IF;
        END IF;
    END IF;
    
    IF tenant_id IS NOT NULL THEN
        INSERT INTO ops.audit_logs(
            tenant_id, user_id, action, entity_type, entity_id,
            old_values, new_values, ip_address
        ) VALUES (
            tenant_id,
            user_id,
            TG_OP,
            TG_TABLE_NAME,
            CASE 
                WHEN TG_OP = 'DELETE' THEN OLD.id
                ELSE NEW.id
            END,
            CASE WHEN TG_OP = 'DELETE' OR TG_OP = 'UPDATE' THEN to_jsonb(OLD) ELSE NULL END,
            CASE WHEN TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN to_jsonb(NEW) ELSE NULL END,
            NULLIF(current_setting('app.client_ip', TRUE), '')
        );
    END IF;
    
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Create row-level security policies for multi-tenant isolation
ALTER TABLE core.tenants ENABLE ROW LEVEL SECURITY;
CREATE POLICY tenant_isolation ON core.tenants 
    USING (id = current_setting('app.current_tenant_id')::UUID OR 
           current_setting('app.is_superadmin', TRUE)::boolean = TRUE);

-- Create indexes for frequently queried columns
CREATE INDEX idx_reservations_arrival_date ON res.reservations(arrival_date);
CREATE INDEX idx_reservations_departure_date ON res.reservations(departure_date);
CREATE INDEX idx_reservations_status ON res.reservations(status);
CREATE INDEX idx_rooms_status ON core.rooms(status);
CREATE INDEX idx_folio_items_posting_date ON fin.folio_items(posting_date);
CREATE INDEX idx_stock_movements_movement_date ON inv.stock_movements(movement_date);
CREATE INDEX idx_audit_logs_timestamp ON ops.audit_logs(timestamp);
CREATE INDEX idx_payments_payment_date ON fin.payments(payment_date);
CREATE INDEX idx_users_tenant_id ON auth.users(tenant_id);

-- Create functions for common operations
CREATE OR REPLACE FUNCTION res.create_reservation(
    p_property_id UUID,
    p_guest_id UUID,
    p_arrival_date DATE,
    p_departure_date DATE,
    p_adults INT,
    p_children INT,
    p_room_type_id UUID,
    p_rate_plan_id UUID,
    p_notes TEXT,
    p_created_by UUID
)
RETURNS UUID AS $$
DECLARE
    v_reservation_id UUID;
    v_reservation_room_id UUID;
    v_folio_id UUID;
    v_total_amount NUMERIC(18,4) := 0;
    v_currency CHAR(3);
    v_current_date DATE := p_arrival_date;
BEGIN
    -- Get property currency
    SELECT t.base_currency INTO v_currency
    FROM core.properties p
    JOIN core.tenants t ON p.tenant_id = t.id
    WHERE p.id = p_property_id;
    
    -- Calculate total amount
    WHILE v_current_date < p_departure_date LOOP
        SELECT v_total_amount + rate INTO v_total_amount
        FROM core.rate_plans
        WHERE id = p_rate_plan_id;
        
        v_current_date := v_current_date + 1;
    END LOOP;
    
    -- Create reservation
    INSERT INTO res.reservations(
        property_id, guest_id, status, arrival_date, departure_date,
        adults, children, total_amount, notes, created_by
    ) VALUES (
        p_property_id, p_guest_id, 'PENDING', p_arrival_date, p_departure_date,
        p_adults, p_children, v_total_amount, p_notes, p_created_by
    ) RETURNING id INTO v_reservation_id;
    
    -- Create reservation room
    INSERT INTO res.reservation_rooms(
        reservation_id, room_type_id, rate_
        -- Continue from the create_reservation function
        rate_plan_id, status
    ) VALUES (
        v_reservation_id, p_room_type_id, p_rate_plan_id, 'RESERVED'
    ) RETURNING id INTO v_reservation_room_id;
    
    -- Create daily rates
    v_current_date := p_arrival_date;
    WHILE v_current_date < p_departure_date LOOP
        INSERT INTO res.reservation_room_rates(
            reservation_room_id, date, rate, updated_by
        )
        SELECT 
            v_reservation_room_id, 
            v_current_date,
            COALESCE(sr.adjustment_factor * rp.rate / 100, rp.rate),
            p_created_by
        FROM core.rate_plans rp
        LEFT JOIN core.seasonal_rates sr ON sr.rate_plan_id = rp.id 
            AND v_current_date BETWEEN sr.start_date AND sr.end_date
        WHERE rp.id = p_rate_plan_id;
        
        v_current_date := v_current_date + 1;
    END LOOP;
    
    -- Create folio
    INSERT INTO fin.folios(
        reservation_id, status, currency, created_by
    ) VALUES (
        v_reservation_id, 'OPEN', v_currency, p_created_by
    ) RETURNING id INTO v_folio_id;
    
    RETURN v_reservation_id;
END;
$$ LANGUAGE plpgsql;

-- Function to check room availability
CREATE OR REPLACE FUNCTION res.check_room_availability(
    p_property_id UUID,
    p_room_type_id UUID,
    p_arrival_date DATE,
    p_departure_date DATE
)
RETURNS TABLE(available_rooms INT, total_rooms INT) AS $$
DECLARE
    v_total_rooms INT;
    v_booked_rooms INT;
BEGIN
    -- Get total rooms of this type
    SELECT COUNT(*) INTO v_total_rooms
    FROM core.rooms r
    WHERE r.property_id = p_property_id 
    AND r.room_type_id = p_room_type_id
    AND r.deleted_at IS NULL
    AND r.status NOT IN ('OUT_OF_ORDER');
    
    -- Get maximum booked rooms for any single date in the period
    SELECT COALESCE(MAX(daily_bookings), 0) INTO v_booked_rooms
    FROM (
        SELECT date_check, COUNT(*) as daily_bookings
        FROM (
            SELECT generate_series(p_arrival_date, p_departure_date - 1, '1 day'::interval)::date as date_check
        ) dates
        LEFT JOIN res.reservation_room_rates rrr ON rrr.date = dates.date_check
        LEFT JOIN res.reservation_rooms rr ON rrr.reservation_room_id = rr.id
        LEFT JOIN res.reservations res ON rr.reservation_id = res.id
        WHERE res.property_id = p_property_id
        AND rr.room_type_id = p_room_type_id
        AND res.status IN ('CONFIRMED', 'CHECKED_IN')
        AND res.deleted_at IS NULL
        GROUP BY date_check
    ) daily_counts;
    
    RETURN QUERY SELECT (v_total_rooms - v_booked_rooms), v_total_rooms;
END;
$$ LANGUAGE plpgsql;

-- Function to auto-assign rooms
CREATE OR REPLACE FUNCTION res.auto_assign_room(
    p_reservation_room_id UUID,
    p_assigned_by UUID
)
RETURNS UUID AS $$
DECLARE
    v_room_id UUID;
    v_property_id UUID;
    v_room_type_id UUID;
    v_arrival_date DATE;
    v_departure_date DATE;
BEGIN
    -- Get reservation details
    SELECT r.property_id, rr.room_type_id, r.arrival_date, r.departure_date
    INTO v_property_id, v_room_type_id, v_arrival_date, v_departure_date
    FROM res.reservation_rooms rr
    JOIN res.reservations r ON rr.reservation_id = r.id
    WHERE rr.id = p_reservation_room_id;
    
    -- Find available room
    SELECT room_id INTO v_room_id
    FROM (
        SELECT rm.id as room_id
        FROM core.rooms rm
        WHERE rm.property_id = v_property_id
        AND rm.room_type_id = v_room_type_id
        AND rm.status IN ('CLEAN', 'VACANT')
        AND rm.deleted_at IS NULL
        AND rm.id NOT IN (
            SELECT DISTINCT rr2.room_id
            FROM res.reservation_rooms rr2
            JOIN res.reservations r2 ON rr2.reservation_id = r2.id
            JOIN res.reservation_room_rates rrr2 ON rr2.id = rrr2.reservation_room_id
            WHERE r2.property_id = v_property_id
            AND rr2.room_id IS NOT NULL
            AND r2.status IN ('CONFIRMED', 'CHECKED_IN')
            AND r2.deleted_at IS NULL
            AND rrr2.date >= v_arrival_date
            AND rrr2.date < v_departure_date
        )
        ORDER BY rm.room_number
        LIMIT 1
    ) available_rooms;
    
    -- Update reservation room with assigned room
    IF v_room_id IS NOT NULL THEN
        UPDATE res.reservation_rooms
        SET room_id = v_room_id, status = 'ASSIGNED'
        WHERE id = p_reservation_room_id;
    END IF;
    
    RETURN v_room_id;
END;
$$ LANGUAGE plpgsql;

-- Function to update stock levels
CREATE OR REPLACE FUNCTION inv.update_stock_level(
    p_item_id UUID,
    p_warehouse_id UUID,
    p_quantity NUMERIC(18,4),
    p_movement_type VARCHAR(20),
    p_reference_type VARCHAR(50) DEFAULT NULL,
    p_reference_id UUID DEFAULT NULL,
    p_user_id UUID DEFAULT NULL
)
RETURNS VOID AS $$
DECLARE
    v_property_id UUID;
    v_current_stock NUMERIC(18,4) := 0;
    v_new_quantity NUMERIC(18,4);
BEGIN
    -- Get property ID for the warehouse
    SELECT property_id INTO v_property_id
    FROM inv.warehouses
    WHERE id = p_warehouse_id;
    
    -- Get current stock level
    SELECT quantity INTO v_current_stock
    FROM inv.stock_levels
    WHERE item_id = p_item_id AND warehouse_id = p_warehouse_id;
    
    -- Calculate new quantity based on movement type
    CASE p_movement_type
        WHEN 'INCOMING' THEN
            v_new_quantity := COALESCE(v_current_stock, 0) + p_quantity;
        WHEN 'OUTGOING' THEN
            v_new_quantity := COALESCE(v_current_stock, 0) - p_quantity;
            IF v_new_quantity < 0 THEN
                RAISE EXCEPTION 'Insufficient stock. Current: %, Requested: %', v_current_stock, p_quantity;
            END IF;
        WHEN 'ADJUSTMENT' THEN
            v_new_quantity := p_quantity;
        ELSE
            RAISE EXCEPTION 'Invalid movement type: %', p_movement_type;
    END CASE;
    
    -- Insert or update stock level
    INSERT INTO inv.stock_levels (property_id, warehouse_id, item_id, quantity)
    VALUES (v_property_id, p_warehouse_id, p_item_id, v_new_quantity)
    ON CONFLICT (warehouse_id, item_id)
    DO UPDATE SET quantity = v_new_quantity, updated_at = NOW();
    
    -- Record stock movement
    INSERT INTO inv.stock_movements (
        property_id, item_id, warehouse_id, movement_type, quantity,
        reference_type, reference_id, created_by
    ) VALUES (
        v_property_id, p_item_id, p_warehouse_id, p_movement_type, p_quantity,
        p_reference_type, p_reference_id, p_user_id
    );
END;
$$ LANGUAGE plpgsql;

-- Function to calculate folio balance
CREATE OR REPLACE FUNCTION fin.calculate_folio_balance(p_folio_id UUID)
RETURNS NUMERIC(18,4) AS $$
DECLARE
    v_charges NUMERIC(18,4) := 0;
    v_payments NUMERIC(18,4) := 0;
    v_balance NUMERIC(18,4);
BEGIN
    -- Calculate total charges
    SELECT COALESCE(SUM(amount + tax_amount + service_charge), 0)
    INTO v_charges
    FROM fin.folio_items
    WHERE folio_id = p_folio_id
    AND amount > 0;
    
    -- Calculate total payments
    SELECT COALESCE(SUM(amount), 0)
    INTO v_payments
    FROM fin.payments
    WHERE folio_id = p_folio_id
    AND status = 'COMPLETED';
    
    v_balance := v_charges - v_payments;
    
    -- Update folio balance
    UPDATE fin.folios
    SET balance = v_balance, updated_at = NOW()
    WHERE id = p_folio_id;
    
    RETURN v_balance;
END;
$$ LANGUAGE plpgsql;

-- Function to post revenue to accounting
CREATE OR REPLACE FUNCTION fin.post_revenue_entry(
    p_folio_item_id UUID,
    p_user_id UUID
)
RETURNS UUID AS $$
DECLARE
    v_journal_id UUID;
    v_tenant_id UUID;
    v_item_amount NUMERIC(18,4);
    v_tax_amount NUMERIC(18,4);
    v_service_charge NUMERIC(18,4);
    v_item_type VARCHAR(20);
    v_ar_account_id UUID;
    v_revenue_account_id UUID;
    v_tax_account_id UUID;
    v_service_account_id UUID;
BEGIN
    -- Get folio item details and tenant
    SELECT fi.amount, fi.tax_amount, fi.service_charge, fi.type, 
           p.tenant_id
    INTO v_item_amount, v_tax_amount, v_service_charge, v_item_type, v_tenant_id
    FROM fin.folio_items fi
    JOIN fin.folios f ON fi.folio_id = f.id
    JOIN res.reservations r ON f.reservation_id = r.id
    JOIN core.properties p ON r.property_id = p.id
    WHERE fi.id = p_folio_item_id;
    
    -- Get account IDs
    SELECT id INTO v_ar_account_id
    FROM fin.accounts
    WHERE tenant_id = v_tenant_id AND code = 'AR';
    
    SELECT id INTO v_revenue_account_id
    FROM fin.accounts
    WHERE tenant_id = v_tenant_id AND code = CASE v_item_type
        WHEN 'ROOM' THEN 'ROOM_REV'
        WHEN 'F&B' THEN 'FNB_REV'
        ELSE 'OTHER_REV'
    END;
    
    SELECT id INTO v_tax_account_id
    FROM fin.accounts
    WHERE tenant_id = v_tenant_id AND code = 'TAX_LIAB';
    
    SELECT id INTO v_service_account_id
    FROM fin.accounts
    WHERE tenant_id = v_tenant_id AND code = 'SERVICE_REV';
    
    -- Create journal entry
    INSERT INTO fin.journals (
        tenant_id, date, reference_type, reference_id, description,
        total_debit, total_credit, created_by
    ) VALUES (
        v_tenant_id, CURRENT_DATE, 'FOLIO_ITEM', p_folio_item_id,
        'Revenue posting for ' || v_item_type,
        v_item_amount + v_tax_amount + v_service_charge,
        v_item_amount + v_tax_amount + v_service_charge,
        p_user_id
    ) RETURNING id INTO v_journal_id;
    
    -- Debit A/R
    INSERT INTO fin.journal_items (journal_id, account_id, debit, description)
    VALUES (v_journal_id, v_ar_account_id, v_item_amount + v_tax_amount + v_service_charge, 'Accounts Receivable');
    
    -- Credit Revenue
    INSERT INTO fin.journal_items (journal_id, account_id, credit, description)
    VALUES (v_journal_id, v_revenue_account_id, v_item_amount, v_item_type || ' Revenue');
    
    -- Credit Tax if applicable
    IF v_tax_amount > 0 THEN
        INSERT INTO fin.journal_items (journal_id, account_id, credit, description)
        VALUES (v_journal_id, v_tax_account_id, v_tax_amount, 'Tax Liability');
    END IF;
    
    -- Credit Service Charge if applicable
    IF v_service_charge > 0 THEN
        INSERT INTO fin.journal_items (journal_id, account_id, credit, description)
        VALUES (v_journal_id, v_service_account_id, v_service_charge, 'Service Charge');
    END IF;
    
    RETURN v_journal_id;
END;
$$ LANGUAGE plpgsql;

-- Function to check user role limits
CREATE OR REPLACE FUNCTION auth.check_user_role_limit(
    p_tenant_id UUID,
    p_role_name VARCHAR(50)
)
RETURNS BOOLEAN AS $$
DECLARE
    v_current_count INT;
    v_max_count INT;
BEGIN
    -- Get current count of users with this role
    SELECT COUNT(*) INTO v_current_count
    FROM auth.users u
    JOIN auth.roles r ON u.role_id = r.id
    WHERE u.tenant_id = p_tenant_id
    AND r.name = p_role_name
    AND u.deleted_at IS NULL;
    
    -- Get maximum allowed count
    SELECT max_count INTO v_max_count
    FROM core.tenant_staff_limits
    WHERE tenant_id = p_tenant_id
    AND role = p_role_name;
    
    RETURN COALESCE(v_current_count, 0) < COALESCE(v_max_count, 999);
END;
$$ LANGUAGE plpgsql;

-----------------------------------------------------
-- Additional Triggers
-----------------------------------------------------

-- Trigger to validate user role limits before insert
CREATE OR REPLACE FUNCTION auth.validate_user_role_limit()
RETURNS TRIGGER AS $$
DECLARE
    v_role_name VARCHAR(50);
BEGIN
    SELECT name INTO v_role_name
    FROM auth.roles
    WHERE id = NEW.role_id;
    
    IF NOT auth.check_user_role_limit(NEW.tenant_id, v_role_name) THEN
        RAISE EXCEPTION 'Maximum number of % users exceeded for this tenant', v_role_name;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER validate_user_role_limit
BEFORE INSERT ON auth.users
FOR EACH ROW EXECUTE FUNCTION auth.validate_user_role_limit();

-- Trigger to update folio balance when items are added
CREATE OR REPLACE FUNCTION fin.update_folio_balance_trigger()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM fin.calculate_folio_balance(NEW.folio_id);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_folio_balance_on_item
AFTER INSERT OR UPDATE ON fin.folio_items
FOR EACH ROW EXECUTE FUNCTION fin.update_folio_balance_trigger();

-- Trigger to update folio balance when payments are made
CREATE OR REPLACE FUNCTION fin.update_folio_balance_on_payment()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM fin.calculate_folio_balance(NEW.folio_id);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_folio_balance_payment
AFTER INSERT OR UPDATE ON fin.payments
FOR EACH ROW EXECUTE FUNCTION fin.update_folio_balance_on_payment();

-- Trigger to create housekeeping task when room is checked out
CREATE OR REPLACE FUNCTION ops.create_checkout_cleaning_task()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.status = 'CHECKED_OUT' AND OLD.status = 'CHECKED_IN' THEN
        INSERT INTO ops.housekeeping_tasks (
            property_id, room_id, task_type, status, priority, 
            scheduled_date, notes, created_by
        )
        SELECT 
            r.property_id,
            rr.room_id,
            'DAILY_CLEAN',
            'PENDING',
            'HIGH',
            CURRENT_DATE,
            'Post-checkout cleaning',
            NEW.updated_by
        FROM res.reservation_rooms rr
        JOIN core.rooms r ON rr.room_id = r.id
        WHERE rr.reservation_id = NEW.id;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER create_checkout_cleaning
AFTER UPDATE OF status ON res.reservations
FOR EACH ROW EXECUTE FUNCTION ops.create_checkout_cleaning_task();

-- Trigger to track reservation status changes
CREATE OR REPLACE FUNCTION res.track_status_change()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.status IS DISTINCT FROM NEW.status THEN
        INSERT INTO res.reservation_status_history (
            reservation_id, old_status, new_status, changed_by
        ) VALUES (
            NEW.id, OLD.status, NEW.status, NEW.updated_by
        );
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER track_reservation_status
AFTER UPDATE ON res.reservations
FOR EACH ROW EXECUTE FUNCTION res.track_status_change();

-- Trigger to prevent double-booking
CREATE OR REPLACE FUNCTION res.prevent_double_booking()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.room_id IS NOT NULL THEN
        IF EXISTS (
            SELECT 1
            FROM res.reservation_rooms rr2
            JOIN res.reservations r2 ON rr2.reservation_id = r2.id
            JOIN res.reservation_room_rates rrr2 ON rr2.id = rrr2.reservation_room_id
            WHERE rr2.room_id = NEW.room_id
            AND r2.status IN ('CONFIRMED', 'CHECKED_IN')
            AND r2.deleted_at IS NULL
            AND rr2.id != NEW.id
            AND EXISTS (
                SELECT 1
                FROM res.reservation_room_rates rrr1
                WHERE rrr1.reservation_room_id = NEW.id
                AND rrr1.date = rrr2.date
            )
        ) THEN
            RAISE EXCEPTION 'Room % is already booked for overlapping dates', NEW.room_id;
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_double_booking
BEFORE INSERT OR UPDATE ON res.reservation_rooms
FOR EACH ROW EXECUTE FUNCTION res.prevent_double_booking();

-- Trigger to auto-post revenue entries
CREATE OR REPLACE FUNCTION fin.auto_post_revenue()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.amount > 0 AND NEW.type IN ('ROOM', 'F&B', 'SPA', 'OTHER') THEN
        PERFORM fin.post_revenue_entry(NEW.id, NEW.created_by);
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER auto_post_revenue
AFTER INSERT ON fin.folio_items
FOR EACH ROW EXECUTE FUNCTION fin.auto_post_revenue();

-----------------------------------------------------
-- Additional Indexes for Performance
-----------------------------------------------------

-- Multi-column indexes for complex queries
CREATE INDEX idx_reservations_property_arrival ON res.reservations(property_id, arrival_date);
CREATE INDEX idx_reservations_property_departure ON res.reservations(property_id, departure_date);
CREATE INDEX idx_reservations_status_dates ON res.reservations(status, arrival_date, departure_date);
CREATE INDEX idx_reservation_rooms_type ON res.reservation_rooms(room_type_id, status);
CREATE INDEX idx_reservation_room_rates_date ON res.reservation_room_rates(date, rate);

-- Indexes for financial queries
CREATE INDEX idx_folio_items_type_date ON fin.folio_items(type, posting_date);
CREATE INDEX idx_payments_method_status ON fin.payments(method, status);
CREATE INDEX idx_invoices_type_generated ON fin.invoices(type, generated_at);
CREATE INDEX idx_folios_status_balance ON fin.folios(status, balance);

-- Indexes for inventory
CREATE INDEX idx_stock_movements_item_date ON inv.stock_movements(item_id, movement_date);
CREATE INDEX idx_stock_movements_warehouse_type ON inv.stock_movements(warehouse_id, movement_type);
CREATE INDEX idx_stock_levels_item_quantity ON inv.stock_levels(item_id, quantity);
CREATE INDEX idx_purchase_orders_vendor_status ON inv.purchase_orders(vendor_id, status);

-- Indexes for operations
CREATE INDEX idx_housekeeping_tasks_room_status ON ops.housekeeping_tasks(room_id, status);
CREATE INDEX idx_housekeeping_tasks_assigned_date ON ops.housekeeping_tasks(assigned_to, scheduled_date);
CREATE INDEX idx_maintenance_requests_property_status ON ops.maintenance_requests(property_id, status);
CREATE INDEX idx_maintenance_requests_priority_reported ON ops.maintenance_requests(priority, reported_at);

-- Indexes for POS
CREATE INDEX idx_pos_orders_outlet_date ON pos.orders(outlet_id, created_at);
CREATE INDEX idx_pos_orders_status_server ON pos.orders(status, server_id);
CREATE INDEX idx_pos_order_items_status ON pos.order_items(status);

-- Indexes for authentication and authorization
CREATE INDEX idx_users_tenant_role ON auth.users(tenant_id, role_id);
CREATE INDEX idx_users_property_active ON auth.users(property_id, is_active);
CREATE INDEX idx_audit_logs_tenant_timestamp ON ops.audit_logs(tenant_id, timestamp);
CREATE INDEX idx_audit_logs_entity ON ops.audit_logs(entity_type, entity_id);

-- Partial indexes for better performance
CREATE INDEX idx_reservations_active ON res.reservations(property_id, status) 
WHERE deleted_at IS NULL;

CREATE INDEX idx_rooms_available ON core.rooms(property_id, room_type_id, status) 
WHERE status IN ('CLEAN', 'VACANT') AND deleted_at IS NULL;

CREATE INDEX idx_items_active ON inv.items(tenant_id, category_id) 
WHERE deleted_at IS NULL AND is_active = TRUE;

CREATE INDEX idx_folio_items_charges ON fin.folio_items(folio_id, type, posting_date) 
WHERE amount > 0;

CREATE INDEX idx_payments_completed ON fin.payments(folio_id, payment_date) 
WHERE status = 'COMPLETED';

-- Unique indexes to prevent duplicates
CREATE UNIQUE INDEX idx_unique_guest_email_per_tenant ON res.guests(tenant_id, email) 
WHERE email IS NOT NULL AND deleted_at IS NULL;

CREATE UNIQUE INDEX idx_unique_room_number_per_property ON core.rooms(property_id, room_number) 
WHERE deleted_at IS NULL;

CREATE UNIQUE INDEX idx_unique_user_username_per_tenant ON auth.users(tenant_id, username) 
WHERE deleted_at IS NULL;

-----------------------------------------------------
-- Stored Procedures for Common Operations
-----------------------------------------------------

-- Procedure to check-in a guest
CREATE OR REPLACE FUNCTION res.check_in_guest(
    p_reservation_id UUID,
    p_room_id UUID,
    p_checked_in_by UUID
)
RETURNS BOOLEAN AS $$
DECLARE
    v_reservation_room_id UUID;
BEGIN
    -- Update reservation status
    UPDATE res.reservations
    SET status = 'CHECKED_IN', updated_by = p_checked_in_by, updated_at = NOW()
    WHERE id = p_reservation_id;
    
    -- Update reservation room with actual room and check-in time
    UPDATE res.reservation_rooms
    SET room_id = p_room_id, status = 'OCCUPIED', check_in_time = NOW()
    WHERE reservation_id = p_reservation_id
    RETURNING id INTO v_reservation_room_id;
    
    -- Update room status
    UPDATE core.rooms
    SET status = 'OCCUPIED', updated_at = NOW()
    WHERE id = p_room_id;
    
    RETURN v_reservation_room_id IS NOT NULL;
END;
$$ LANGUAGE plpgsql;

-- Procedure to check-out a guest
CREATE OR REPLACE FUNCTION res.check_out_guest(
    p_reservation_id UUID,
    p_checked_out_by UUID
)
RETURNS BOOLEAN AS $$
DECLARE
    v_room_ids UUID[];
    v_room_id UUID;
BEGIN
    -- Get all room IDs for this reservation
    SELECT ARRAY_AGG(room_id) INTO v_room_ids
    FROM res.reservation_rooms
    WHERE reservation_id = p_reservation_id AND room_id IS NOT NULL;
    
    -- Update reservation status
    UPDATE res.reservations
    SET status = 'CHECKED_OUT', updated_by = p_checked_out_by, updated_at = NOW()
    WHERE id = p_reservation_id;
    
    -- Update reservation rooms
    UPDATE res.reservation_rooms
    SET status = 'COMPLETED', check_out_time = NOW()
    WHERE reservation_id = p_reservation_id;
    
    -- Update room status to dirty
    FOREACH v_room_id IN ARRAY v_room_ids
    LOOP
        UPDATE core.rooms
        SET status = 'DIRTY', updated_at = NOW()
        WHERE id = v_room_id;
    END LOOP;
    
    -- Close folio
    UPDATE fin.folios
    SET status = 'CLOSED', updated_at = NOW(), updated_by = p_checked_out_by
    WHERE reservation_id = p_reservation_id;
    
    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

-- Function to get property dashboard data
CREATE OR REPLACE FUNCTION report.get_property_dashboard(
    p_property_id UUID,
    p_date DATE DEFAULT CURRENT_DATE
)
RETURNS TABLE(
    total_rooms INT,
    occupied_rooms INT,
    available_rooms INT,
    dirty_rooms INT,
    ooo_rooms INT,
    occupancy_rate NUMERIC(5,2),
    arrivals_today INT,
    departures_today INT,
    revenue_today NUMERIC(18,4)
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        -- Room counts
        (SELECT COUNT(*)::INT FROM core.rooms WHERE property_id = p_property_id AND deleted_at IS NULL),
        (SELECT COUNT(*)::INT FROM core.rooms WHERE property_id = p_property_id AND status = 'OCCUPIED' AND deleted_at IS NULL),
        (SELECT COUNT(*)::INT FROM core.rooms WHERE property_id = p_property_id AND status IN ('CLEAN', 'VACANT') AND deleted_at IS NULL),
        (SELECT COUNT(*)::INT FROM core.rooms WHERE property_id = p_property_id AND status = 'DIRTY' AND deleted_at IS NULL),
        (SELECT COUNT(*)::INT FROM core.rooms WHERE property_id = p_property_id AND status = 'OUT_OF_ORDER' AND deleted_at IS NULL),
        
        -- Occupancy rate
        CASE 
            WHEN (SELECT COUNT(*) FROM core.rooms WHERE property_id = p_property_id AND deleted_at IS NULL) > 0
            THEN (
                SELECT COUNT(*)::NUMERIC / 
                (SELECT COUNT(*) FROM core.rooms WHERE property_id = p_property_id AND deleted_at IS NULL)::NUMERIC * 100
                FROM core.rooms WHERE property_id = p_property_id AND status = 'OCCUPIED' AND deleted_at IS NULL
            )::NUMERIC(5,2)
            ELSE 0::NUMERIC(5,2)
        END,
        
        -- Arrivals today
        (SELECT COUNT(*)::INT FROM res.reservations WHERE property_id = p_property_id AND arrival_date = p_date AND status NOT IN ('CANCELLED') AND deleted_at IS NULL),
        
        -- Departures today
        (SELECT COUNT(*)::INT FROM res.reservations WHERE property_id = p_property_id AND departure_date = p_date AND status NOT IN ('CANCELLED') AND deleted_at IS NULL),
        
        -- Revenue today
        COALESCE((
            SELECT SUM(fi.amount + fi.tax_amount + fi.service_charge)
            FROM fin.folio_items fi
            JOIN fin.folios f ON fi.folio_id = f.id
            JOIN res.reservations r ON f.reservation_id = r.id
            WHERE r.property_id = p_property_id 
            AND fi.posting_date::date = p_date
            AND fi.amount > 0
        ), 0)::NUMERIC(18,4);
END;
$$ LANGUAGE plpgsql;

-- Create default data insertion function
CREATE OR REPLACE FUNCTION core.create_default_data(p_tenant_id UUID)
RETURNS VOID AS $$
BEGIN
    -- Create default room features
    INSERT INTO core.room_features (tenant_id, name, description) VALUES
    (p_tenant_id, 'WiFi', 'Complimentary wireless internet'),
    (p_tenant_id, 'TV', 'Flat screen television'),
    (p_tenant_id, 'AC', 'Air conditioning'),
    (p_tenant_id, 'Minibar', 'In-room minibar'),
    (p_tenant_id, 'Safe', 'In-room safe'),
    (p_tenant_id, 'Balcony', 'Private balcony'),
    (p_tenant_id, 'Sea View', 'Sea view from room');
    
    -- Create default chart of accounts
    INSERT INTO fin.accounts (tenant_id, code, name, type) VALUES
    (p_tenant_id, '1000', 'Cash', 'ASSET'),
    (p_tenant_id, '1100', 'Accounts Receivable', 'ASSET'),
    (p_tenant_id, '1200', 'Inventory', 'ASSET'),
    (p_tenant_id, '2000', 'Accounts Payable', 'LIABILITY'),
    (p_tenant_id, '2100', 'Tax Liability', 'LIABILITY'),
    (p_tenant_id, '3000', 'Equity', 'EQUITY'),
    (p_tenant_id, '4000', 'Room Revenue', 'REVENUE'),
    (p_tenant_id, '4100', 'F&B Revenue', 'REVENUE'),
    (p_tenant_id, '4200', 'Other Revenue', 'REVENUE'),
    (p_tenant_id, '4300', 'Service Charge Revenue', 'REVENUE'),
    (p_tenant_id, '5000', 'Cost of Goods Sold', 'EXPENSE'),
    (p_tenant_id, '6000', 'Operating Expenses', 'EXPENSE'),
    (p_tenant_id, '6100', 'Utilities', 'EXPENSE'),
    (p_tenant_id, '6200', 'Maintenance', 'EXPENSE');
    
    -- Create default UOMs
    INSERT INTO inv.uoms (tenant_id, name, symbol) VALUES
    (p_tenant_id, 'Each', 'EA'),
    (p_tenant_id, 'Kilogram', 'KG'),
    (p_tenant_id, 'Liter', 'L'),
    (p_tenant_id, 'Piece', 'PC'),
    (p_tenant_id, 'Box', 'BOX'),
    (p_tenant_id, 'Bottle', 'BTL');
    
    -- Create default taxes
    INSERT INTO fin.taxes (tenant_id, name, rate, type) VALUES
    (p_tenant_id, 'VAT', 18.00, 'VAT'),
    (p_tenant_id, 'Service Charge', 10.00, 'SERVICE');
    
END;
$$ LANGUAGE plpgsql;

-- Function to refresh materialized views
CREATE OR REPLACE FUNCTION report.refresh_all_views()
RETURNS VOID AS $$
BEGIN
    REFRESH MATERIALIZED VIEW report.occupancy_daily;
    REFRESH MATERIALIZED VIEW report.revenue_daily;
    REFRESH MATERIALIZED VIEW report.adr_daily;
    REFRESH MATERIALIZED VIEW report.inventory_status;
    REFRESH MATERIALIZED VIEW report.maintenance_summary;
END;
$$ LANGUAGE plpgsql;

-- Schedule automatic refresh of materialized views (using pg_cron if available)
-- SELECT cron.schedule('refresh-views', '0 1 * * *', 'SELECT report.refresh_all_views();');

COMMIT;