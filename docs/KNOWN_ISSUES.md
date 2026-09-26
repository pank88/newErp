# Known Issues & Recommendations

These are gaps in the current scaffold, ordered roughly by impact.

## Security

### 1. User model is not JWT-ready
`JWTAuth::fromUser()` and `JWTAuth::attempt()` need `User` to implement `Tymon\JWTAuth\Contracts\JWTSubject`:

```php
class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
}
```

### 2. Anyone can register as admin
`AuthController@register` accepts `role` from the request. Ignore it (always create `user`) and promote admins
manually or through an admin-only endpoint.

### 3. Registration has no validation
Add `name|required`, `email|required|email|unique:users`, `password|required|min:8`.

### 4. Updates bypass validation
`InventoryController@update` and `AccountingController@update` use `$request->all()`. Validate with `sometimes|…`
rules (including `unique:inventory_items,sku,{id}` for SKU).

### 5. No role checks on business data
Any authenticated user can create or delete inventory and transactions. Consider policies or a role middleware.

## Functional

### 6. `/users` resource is incomplete
`Route::apiResource('users', …)` exposes five routes, but only `index` exists. Either use
`->only(['index'])` or implement the rest.

### 7. Inventory value ignores quantity
Dashboard `total_inventory_value` / `total_sales_value` and report `total_value` sum unit prices. For stock
valuation, use `SUM(quantity * cost_price)` and `SUM(quantity * sale_price)`.

### 8. Monthly accounting report merges years
`/reports/accounting` groups by `MONTH(date)` only. Group by year and month, or filter to a single year.

### 9. MySQL-only SQL
`YEAR()` and `MONTH()` fail on SQLite and PostgreSQL. Use driver-aware expressions if portability matters.

### 10. No pagination
`index` endpoints return every row. Use `->paginate()` as data grows.

### 11. Free-text transaction `type`
Restrict it with `in:income,expense` (or an enum) so totals stay consistent.

## Project structure

### 12. Incomplete Laravel skeleton
There's no `composer.json`, `artisan`, `config/`, base `Controller`, or `users` migration, and the frontend the
initial commit mentions isn't in the repo. See [SETUP.md](SETUP.md#bootstrapping-into-a-laravel-project).

### 13. No tests
Add feature tests for auth, CRUD and the summary endpoints.
