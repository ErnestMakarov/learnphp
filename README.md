# LearnPHP

A classroom PHP journal. The project follows the controller/model/view approach from
[Kaspar Martin Suursalu's course example](https://github.com/Kasparsu/ta24blearnphp)
(reference commit `43ffc842591bc93212971c5103b7d33ab86c2a5f`).
The router, database access, forms and controllers are adapted for this repository.
Bootstrap CSS is distributed with its original MIT notice.

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

## Completed assignments

| Requirement | Implementation |
| --- | --- |
| Technology page and original posts | `PublicController::tech()`, `views/tech.php`, `/tech` |
| Shared page layout | Header, posts, sidebar and footer partials |
| Technology menu link | `views/partials/nav.php` |
| User list and detail view | `/admin/users`, `/admin/users/view?id=...` |
| Create a user | GET `/admin/users/create`, POST `/admin/users` |
| Edit a user | GET and POST `/admin/users/edit` |
| Delete a user | POST `/admin/users/delete` with confirmation and CSRF protection |
| Admin menu | Manage articles and Manage users links |
| Separate commits | Foundation, Tech, user list/details, creation, editing, deletion, verification |

The assignment's article URLs are `/admin/articles`; the teacher's current example uses
`/admin/posts`. This project uses the assignment's naming and keeps a working article CRUD.
New accounts created through the users form are ordinary users. Only the administrator
created by setup can manage records. The form does not grant administrator rights.
An administrator cannot delete their own account. An empty password on edit keeps the old
hash; a new password must match its confirmation. Passwords and hashes are never rendered.
Bcrypt passwords are limited to 72 bytes to avoid silent truncation.

## Verification

Run the HTTP integration checks with PHP and Python 3 installed:

```sh
python3 tests/smoke.py
```

Optionally set `PHP_BIN` to a different PHP executable. Tests create a temporary database,
sessions and a local PHP server, then clean them up; they do not use your development data.
They cover Tech navigation, article and user CRUD, login/logout, access restrictions,
duplicate emails, invalid IDs, password changes, CSRF, HTML escaping and database persistence.
The automated integration suite uses SQLite. MySQL is an optional configuration and is not
covered by this suite.

Manual check: sign in, open **Manage users**, create a user, view it, edit its name with an
empty password, then delete it. Open **Technology** from the public menu and check its two posts.

For a Russian explanation of the request flow, see [docs/how-it-works.md](docs/how-it-works.md).
