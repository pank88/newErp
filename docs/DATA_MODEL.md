# Data Model

```
┌──────────────┐     ┌────────────────────┐     ┌──────────────────┐
│    users     │     │  inventory_items   │     │   transactions   │
├──────────────┤     ├────────────────────┤     ├──────────────────┤
│ id           │     │ id                 │     │ id               │
│ name         │     │ name               │     │ date             │
│ email        │     │ sku (unique)       │     │ type             │
│ password     │     │ quantity           │     │ description      │
│ role         │     │ location           │     │ amount           │
│ ...          │     │ cost_price         │     │ created_at       │
│ timestamps   │     │ sale_price         │     │ updated_at       │
└──────────────┘     │ created_at         │     └──────────────────┘
                     │ updated_at         │
                     └────────────────────┘
```

The tables are independent: there are no foreign keys between them yet.

## `users`

Created by Laravel's default migration. This repo adds `role` in
`2025_09_06_000003_add_role_to_users_table.php`.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `name` | string | |
| `email` | string, unique | Login identifier |
| `password` | string | Bcrypt hash (`Hash::make`) |
| `role` | string | Default `'user'`. Recognized values: `user`, `admin` |
| `remember_token`, `email_verified_at`, timestamps | | Laravel defaults |

**Model:** `App\Models\User`
- Fillable: `name`, `email`, `password`, `role`
- Hidden from JSON: `password`, `remember_token`

## `inventory_items`

Migration: `2025_09_06_000001_create_inventory_items_table.php`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint | PK | |
| `name` | string | required | Item name |
| `sku` | string | **unique**, required | Stock-keeping unit code |
| `quantity` | integer | required | Units on hand |
| `location` | string | required | Warehouse, store or bin |
| `cost_price` | decimal(10,2) | required | Unit purchase cost |
| `sale_price` | decimal(10,2) | required | Unit selling price |
| `created_at`, `updated_at` | timestamp | | |

**Model:** `App\Models\InventoryItem`. Fillable: `name`, `sku`, `quantity`, `location`, `cost_price`, `sale_price`.

## `transactions`

Migration: `2025_09_06_000002_create_transactions_table.php`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint | PK | |
| `date` | date | required | Transaction date |
| `type` | string | required | Free text. **`income`** and **`expense`** are the values used by the dashboard and reports |
| `description` | string | required | |
| `amount` | decimal(10,2) | required | Positive amount; the direction comes from `type` |
| `created_at`, `updated_at` | timestamp | | |

**Model:** `App\Models\Transaction`. Fillable: `date`, `type`, `description`, `amount`.

> Types other than `income` and `expense` can be stored. They appear in `/analytics/trends`, but the dashboard
> totals and `/reports/accounting` ignore them.

## Monetary precision

`decimal(10,2)` allows values up to **99,999,999.99**. Money is stored without a currency; the system assumes a
single currency.
