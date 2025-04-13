# TLYN Gold Trading System

## Overview

This Laravel-based system enables users to trade gold by placing buy and sell orders, which are matched automatically. It includes features such as fee calculation, transaction logging, and user balance updates.

---

## Features

- Buy/Sell Order Placement
- Automatic Order Matching
- Tiered Fee Calculation
- User Gold Balance Update
- API Validation
- Artisan Command for Matching
- Job Queue Integration

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Hassanhs-97/laravel-tlyn.git
   cd laravel-tlyn
   ```

2. Run the install script:
   ```bash
    ./install.sh
   ```

3. Alternatively, run manually:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan queue:work
   ```

4. Set up a scheduler to run the matching job:
   ```bash
   * * * * * cd /laravel-tlyn && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## How It Works

- Orders with status `open` or `partial` are processed by a job every 10 seconds.
- Orders are matched with opposite types at matching prices.
- For each transaction, a fee is calculated based on the following:
  - Up to 1g: 2%
  - 1g–10g: 1.5%
  - More than 10g: 1%
  - Min fee: 500,000 IRR
  - Max fee: 50,000,000 IRR
- Once a transaction is created, a job updates both buyer's and seller's `gold_balance`.

---

## Artisan Commands

### Match Orders

```bash
php artisan tlyn:match-order
```

This command dispatches the `OrderMatchJob`.

---

## Project Structure

- `Jobs/OrderMatchJob.php` — Handles order matching logic.
- `Jobs/UpdateUserGoldBalance.php` — Updates user balances after a trade.
- `Services/FeeService.php` — Calculates fee per trade.
- `Rules/SufficientGoldBalance.php` — Custom validation rule for sell orders.
- `Http/Requests/OrderStoreRequest.php` — Validates order creation.
- `Console/Commands/MatchOrder.php` — Artisan command to trigger matching.

---

## Seeder and Factory

Initial users are seeded with random gold balances (e.g., 8.517g). You can customize seeding logic via `UserFactory`.

```bash
php artisan db:seed
```

---

## Notes

- Make sure the queue worker is running.
- Transactions and locks ensure data integrity for each match.

---

## License

MIT
