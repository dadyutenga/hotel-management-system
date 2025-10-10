# Hotel Management System

A comprehensive multi-tenant hotel management system built with Laravel 11 and PostgreSQL.

## Features

### Core Functionality
- **Multi-tenancy** - Powered by Stancl/Tenancy for complete tenant isolation
- **Property Management** - Manage multiple properties, buildings, floors, and rooms
- **User Management** - Role-based access control with 7 distinct roles
- **Room Types & Features** - Configurable room types with custom features

### Guest & Reservation Management ✨ NEW
- **Guest Profiles** - Complete guest management with contact details, preferences, and history
- **Reservations** - Full booking system with room assignment and availability checking
- **Check-in/Check-out** - Streamlined guest arrival and departure processes
- **Status Tracking** - Real-time reservation status updates

### Financial Management ✨ NEW
- **Folios** - Guest billing with charge tracking
- **Payments** - Multiple payment methods (Cash, Card, Mobile, Bank)
- **Invoicing** - Automated invoice generation (Proforma & Actual)
- **Revenue Tracking** - Comprehensive financial reports

### Operations Management ✨ NEW
- **Housekeeping** - Task assignment and tracking for cleaning staff
- **Maintenance** - Request management with priority levels
- **Room Status** - Automatic room status updates (Available, Occupied, Dirty, Clean, Maintenance)

### Point of Sale ✨ NEW
- **POS Orders** - Restaurant and bar order management
- **Menu Management** - Configurable menus and items
- **Room Service** - Automatic folio integration for room charges
- **Payment Processing** - Multiple payment methods with receipt generation

### Reports & Analytics ✨ NEW
- **Occupancy Reports** - Property-wise occupancy rates and statistics
- **Revenue Reports** - Detailed revenue breakdown (Room, F&B, Other)
- **Guest Analytics** - Demographics, repeat guests, marketing consent
- **Reservation Analytics** - Booking sources, cancellation rates, average stay
- **Housekeeping Performance** - Task completion statistics

## Technology Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: PostgreSQL with schemas (core, auth, res, fin, pos, ops, inv)
- **Multi-tenancy**: Stancl/Tenancy
- **Authentication**: Laravel Breeze
- **Frontend**: Blade templates with modern CSS
- **Icons**: Font Awesome

## Role-Based Access Control

The system includes 7 pre-defined roles with specific permissions:

1. **DIRECTOR** - Full system access, all properties
2. **MANAGER** - Property management, operations, reporting
3. **SUPERVISOR** - Housekeeping and maintenance oversight
4. **ACCOUNTANT** - Financial management and reporting
5. **RECEPTIONIST** - Guest services, reservations, check-in/out
6. **BAR_TENDER** - POS operations for F&B
7. **HOUSEKEEPER** - Task completion and room status updates

## Documentation

See [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) for detailed documentation of all features, routes, and implementation details.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
