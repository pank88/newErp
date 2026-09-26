# Architecture

## Project layout

```
backend/
├── .env.example                         # Environment template
├── routes/
│   └── api.php                          # All API routes (served under /api)
├── app/
│   ├── Models/
│   │   ├── User.php                     # Authenticatable user with a role
│   │   ├── InventoryItem.php            # Stock item
│   │   └── Transaction.php              # Financial transaction
│   └── Http/Controllers/Api/
│       ├── AuthController.php           # register, login, profile, logout
│       ├── UserController.php           # list users (admin only)
│       ├── InventoryController.php      # inventory CRUD
│       ├── AccountingController.php     # transaction CRUD
│       ├── DashboardController.php      # headline totals
│       ├── ReportController.php         # inventory & accounting summaries
│       └── AnalyticsController.php      # 12-month trends
└── database/migrations/
    ├── 2025_09_06_000001_create_inventory_items_table.php
    ├── 2025_09_06_000002_create_transactions_table.php
    └── 2025_09_06_000003_add_role_to_users_table.php
```

## Request flow

```
Client ──HTTP──▶ routes/api.php ──▶ auth:api middleware (JWT) ──▶ Api\*Controller ──▶ Eloquent model ──▶ MySQL
                        │
                        └── /login, /register are public (no middleware)
```

Controllers are thin. Each one validates input where it needs to, calls Eloquent directly, and returns JSON.
There is no service or repository layer, no API Resources, and no pagination. Laravel serializes models and
arrays to JSON automatically.

## Authentication

- **Mechanism:** stateless JWT via `tymon/jwt-auth`.
- **Issuing tokens:** `POST /api/register` and `POST /api/login` return a `token`.
- **Using tokens:** send `Authorization: Bearer <token>` on every protected request.
- **Guard:** protected routes use the `auth:api` middleware, which must be configured as a `jwt` driver
  (see [SETUP.md](SETUP.md)).
- **Logout:** `POST /api/logout` invalidates the current token.

## Authorization (roles)

Users have a `role` string column, defaulting to `user`.

| Role | Access |
|---|---|
| `user` | All protected endpoints except listing users |
| `admin` | Everything, including `GET /api/users` |

Only `UserController@index` checks the role today. Inventory, accounting, reports and analytics are open to any
authenticated user. See [KNOWN_ISSUES.md](KNOWN_ISSUES.md) for recommendations.

## Modules

| Module | Controller | Model(s) | Endpoints |
|---|---|---|---|
| Auth | `AuthController` | `User` | `register`, `login`, `profile`, `logout` |
| Users | `UserController` | `User` | `GET /users` |
| Inventory | `InventoryController` | `InventoryItem` | REST resource `/inventory` |
| Accounting | `AccountingController` | `Transaction` | REST resource `/accounting` |
| Dashboard | `DashboardController` | `InventoryItem`, `Transaction` | `GET /dashboard/summary` |
| Reports | `ReportController` | `InventoryItem`, `Transaction` | `GET /reports/inventory`, `GET /reports/accounting` |
| Analytics | `AnalyticsController` | `Transaction` | `GET /analytics/trends` |

Full endpoint reference: [API.md](API.md).
