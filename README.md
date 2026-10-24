<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

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

## Admin API

Admin data and actions are available under `/api/v1/admin`. Requests require Laravel Sanctum authentication and the `admin` role. The Blade admin pages use same-origin requests with the current session and CSRF token.

| Resource | Endpoints |
| --- | --- |
| Dashboard | `GET /dashboard` |
| Registrations | `GET /registrations`, `/registrations/approved`, `/registrations/rejected`, `/registrations/{id}`; `POST /registrations/{id}/approve`, `/registrations/{id}/reject` |
| User management | `GET /users`, `/users/{id}`; `PATCH /users/{id}/status` |
| Seller compliance | `GET /seller-compliance`, `/seller-compliance/{id}`; `POST /seller-compliance/{id}/suspend`, `/seller-compliance/products/{id}/approve`, `/warn`, `/remove` |
| Complaints and commissions | `GET /complaints`, `PATCH /complaints/{id}/status`; `GET /commissions` |
| Logistics | `GET /logistics-management`; registration decisions and branch CRUD under `/logistics-management` |
| Platform settings | `GET /platform-settings`; announcement and policy CRUD under `/platform-settings` |
| Messages | Conversations, contacts, messages, and attachments under `/messages` |
| Notifications | `GET /notifications`; `PATCH /notifications/read-all` and `/notifications/{id}/read` |
| Admin account | `GET` and `PUT /account`; `POST /account/notifications` and password-security actions under `/account/security` |
| Admin account management | Full administrators can `GET /account/admins`, `POST /account/admins`, and `PATCH /account/admins/{id}` to manage enabled status, module access, and credentials. Restricted administrator permissions are enforced for admin routes; ID photos are served through an authenticated endpoint. |

## Rider and logistics delivery API

The Flutter rider app and the web logistics portal use the same Laravel database and Sanctum API. Run `php artisan migrate` after updating the backend to create rider assignment, delivery-proof, attendance, recipient, support-ticket, calculator-history, Verify Order archive, order-message, COD, remittance, earnings, and delivery-action records.

| Role | Endpoint | Purpose |
| --- | --- | --- |
| Rider (public) | `POST /api/v1/rider/register` | Submit rider registration data and identity/vehicle documents; return a persistent application reference and review status. |
| Public | `GET /api/v1/locations/regions`, region province/district/city routes, province/district city routes, and city barangay routes under `/api/v1/locations` | Proxy and cache PSGC registration-picker data through Laravel instead of calling PSGC directly from Flutter. |
| Logistics | `GET /api/v1/logistics/branches`, `GET /riders` | List the authenticated company’s branches and eligible approved riders. |
| Logistics | `PATCH /api/v1/logistics/riders/{rider}/branch` | Add an eligible rider to one of the company’s branches or remove them when they have no active deliveries. |
| Logistics | `GET /api/v1/logistics/available-deliveries` | List orders awaiting pickup without exposing buyer delivery details before assignment. |
| Logistics | `POST /api/v1/logistics/deliveries/{order}/assign` | Assign an order awaiting pickup to an approved rider in the company’s branch. |
| Logistics | `GET /api/v1/logistics/deliveries` | Track the company’s assigned deliveries. |
| Rider | `GET /api/v1/rider/deliveries`, `GET /deliveries/{order}` | Read deliveries assigned to the signed-in rider. |
| Rider | `POST /api/v1/rider/deliveries/{order}/proof` | Submit required pickup or delivery proof; pickup and completion transitions are proof-gated. |
| Rider | `PATCH /api/v1/rider/deliveries/{order}/status` | Advance only the supported no-proof transition (`in_transit` → `out_for_delivery`). |
| Rider | `GET/PATCH /api/v1/rider/profile`, `GET/POST /api/v1/rider/attendance` | Read/update profile and clock in/out with a photo. |
| Rider | `GET/POST /api/v1/rider/recipients`, `GET/POST /api/v1/rider/support-tickets` | Manage saved recipients and submit support tickets. |
| Rider | `GET /api/v1/rider/finance`, `GET /api/v1/rider/earnings`, `POST /api/v1/rider/remittances` | View posted earnings and COD balances and submit remittances for review. |
| Rider | `GET /api/v1/rider/star-courier?period=month&year=2026&month=10` | Return completed delivery counts and explicitly posted earnings for the selected month or year. Star points/tiers are not fabricated or calculated. |
| Rider | `GET/POST/DELETE /api/v1/rider/calculations`, `GET /api/v1/rider/verify-orders`, and verify-order archive/restore/dismiss actions | Persist calculator history and rider-specific Verify Order archive state. |
| Rider | `GET /api/v1/rider/pickup-tasks`, pickup proof routes, and `GET /api/v1/rider/pickup-delivery-records` | Read assigned pickup tasks, submit pickup proof, and view pickup/delivery records. |
| Rider | `GET/POST /api/v1/rider/deliveries/{order}/messages` | Persist rider messages associated with an assigned order. |
| Logistics | `GET /api/v1/logistics/rider-remittances`, `PATCH /rider-remittances/{id}/review` | Review pending rider remittances as verified or rejected. |
| Logistics | `POST /api/v1/logistics/rider-earnings` | Post an explicit rider earnings ledger entry. |
| Logistics | `GET /api/v1/logistics/rider-support-tickets`, `PATCH /rider-support-tickets/{id}/review` | View and respond to support tickets submitted by riders in the company’s network. |
| Rider and Logistics | `GET/POST /api/v1/rider/support-tickets/{id}/messages`, `POST /api/v1/logistics/rider-support-tickets/{id}/messages` | Persist asynchronous rider support messages; Logistics replies are shared with the rider and trigger a notification. |
| Logistics | `GET /api/v1/logistics/rider-delivery-actions`, `PATCH /rider-delivery-actions/{id}/review` | Review rider-submitted delivery issue, reschedule, and return requests. |

Except for public rider registration and location lookup, endpoints require a Sanctum-authenticated user with the matching role. Rider status changes update the order, shipment timestamps/location, status history, and buyer notification in one transaction. COD is recorded only after a COD delivery is completed. Remittances remain pending until Logistics reviews them, and earnings appear only after an explicit ledger entry is posted. Buyer order data includes tracking details; sellers continue to see the same order status, while seller status changes are rejected after a rider has been assigned. Logistics staff can assign deliveries and review remittances at `/logistics/deliveries` and `/logistics/rider-operations`; riders can access the web delivery list at `/rider/dashboard`.

Rider controllers are grouped under `app/Http/Controllers/Api/Rider` by the mobile app’s `Register`, `Homepage` (including `Attendance`, `Calculator`, `Contact`, `DeliveryTask`, `MoneyReport`, `Notifications`, `PickupTask`, `PickupDeliveryRecord`, `StarCourier`, and `VerifyOrder`), `Content`, and `User` sections. About, Knowledgebase, Star Courier Guidelines, and navigation configuration are provided by the rider content endpoint; Live Support is an asynchronous, persisted ticket conversation—not a real-time chat service. Camera, map-arrival, and confirmation screens are workflow steps whose backend actions are performed by their parent attendance or delivery flows.

For local Flutter testing, pass the backend API URL with `--dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1` when using the Android emulator. For a physical device, use the development computer’s reachable LAN address instead and start Laravel with `php artisan serve --host=0.0.0.0`. The app keeps its production/emulator defaults when the override is omitted.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
