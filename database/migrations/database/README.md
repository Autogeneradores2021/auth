# Database Migrations

This directory contains all the database migration files for the application. Migrations are a way to version control your database schema, allowing you to easily create, modify, and share the database structure with your team.

## Migration Files

- **2024_01_01_000000_create_validation_codes_table.php**: This migration creates the `validation_codes` table, which is used to store the two-factor authentication codes for users. The table includes the following columns:
  - `id`: Primary key for the table.
  - `user_id`: Foreign key referencing the user associated with the validation code.
  - `code`: The actual validation code sent to the user.
  - `type`: The type of validation (e.g., two-factor authentication).
  - `email`: The email address of the user to whom the code was sent.
  - `expires_at`: Timestamp indicating when the code expires.
  - `max_attempts`: Maximum number of attempts allowed to enter the code.
  - `attempts`: Number of attempts made to enter the code.
  - `is_active`: Boolean indicating whether the code is currently active.
  - `verified_at`: Timestamp indicating when the code was verified.
  - `metadata`: JSON column for storing additional information related to the validation code.

## Running Migrations

To run the migrations and create the necessary tables in your database, use the following Artisan command:

```
php artisan migrate
```

## Rolling Back Migrations

If you need to roll back the last batch of migrations, you can use the following command:

```
php artisan migrate:rollback
```

This will drop the tables created in the last migration batch.