"""HTTP integration checks against a disposable SQLite database (Python standard library)."""

import html
import http.cookiejar
import os
from pathlib import Path
import re
import shutil
import socket
import sqlite3
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request


ROOT = Path(__file__).resolve().parents[1]
PHP = os.environ.get("PHP_BIN", "php")
CHECKS = 0


def check(condition, description):
    global CHECKS
    if not condition:
        raise AssertionError(description)
    CHECKS += 1


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


class Browser:
    def __init__(self, base):
        self.base = base
        self.cookies = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(self.cookies), NoRedirect()
        )

    def request(self, path, data=None, expected=200):
        payload = None if data is None else urllib.parse.urlencode(data).encode()
        request = urllib.request.Request(self.base + path, data=payload)
        try:
            response = self.opener.open(request, timeout=5)
        except urllib.error.HTTPError as error:
            response = error
        body = response.read().decode()
        check(response.code == expected, f"{path}: expected {expected}, got {response.code}: {body[:300]}")
        check(not re.search(r"(?:Warning|Fatal error|Deprecated):", body), f"No PHP errors on {path}")
        return body, response.headers

    def token(self, path="/login"):
        body, _ = self.request(path)
        match = re.search(r'name="_token" value="([a-f0-9]+)"', body)
        check(match is not None, f"CSRF token on {path}")
        return match.group(1)

    def login(self, email, password, expected=303):
        token = self.token()
        return self.request("/login", {"email": email, "password": password, "_token": token}, expected)


def run(base, database):
    visitor = Browser(base)
    world, _ = visitor.request("/")
    us, _ = visitor.request("/us")
    tech, _ = visitor.request("/tech?source=menu")
    check('href="/tech"' in world, "Technology navigation points to /tech")
    check("What happens after you submit a form?" in tech, "Technology has its own first post")
    check("Why Git commits should describe one change" in tech, "Technology has two posts")
    check("A student guide to community projects" in us and "A student guide to community projects" not in tech, "U.S. and Tech content differ")
    check('aria-current="page">Technology' in tech, "Active Technology navigation")
    visitor.request("/assets/dist/css/bootstrap.min.css")
    for path in ["/unknown", "/storage/learnphp.sqlite", "/tables.sql", "/bin/setup.php", "/bootstrap.php"]:
        visitor.request(path, expected=404)

    admin_get = ["/admin/users", "/admin/users/view?id=1", "/admin/users/create", "/admin/users/edit?id=1", "/admin/articles"]
    for path in admin_get:
        _, headers = visitor.request(path, expected=303)
        check(headers.get("Location") == "/login", "Guests redirected to login")
    visitor.request("/admin/users/delete", {"id": 1}, expected=403)
    visitor.login("' OR 1=1 --", "wrong", expected=422)
    visitor.login("admin@example.test", "wrong", expected=422)

    admin = Browser(base)
    admin.token()
    before_login = list(admin.cookies)[0].value
    _, headers = admin.login("admin@example.test", "TestAdminPassword123")
    check(headers.get("Location") == "/admin/articles", "Admin lands on articles")
    check(list(admin.cookies)[0].value != before_login, "Session ID changes at login")
    csrf = admin.token("/admin/users/create")
    db = sqlite3.connect(database)
    db.row_factory = sqlite3.Row
    admin_hash = db.execute("SELECT password FROM users WHERE id=1").fetchone()[0]

    for path in ["/admin/users", "/admin/users/view?id=1", "/admin/users/edit?id=1"]:
        body, _ = admin.request(path)
        check(admin_hash not in body and "TestAdminPassword123" not in body, "Password and hash never rendered")
    check('href="/admin/users"' in admin.request("/admin/articles")[0], "Admin menu links to users")
    for value in ["", "0", "-1", "abc", "1 OR 1=1", "999999999999999999999999"]:
        admin.request("/admin/users/view?id=" + urllib.parse.quote(value), expected=404)
    admin.request("/admin/users/view?id[]=1", expected=404)
    admin.request("/admin/users/view?id=99999", expected=404)
    admin.request("/admin/users/edit?id=99999", expected=404)
    admin.request("/admin/users/delete?id=1", expected=405)

    user_data = {
        "name": "O'Neil <script>alert(1)</script>", "email": "student@example.test",
        "password": "StudentPassword123", "password_confirm": "StudentPassword123", "_token": csrf,
    }
    admin.request("/admin/users", {**user_data, "_token": "wrong"}, expected=403)
    invalid_cases = [
        {"name": " "}, {"email": "invalid"}, {"name": "a" * 101},
        {"password": "short", "password_confirm": "short"},
        {"password": "", "password_confirm": ""}, {"password_confirm": "different"},
        {"password": "a" * 73, "password_confirm": "a" * 73},
        {"password": "password\0x", "password_confirm": "password\0x"},
        {"email": "ADMIN@EXAMPLE.TEST"},
    ]
    for invalid in invalid_cases:
        admin.request("/admin/users", {**user_data, **invalid}, expected=422)
    check(db.execute("SELECT COUNT(*) FROM users").fetchone()[0] == 1, "Invalid creates do not insert users")

    admin.request("/admin/users", {**user_data, "is_admin": "1"}, expected=303)
    saved = db.execute("SELECT * FROM users WHERE email=?", (user_data["email"],)).fetchone()
    user_id = saved["id"]
    old_hash = saved["password"]
    check(old_hash.startswith("$2y$") and old_hash != user_data["password"], "New password is bcrypt hashed")
    check(saved["is_admin"] == 0, "Submitted admin flag is ignored")
    for path in ["/admin/users", f"/admin/users/view?id={user_id}", f"/admin/users/edit?id={user_id}"]:
        body, _ = admin.request(path)
        check("<script>alert(1)</script>" not in body and html.escape(user_data["name"], quote=True).replace("&#x27;", "&#039;") in body, "Names are escaped")
        check(old_hash not in body and user_data["password"] not in body, "No user password disclosure")

    student = Browser(base)
    student.login(user_data["email"], user_data["password"])
    for path in admin_get:
        student.request(path, expected=403)
    student_token = student.token("/")
    for path in ["/admin/users", "/admin/users/edit", "/admin/users/delete", "/admin/articles", "/admin/articles/edit", "/admin/articles/delete"]:
        student.request(path, {"id": 1, "_token": student_token}, expected=403)

    edits = {**user_data, "id": user_id, "name": "Updated student", "password": "", "password_confirm": ""}
    admin.request("/admin/users/edit", edits, expected=303)
    updated = db.execute("SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
    check(updated["password"] == old_hash, "Blank password preserves the exact stored hash")
    check(updated["name"] == "Updated student", "Profile update persists")
    admin.request("/admin/users/edit", {**edits, "email": "ADMIN@example.test"}, expected=422)
    admin.request("/admin/users/edit", {**edits, "password_confirm": "unexpected"}, expected=422)
    admin.request("/admin/users/edit", {**edits, "id": 99999}, expected=404)
    check(db.execute("SELECT email FROM users WHERE id=?", (user_id,)).fetchone()[0] == user_data["email"], "Rejected update preserves email")
    edits.update(email="updated@example.test", password="ChangedPassword123", password_confirm="ChangedPassword123")
    admin.request("/admin/users/edit", edits, expected=303)
    check(db.execute("SELECT password FROM users WHERE id=?", (user_id,)).fetchone()[0] != old_hash, "New password updates hash")
    Browser(base).login(edits["email"], user_data["password"], expected=422)
    Browser(base).login(edits["email"], edits["password"])

    article = {"title": "O'Reilly <script>alert(2)</script>", "body": "A quote: ' and <b>text</b>", "_token": csrf}
    admin.request("/admin/articles", article, expected=303)
    article_id = db.execute("SELECT MAX(id) FROM articles").fetchone()[0]
    for path in ["/", "/admin/articles", f"/admin/articles/view?id={article_id}", f"/admin/articles/edit?id={article_id}"]:
        body, _ = admin.request(path)
        check("<script>alert(2)</script>" not in body and "&lt;script&gt;" in body, "Article text is escaped")
    admin.request("/admin/articles/edit", {**article, "id": article_id, "title": "Edited article"}, expected=303)
    admin.request("/admin/articles/edit", {**article, "id": article_id, "title": " "}, expected=422)
    admin.request("/admin/articles/delete?id=" + str(article_id), expected=405)
    admin.request("/admin/articles/delete", {"id": article_id, "_token": csrf}, expected=303)
    admin.request(f"/admin/articles/view?id={article_id}", expected=404)

    admin.request("/admin/users/delete", {"id": 1, "_token": csrf}, expected=422)
    admin.request("/admin/users/delete", {"id": user_id, "_token": csrf}, expected=303)
    check(db.execute("SELECT COUNT(*) FROM users WHERE id=?", (user_id,)).fetchone()[0] == 0, "Deleted user is gone")
    admin.request(f"/admin/users/view?id={user_id}", expected=404)
    admin.request("/admin/users/delete", {"id": user_id, "_token": csrf}, expected=404)
    student.request("/admin/users", expected=303)
    Browser(base).login(edits["email"], edits["password"], expected=422)
    admin.request("/logout", {"_token": csrf}, expected=303)
    admin.request("/admin/users", expected=303)
    db.close()


def main():
    if shutil.which(PHP) is None:
        raise SystemExit("PHP is required. Install it or set PHP_BIN to your PHP executable.")
    with tempfile.TemporaryDirectory(prefix="learnphp-test-") as directory:
        database = Path(directory) / "test.sqlite"
        env = {**os.environ, "DB_DSN": f"sqlite:{database}", "ADMIN_EMAIL": "admin@example.test", "ADMIN_PASSWORD": "TestAdminPassword123"}
        setup = subprocess.run([PHP, "bin/setup.php"], cwd=ROOT, env=env, capture_output=True, text=True, check=True)
        check("Administrator created." in setup.stdout, "Fresh database setup")
        setup_again = subprocess.run([PHP, "bin/setup.php"], cwd=ROOT, env=env, capture_output=True, text=True, check=True)
        check("password unchanged" in setup_again.stdout, "Setup is repeatable")
        with socket.socket() as listener:
            listener.bind(("127.0.0.1", 0))
            port = listener.getsockname()[1]
        base = f"http://127.0.0.1:{port}"
        with tempfile.TemporaryFile(mode="w+") as log:
            server = subprocess.Popen([PHP, "-d", f"session.save_path={directory}", "-S", f"127.0.0.1:{port}", "-t", "public", "public/index.php"], cwd=ROOT, env=env, stdout=log, stderr=log)
            try:
                for _ in range(50):
                    try:
                        with urllib.request.urlopen(base + "/login", timeout=0.2):
                            break
                    except (urllib.error.URLError, TimeoutError):
                        time.sleep(0.1)
                else:
                    raise AssertionError("PHP server did not start")
                run(base, database)
            except Exception:
                log.seek(0)
                print(log.read())
                raise
            finally:
                server.terminate()
                server.wait(timeout=5)
            log.seek(0)
            check(not re.search(r"(?:Warning|Fatal error|Deprecated|Uncaught).*", log.read()), "Clean PHP server log")
    print(f"PASS: {CHECKS} checks (HTTP, validation, permissions, passwords, CSRF, escaping and persistence)")


if __name__ == "__main__":
    main()
