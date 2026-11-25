# LearnPHP

A classroom PHP journal. The project follows the controller/model/view approach from
[Kaspar Martin Suursalu's course example](https://github.com/Kasparsu/ta24blearnphp)
(reference commit `43ffc842591bc93212971c5103b7d33ab86c2a5f`).
The router, database access, forms and controllers are adapted for this repository.
Bootstrap CSS is distributed with its original MIT notice. No classmate's repository is used.

## Run locally

Requirements: PHP 8.2 or newer with PDO and `pdo_sqlite`. No Composer packages are required.

```sh
php bin/setup.php
php -S localhost:9999 -t public public/index.php
```

Open http://localhost:9999. The setup script prints the initial administrator's email and
a randomly generated password. Save them locally, then sign in at `/login`.
Running setup again keeps the existing administrator and password.
The SQLite file is stored in `storage/` and excluded from Git.

Always use `-t public`: only that directory should be served by the web server.
The PHP development server is intended for local coursework, not production hosting.

## MariaDB / MySQL (optional)

Create an empty `learnphp` database and enable `pdo_mysql`, then export the connection
settings before both setup and server startup:

```sh
export DB_DSN='mysql:host=127.0.0.1;port=3306;dbname=learnphp;charset=utf8mb4'
export DB_USER='your_database_user'
export DB_PASSWORD='your_database_password'
php bin/setup.php
php -S localhost:9999 -t public public/index.php
```

`tables.sql` contains the MySQL schema; SQLite uses `database/schema.sqlite.sql`.
Environment variables are read from the shell; `.env` files are not loaded automatically.

## Structure

- `public/index.php`: session setup and request entry point.
- `routes.php`: GET and POST routes.
- `src/Controllers/`: request handling and validation.
- `src/Models/`: user and article records.
- `src/DB.php`: PDO queries with bound parameters.
- `views/`: templates with shared partials and escaped output.
- `exercises/` and `docs/lesson-notes.md`: preserved early lesson work.

Admin pages require an administrator session. Every POST form uses a CSRF token.
Passwords are stored using `password_hash` and checked using `password_verify`.
