# Car Business Management System

A Laravel 12 multi-branch management system for:

- Car sale and purchase
- Service booking and workshop job cards
- POS sales, purchases and stock
- Employees, attendance, daily/weekly/monthly payroll
- Expenses, receipts, payments and management reports

## Advanced User-Friendly Features

- Responsive dashboard with quick actions and follow-up alerts
- Global search across invoices, parties, registrations, CNICs and mobile numbers
- Administrator user and role management with branch-level access
- Automatic daily, weekly and monthly payroll calculation
- Automatic car sale gross-profit calculation after cost and commission
- Branch-type-aware modules and branch data isolation
- Branch and date filters for management reports
- Multiple document and image attachments with audit history

## Completed Dedicated Module: Expense Invoice

The Expense module now has separate normalized tables for expense heads, invoices and line items. It includes dynamic add/remove rows, quantity, rate and tax calculations, invoice totals, attachments, draft/submitted workflow, branch security, invoice listing, editing, printing and seeded expense categories.

## Requirements

- PHP 8.2 or newer
- Composer 2
- SQLite or MySQL

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`.

Demo administrator:

- Email: `admin@example.com`
- Password: `password`

For MySQL, change `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` before migration.

## Architecture

The application uses a centralized `business_records` transaction table with module-specific JSON fields. This provides consistent CRUD, branch assignment, status workflow, amounts, approvals and audit history across all operational modules. It is suitable for an MVP and can later be normalized module by module as transaction volume and integration requirements grow.

## Important Production Work

Before production deployment, change the demo password, configure HTTPS and backups, review role permissions and approval limits, confirm tax rules, and add automated tests for the client's final business rules.
