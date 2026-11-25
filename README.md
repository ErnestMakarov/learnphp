# LearnPHP

PHP classroom blog by Ernest Makarov. Based on the controller/model/view approach in
[Kaspar Martin Suursalu's course](https://github.com/Kasparsu/ta24blearnphp).
The two assessed tasks are the **Technology page (02.10)** and **users CRUD (11.11)**.
See [the assignment checklist](docs/assignment-checklist.md) for exact file mappings.

## Requirements

- PHP **8.2+** with PDO and `pdo_sqlite` (or `pdo_mysql` for MariaDB).
- Composer 2, Git. No Node.js or Python is required.
- Docker Desktop only for the optional MariaDB setup below.

Check PHP with `php -v`, extensions with `php -m`, and Composer with `composer --version`.
On macOS with Homebrew: `brew install php composer`.
On Windows, enable `pdo_sqlite` in the PHP configuration shown by `php --ini`.

## Clone and run (SQLite)

```bash
git clone https://github.com/ErnestMakarov/learnphp.git
cd learnphp
composer install
php bin/setup.php
bash server.sh
```

If the project is already cloned, run the last three commands in its root folder.
Open **http://localhost:9999**. Setup prints the administrator email and a random password;
save them locally and sign in at `/login`. Running setup again keeps existing accounts,
passwords and articles. The database is `storage/learnphp.sqlite`, excluded from Git.
If the administrator password was lost, run `php bin/reset-password.php admin@example.test`.

Registering at `/register` creates an ordinary user and logs them in. Only administrators
can manage articles/users. The account role cannot be changed through a web form.

Equivalent start command: `php -S localhost:9999 -t public public/index.php`.
Only `public/` is served. This development server is for local coursework.
Press Ctrl+C to stop it.

## MariaDB with Docker (classroom option)

From the project root, with Docker Desktop running:

```bash
docker compose up -d --wait
export DB_DSN='mysql:host=127.0.0.1;port=33061;dbname=learnphp;charset=utf8mb4'
export DB_USER='learnphp'
export DB_PASSWORD='local-coursework'
composer install
php bin/setup.php
bash server.sh --mysql
```

The Compose file pins the MariaDB 11.4 series, binds the database to localhost only and
keeps data in a named volume. The listed passwords are **local development defaults**.
DataGrip: host `127.0.0.1`, port `33061`, database/user `learnphp`, password `local-coursework`.
`docker compose down` stops the database while preserving the volume; do not add `-v`
unless you intend to delete that database.

`tables.sql` is the MariaDB/MySQL schema. SQLite uses `database/schema.sqlite.sql`.
Setup creates missing tables, but does not migrate an older, differently structured database.
The existing `articles` and `users` data from this project remain compatible.
Environment variables come from the shell; `.env` files are **not** automatically loaded.
To return to SQLite in the same terminal: `unset DB_DSN DB_USER DB_PASSWORD`.
Use the same database environment for password resets and setup as for the server.

## Project structure

| Location | Purpose |
| --- | --- |
| `public/index.php` | Request entry point, session setup and dispatch |
| `bootstrap.php`, `composer.json` | Composer PSR-4 autoloading: `App\\` → `src/` |
| `src/Route.php`, `src/Router.php`, `routes.php` | Route objects, path/method matching and route list |
| `src/Controllers/` | Public pages, authentication, article and user actions |
| `src/Models/`, `src/DB.php` | Abstract model, record objects, PDO prepared queries |
| `src/Validation/UserInput.php` | Shared registration/admin user validation |
| `views/` | Bootstrap templates, shared partials, escaped output |
| `bin/` | Database setup and local password recovery |
| `exercises/basics.php` | Standalone examples of the early PHP lessons |
| `tests/smoke.php` | HTTP integration checks using PHP only |

`composer.lock` records exact dependencies. `composer install` recreates `vendor/`;
`vendor/` is not committed. `symfony/var-dumper` provides `dump()` and `dd()`.
Its 7.4 series supports PHP 8.2; the teacher's newer 8.x dependency needs a newer PHP runtime.
The project classes use PSR-4 naming and conventional PSR-12 layout; no full style audit is claimed.

## Pages and behavior

- `/`, `/us`: articles from the database. The home page also has hero/featured partials.
- `/tech`: two original technology posts, its own controller method and shared layout.
- `/forms`: GET shows a form, POST processes a name and age with validation.
- `/register`, `/login`, POST `/logout`: registration, login and logout using server sessions.
- `/admin/users`: list, view, create, edit and delete users; dates are shown, passwords/hashes are not.
- `/admin/articles`: article CRUD. `/admin/posts` and its action URLs are equivalent aliases
  matching the classroom naming. Existing `/admin/articles` links remain supported.

Both CRUDs use GET to view forms and POST to change data. POST forms check CSRF tokens.
A blank password during editing preserves the existing hash; a supplied password is validated
and hashed with bcrypt. An administrator cannot delete the account they are using.
Names and content are HTML-escaped. Duplicate email is rejected by validation and a database
unique constraint. Non-admin users receive HTTP 403 when opening admin pages.
Article content may be empty; the title is required.

## Verification

```bash
composer validate --strict
php tests/smoke.php
php exercises/basics.php
```

The test runner needs `pdo_sqlite`, enabled `proc_open` and HTTP streams (`allow_url_fopen`).
It starts a local PHP server with a temporary database/session directory, tests the real HTTP
routes and removes the temporary files. It does not touch development data. Optional `PHP_BIN`
selects a different PHP executable. Tests cover Tech navigation, user/article CRUD, registration,
login/logout, permissions, password changes, duplicates, invalid IDs, CSRF and HTML escaping.
The automated database suite targets SQLite. The Docker/MariaDB runtime must be checked
on a computer with Docker; it was not available in the verification environment.

Manual demonstration: sign in as admin → Manage users → New user → View → Edit (leave password
blank) → Edit with a new password → Delete. Show Technology using the public menu, then test
registration in a private browser window. Detailed Russian explanations are in
[docs/how-it-works.md](docs/how-it-works.md).

## Scope and credits

This is the plain-PHP `learnphp` project through the Composer lesson, not the separate Laravel
blog started on 18 November. The first-period X is a grading status, not an extra programming
feature. Submit the GitHub link in Teams for both assessed tasks; only the teacher can update
Tahvel after checking the work.

Course examples: [ta24blearnphp](https://github.com/Kasparsu/ta24blearnphp) and
[lesson notes](https://github.com/Kasparsu/TA24B/tree/main/backend).
Bundled Bootstrap CSS/JS retain their MIT notices. Composer dependencies retain their licenses.
The application is adapted coursework, not a verbatim copy of every classroom experiment.
