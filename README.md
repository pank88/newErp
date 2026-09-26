# newErp

A lightweight ERP (Enterprise Resource Planning) REST API built on **Laravel** with **JWT authentication**.
It covers four business areas:

| Module | What it does |
|---|---|
| **Auth & Users** | Register, log in and out with JWT tokens; role-based access (`user` / `admin`) |
| **Inventory** | CRUD for stock items (SKU, quantity, location, cost and sale price) |
| **Accounting** | CRUD for financial transactions (income, expense, …) |
| **Dashboard, Reports & Analytics** | Totals, per-location and per-month summaries, 12-month trends |

## Documentation

| Document | Contents |
|---|---|
| [docs/SETUP.md](docs/SETUP.md) | Requirements, installation, configuration, running the API |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Project layout, request flow, authentication, roles |
| [docs/DATA_MODEL.md](docs/DATA_MODEL.md) | Database tables, columns and Eloquent models |
| [docs/API.md](docs/API.md) | Every endpoint with request and response examples |
| [docs/KNOWN_ISSUES.md](docs/KNOWN_ISSUES.md) | Gaps in the current scaffold and recommended fixes |

## Quick start

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve          # http://localhost:8000/api
```

```bash
# Register, then call a protected endpoint with the returned token
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Ada","email":"ada@example.com","password":"secret123"}'

curl http://localhost:8000/api/dashboard/summary \
  -H "Authorization: Bearer <token>"
```

See [docs/SETUP.md](docs/SETUP.md) for full details.
