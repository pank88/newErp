# Setup Guide

## Requirements

| Component | Version / notes |
|---|---|
| PHP | 8.1+ (whatever your Laravel version requires) |
| Composer | 2.x |
| Laravel | 9.x or later |
| Database | **MySQL / MariaDB**. Reports and analytics use `YEAR()` and `MONTH()`, which SQLite and PostgreSQL don't support as written |
| JWT package | [`tymon/jwt-auth`](https://github.com/tymondesigns/jwt-auth) (used by `AuthController`) |

> **Note:** the repository contains only the application code (models, controllers, migrations, routes and `.env.example`).
> It does not yet include a full Laravel skeleton (`composer.json`, `artisan`, `config/`, `bootstrap/`, the base
> `App\Http\Controllers\Controller`, or the default `users` table migration). See
> [Bootstrapping into a Laravel project](#bootstrapping-into-a-laravel-project).

## Bootstrapping into a Laravel project

1. Create a fresh Laravel app and copy this repo's `backend/` files over it:

   ```bash
   composer create-project laravel/laravel erp-backend
   cp -r backend/app backend/database backend/routes backend/.env.example erp-backend/
   cd erp-backend
   ```

2. Install JWT auth:

   ```bash
   composer require tymon/jwt-auth
   php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   php artisan jwt:secret
   ```

3. Make the `User` model implement `Tymon\JWTAuth\Contracts\JWTSubject` (add `getJWTIdentifier()` and
   `getJWTCustomClaims()`). See [KNOWN_ISSUES.md](KNOWN_ISSUES.md#1-user-model-is-not-jwt-ready).

4. Add a JWT `api` guard in `config/auth.php`:

   ```php
   'guards' => [
       'api' => [
           'driver'   => 'jwt',
           'provider' => 'users',
       ],
   ],
   ```

5. On Laravel 11+, register the API routes file (`php artisan install:api`, or wire `routes/api.php` in
   `bootstrap/app.php`). All routes are served under the `/api` prefix.

## Configuration (`.env`)

| Variable | Default in `.env.example` | Purpose |
|---|---|---|
| `APP_NAME` | `Laravel` | Application name |
| `APP_ENV` | `production` | Set to `local` during development |
| `APP_KEY` | *(empty)* | Generate with `php artisan key:generate` |
| `APP_DEBUG` | `false` | Set to `true` locally for stack traces |
| `APP_URL` | `http://localhost` | Base URL |
| `DB_CONNECTION` | `mysql` | Database driver |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3306` | Database server |
| `DB_DATABASE` | `aureuserp` | Database name |
| `DB_USERNAME` / `DB_PASSWORD` | `root` / *(empty)* | Credentials |
| `JWT_SECRET` | *(empty)* | Signing key for tokens; generate with `php artisan jwt:secret` |

## Database

```bash
mysql -u root -e "CREATE DATABASE aureuserp"
php artisan migrate
```

The migrations create `inventory_items` and `transactions`, and add a `role` column to `users`.
The `users` table itself comes from Laravel's default migration. See [DATA_MODEL.md](DATA_MODEL.md).

### Creating the first admin

Registration accepts a `role` field (see [KNOWN_ISSUES.md](KNOWN_ISSUES.md)). Until that is locked down, prefer
promoting a user manually:

```bash
php artisan tinker
>>> App\Models\User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

## Running

```bash
php artisan serve                     # http://localhost:8000
curl http://localhost:8000/api/login  # should return 405 (POST only), which confirms routing works
```
