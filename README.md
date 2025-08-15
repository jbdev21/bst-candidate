# Ecom Volatile Pricing Starter

**Stack**: Laravel 11, Vue 3 (Inertia), SQLite (tests). Money in integer cents. Idempotent checkout. Mock fulfillment inventory.

## Setup

```bash
make setup
composer run dev
```

## Endpoints

- `POST /api/quote {sku, qty}` → quote with 5-min lock
- `POST /api/checkout {quote_id}` (header `Idempotency-Key` required)
- `GET /api/mock-fulfillment/availability/{sku}`
- `POST /api/mock-fulfillment/availability {sku, available_qty}`
- `POST /api/webhooks/payments`

## TODOs (for candidates)

- Enforce tolerance check precisely against current spot (see `CheckoutService`).
- Strengthen quote expiry + add tests for boundary conditions.
- Extend analytics (MA7 endpoint + sparkline on UI) — optional.

## Notes

- All money is integer cents. No floats.
- Orders are created pending; webhooks update status.
- CI runs Pint, Larastan, PHPUnit.
