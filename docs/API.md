# API Reference

- **Base URL:** `http://<host>/api`
- **Format:** JSON requests and responses. Send `Accept: application/json` so validation errors come back as JSON
  (HTTP 422) instead of redirects.
- **Auth:** every endpoint except `register` and `login` needs `Authorization: Bearer <token>`.
  A missing or invalid token returns **401**.

## Endpoint summary

| Method | Path | Auth | Description |
|---|---|---|---|
| POST | `/register` | – | Create an account and get a token |
| POST | `/login` | – | Get a token |
| GET | `/profile` | ✔ | Current user |
| POST | `/logout` | ✔ | Invalidate the current token |
| GET | `/users` | ✔ admin | List all users |
| GET | `/inventory` | ✔ | List inventory items |
| POST | `/inventory` | ✔ | Create an item |
| GET | `/inventory/{id}` | ✔ | Get an item |
| PUT/PATCH | `/inventory/{id}` | ✔ | Update an item |
| DELETE | `/inventory/{id}` | ✔ | Delete an item |
| GET | `/accounting` | ✔ | List transactions |
| POST | `/accounting` | ✔ | Create a transaction |
| GET | `/accounting/{id}` | ✔ | Get a transaction |
| PUT/PATCH | `/accounting/{id}` | ✔ | Update a transaction |
| DELETE | `/accounting/{id}` | ✔ | Delete a transaction |
| GET | `/dashboard/summary` | ✔ | Headline totals |
| GET | `/reports/inventory` | ✔ | Inventory summary |
| GET | `/reports/accounting` | ✔ | Income and expense by month |
| GET | `/analytics/trends` | ✔ | Totals by year, month and type for the last 12 months |

---

## Authentication

### `POST /register`

```json
{ "name": "Ada Lovelace", "email": "ada@example.com", "password": "secret123", "role": "user" }
```

`role` is optional and defaults to `user`. The endpoint doesn't validate its input yet (see Known Issues).

**200 OK**
```json
{
  "user": { "id": 1, "name": "Ada Lovelace", "email": "ada@example.com", "role": "user",
            "created_at": "2025-09-06T10:00:00.000000Z", "updated_at": "2025-09-06T10:00:00.000000Z" },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
```

### `POST /login`

```json
{ "email": "ada@example.com", "password": "secret123" }
```

**200 OK** returns `{ "token": "eyJ0eXAi..." }`. **401** returns `{ "error": "Unauthorized" }`.

### `GET /profile`

**200 OK** returns the authenticated user object (password hidden).

### `POST /logout`

**200 OK** returns `{ "message": "Successfully logged out" }`.

---

## Users

### `GET /users` (admin only)

**200 OK** returns an array of users. **403** returns `{ "error": "Forbidden" }` for non-admins.

> `users` is registered with `Route::apiResource`, but `UserController` implements only `index`. `POST`, `GET {id}`,
> `PUT` and `DELETE` on `/users` currently fail with a server error.

---

## Inventory

### Item object

```json
{
  "id": 1,
  "name": "USB-C Cable 1m",
  "sku": "CAB-USBC-1M",
  "quantity": 250,
  "location": "Warehouse A",
  "cost_price": "2.40",
  "sale_price": "7.99",
  "created_at": "2025-09-06T10:00:00.000000Z",
  "updated_at": "2025-09-06T10:00:00.000000Z"
}
```

Decimals are returned as strings (MySQL `decimal` behavior).

### `GET /inventory`
**200 OK** returns an array of every item. There is no pagination or filtering.

### `POST /inventory`

| Field | Rules |
|---|---|
| `name` | required |
| `sku` | required, unique in `inventory_items` |
| `quantity` | required, integer |
| `location` | required |
| `cost_price` | required, numeric |
| `sale_price` | required, numeric |

**201 Created** returns the item. **422** returns validation errors.

### `GET /inventory/{id}`
**200 OK** returns the item, or **404** if it doesn't exist.

### `PUT/PATCH /inventory/{id}`
Send any subset of the fields. **200 OK** returns the updated item, or **404**. Updates are **not validated**.

### `DELETE /inventory/{id}`
**204 No Content**. This also returns 204 if the id doesn't exist.

---

## Accounting (transactions)

### Transaction object

```json
{
  "id": 1,
  "date": "2025-09-01",
  "type": "income",
  "description": "Invoice #1001",
  "amount": "1500.00",
  "created_at": "...",
  "updated_at": "..."
}
```

### `GET /accounting`
**200 OK** returns an array of every transaction.

### `POST /accounting`

| Field | Rules |
|---|---|
| `date` | required, valid date (`YYYY-MM-DD`) |
| `type` | required. Use `income` or `expense` so the dashboard and reports count it |
| `description` | required |
| `amount` | required, numeric |

**201 Created** returns the transaction. **422** returns validation errors.

### `GET /accounting/{id}`, `PUT/PATCH /accounting/{id}`, `DELETE /accounting/{id}`
These behave the same way as the inventory equivalents (200 / 404, unvalidated update, 204).

---

## Dashboard

### `GET /dashboard/summary`

```json
{
  "total_inventory_items": 42,
  "total_inventory_value": "1234.50",
  "total_sales_value": "3120.00",
  "total_transactions": 118,
  "total_income": "54000.00",
  "total_expense": "31250.75"
}
```

| Field | Calculation |
|---|---|
| `total_inventory_items` | Number of inventory rows |
| `total_inventory_value` | `SUM(cost_price)`. Unit prices are summed without multiplying by quantity (see Known Issues) |
| `total_sales_value` | `SUM(sale_price)`, also without quantity |
| `total_transactions` | Number of transactions |
| `total_income` | `SUM(amount)` where `type = 'income'` |
| `total_expense` | `SUM(amount)` where `type = 'expense'` |

---

## Reports

### `GET /reports/inventory`

```json
{
  "total_items": 42,
  "total_value": "1234.50",
  "items_by_location": [
    { "location": "Warehouse A", "count": 30 },
    { "location": "Store 1", "count": 12 }
  ]
}
```

### `GET /reports/accounting`

```json
{
  "income_by_month":  [ { "month": 1, "total": "4500.00" }, { "month": 2, "total": "5100.00" } ],
  "expense_by_month": [ { "month": 1, "total": "2300.00" } ]
}
```

`month` is 1–12 and is grouped **across all years**, so January 2024 and January 2025 are combined.

---

## Analytics

### `GET /analytics/trends`

Totals per year, month and type for transactions dated within the last 12 months.

```json
[
  { "year": 2025, "month": 8, "type": "expense", "total": "2100.00" },
  { "year": 2025, "month": 8, "type": "income",  "total": "6400.00" },
  { "year": 2025, "month": 9, "type": "income",  "total": "1500.00" }
]
```

---

## Error format summary

| Status | When | Body |
|---|---|---|
| 401 | Missing or invalid token, or bad login | `{ "message": "Unauthenticated." }` / `{ "error": "Unauthorized" }` |
| 403 | Non-admin calls `/users` | `{ "error": "Forbidden" }` |
| 404 | Unknown id | `{ "message": "No query results for model ..." }` |
| 422 | Validation failed | `{ "message": "...", "errors": { "field": ["..."] } }` |
