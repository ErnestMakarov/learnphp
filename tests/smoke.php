<?php

// Integration tests use a temporary database and never touch storage/learnphp.sqlite.
declare(strict_types=1);

$checks = 0;
function check(bool $condition, string $description): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($description);
    }
    $checks++;
}

class Browser
{
    public string $cookie = '';

    public function __construct(private string $base)
    {
    }

    public function request(string $path, ?array $data = null, int $expected = 200): array
    {
        $headers = "Cookie: {$this->cookie}\r\n";
        $options = ['method' => $data === null ? 'GET' : 'POST', 'ignore_errors' => true,
            'follow_location' => 0, 'timeout' => 5];
        if ($data !== null) {
            $options['content'] = http_build_query($data);
            $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        }
        $options['header'] = $headers;
        $body = file_get_contents($this->base . $path, false, stream_context_create(['http' => $options]));
        $responseHeaders = $http_response_header ?? [];
        preg_match('/\s(\d{3})\s/', $responseHeaders[0] ?? '', $status);
        check((int) ($status[1] ?? 0) === $expected, "$path expected $expected: " . substr((string) $body, 0, 300));
        check(!preg_match('/(?:Warning|Fatal error|Deprecated):/', (string) $body), "No PHP errors: $path");
        foreach ($responseHeaders as $header) {
            if (preg_match('/^Set-Cookie: (PHPSESSID=[^;]+)/i', $header, $match)) {
                $this->cookie = $match[1];
            }
        }
        return [(string) $body, implode("\n", $responseHeaders)];
    }

    public function token(string $path = '/login'): string
    {
        [$body] = $this->request($path);
        preg_match('/name="_token" value="([a-f0-9]+)"/', $body, $match);
        check(isset($match[1]), "CSRF token on $path");
        return $match[1];
    }

    public function login(string $email, string $password, int $expected = 303): array
    {
        return $this->request('/login', ['email' => $email, 'password' => $password, '_token' => $this->token()], $expected);
    }
}

function runChecks(string $base, PDO $db): void
{
    $visitor = new Browser($base);
    [$world] = $visitor->request('/');
    [$tech] = $visitor->request('/tech?source=menu');
    [$us] = $visitor->request('/us');
    check(str_contains($world, 'href="/tech"'), 'Technology menu link');
    check(str_contains($tech, 'What happens after you submit a form?'), 'First original Technology post');
    check(str_contains($tech, 'Why Git commits should describe one change'), 'Second original Technology post');
    check(!str_contains($us, 'Why Git commits should describe one change'), 'Technology content is separate');
    check(str_contains($tech, 'aria-current="page">Technology'), 'Active menu link');
    $visitor->request('/assets/dist/css/bootstrap.min.css');
    $visitor->request('/assets/dist/js/bootstrap.bundle.min.js');
    foreach (['/unknown', '/storage/learnphp.sqlite', '/tables.sql', '/bin/setup.php', '/bootstrap.php', '/composer.json', '/vendor/autoload.php'] as $path) {
        $visitor->request($path, null, 404);
    }
    $token = $visitor->token('/forms?test=1');
    [$form] = $visitor->request('/forms', ['_token' => $token, 'name' => '<script>alert(1)</script>', 'age' => '19']);
    check(str_contains($form, '&lt;script&gt;') && !str_contains($form, '<script>alert(1)'), 'Form escapes input');
    $visitor->request('/forms', ['_token' => $token, 'name' => 'Student', 'age' => '-1'], 422);
    $visitor->request('/forms', ['name' => 'Student', 'age' => '19'], 403);

    $adminPages = ['/admin/users', '/admin/users/view?id=1', '/admin/users/create', '/admin/users/edit?id=1', '/admin/articles', '/admin/posts'];
    foreach ($adminPages as $path) {
        [, $headers] = $visitor->request($path, null, 303);
        check(str_contains($headers, 'Location: /login'), 'Guest redirected');
    }
    $visitor->request('/admin/users/delete', ['id' => 1], 403);
    $visitor->login("' OR 1=1 --", 'wrong', 422);
    $visitor->login('admin@example.test', 'wrong', 422);

    $admin = new Browser($base);
    $admin->token();
    $beforeLogin = $admin->cookie;
    [, $headers] = $admin->login('admin@example.test', 'TestAdminPassword123');
    check(str_contains($headers, 'Location: /admin/articles'), 'Admin login redirects to articles');
    check($beforeLogin !== $admin->cookie, 'Session ID rotates on login');
    check(str_contains(strtolower($headers), 'httponly') && str_contains(strtolower($headers), 'samesite=lax'), 'Session cookie attributes');
    $csrf = $admin->token('/admin/users/create');
    $adminHash = $db->query('SELECT password FROM users WHERE id=1')->fetchColumn();
    foreach (['/admin/users', '/admin/users/view?id=1', '/admin/users/edit?id=1'] as $path) {
        [$body] = $admin->request($path);
        check(!str_contains($body, $adminHash) && !str_contains($body, 'TestAdminPassword123'), 'No password disclosure');
    }
    [$list] = $admin->request('/admin/users');
    check(str_contains($list, '>Created</th>') && str_contains($list, '>Updated</th>'), 'Dates in users list');
    foreach (['', '0', '-1', 'abc', '1 OR 1=1', '999999999999999999999'] as $id) {
        $admin->request('/admin/users/view?id=' . rawurlencode($id), null, 404);
    }
    $admin->request('/admin/users/view?id[]=1', null, 404);
    $admin->request('/admin/users/view?id=99999', null, 404);
    $admin->request('/admin/users/delete?id=1', null, 405);

    $user = ['name' => "O'Neil <script>alert(1)</script>", 'email' => 'student@example.test',
        'password' => 'StudentPassword123', 'password_confirm' => 'StudentPassword123', '_token' => $csrf];
    $admin->request('/admin/users', array_replace($user, ['_token' => 'wrong']), 403);
    foreach ([['name' => ' '], ['email' => 'invalid'], ['name' => str_repeat('a', 101)],
        ['password' => 'short', 'password_confirm' => 'short'], ['password' => '', 'password_confirm' => ''],
        ['password_confirm' => 'different'], ['password' => str_repeat('a', 73), 'password_confirm' => str_repeat('a', 73)],
        ['password' => "password\0x", 'password_confirm' => "password\0x"], ['email' => 'ADMIN@EXAMPLE.TEST']] as $invalid) {
        $admin->request('/admin/users', array_replace($user, $invalid), 422);
    }
    check((int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn() === 1, 'Invalid user creation does not persist');
    $admin->request('/admin/users', $user + ['is_admin' => '1'], 303);
    $saved = $db->query("SELECT * FROM users WHERE email='student@example.test'")->fetch(PDO::FETCH_ASSOC);
    $id = (int) $saved['id'];
    $oldHash = $saved['password'];
    check(password_verify($user['password'], $oldHash), 'Created password is correctly hashed');
    check((int) $saved['is_admin'] === 0, 'Cannot grant admin through form');
    foreach (['/admin/users', "/admin/users/view?id=$id", "/admin/users/edit?id=$id"] as $path) {
        [$body] = $admin->request($path);
        check(str_contains($body, htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8')) && !str_contains($body, '<script>alert(1)'), 'Escaped user name');
        check(!str_contains($body, $oldHash) && !str_contains($body, $user['password']), 'No user password disclosure');
    }
    $student = new Browser($base);
    $student->login($user['email'], $user['password']);
    foreach ($adminPages as $path) {
        $student->request($path, null, 403);
    }
    $studentToken = $student->token('/');
    foreach (['/admin/users', '/admin/users/edit', '/admin/users/delete', '/admin/articles', '/admin/articles/edit', '/admin/articles/delete'] as $path) {
        $student->request($path, ['id' => 1, '_token' => $studentToken], 403);
    }
    $edit = array_replace($user, ['id' => $id, 'name' => 'Updated student', 'password' => '', 'password_confirm' => '']);
    $admin->request('/admin/users/edit', $edit, 303);
    $updated = $db->query("SELECT * FROM users WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
    check($updated['password'] === $oldHash && $updated['name'] === 'Updated student', 'Blank password preserves hash; name saved');
    $admin->request('/admin/users/edit', array_replace($edit, ['email' => 'ADMIN@example.test']), 422);
    $admin->request('/admin/users/edit', array_replace($edit, ['password_confirm' => 'unexpected']), 422);
    $admin->request('/admin/users/edit', array_replace($edit, ['id' => 99999]), 404);
    check($db->query("SELECT email FROM users WHERE id=$id")->fetchColumn() === $user['email'], 'Rejected update preserves email');
    $edit = array_replace($edit, ['email' => 'updated@example.test', 'password' => 'ChangedPassword123', 'password_confirm' => 'ChangedPassword123']);
    $admin->request('/admin/users/edit', $edit, 303);
    (new Browser($base))->login($edit['email'], $user['password'], 422);
    (new Browser($base))->login($edit['email'], $edit['password']);

    $article = ['title' => "O'Reilly <script>alert(2)</script>", 'body' => "A quote: ' and <b>text</b>", '_token' => $csrf];
    $admin->request('/admin/articles', $article, 303);
    $articleId = (int) $db->query('SELECT MAX(id) FROM articles')->fetchColumn();
    foreach (['/', '/us', '/admin/articles', "/admin/articles/view?id=$articleId", "/admin/articles/edit?id=$articleId", "/admin/posts/view?id=$articleId"] as $path) {
        [$body] = $admin->request($path);
        check(!str_contains($body, '<script>alert(2)') && str_contains($body, '&lt;script&gt;'), 'Article output escaped');
    }
    $admin->request('/admin/articles/edit', array_replace($article, ['id' => $articleId, 'title' => 'Changed article', 'body' => '']), 303);
    $articleSaved = $db->query("SELECT * FROM articles WHERE id=$articleId")->fetch(PDO::FETCH_ASSOC);
    check($articleSaved['title'] === 'Changed article' && $articleSaved['body'] === '', 'Article edit persists and empty content is allowed');
    $admin->request('/admin/articles/edit', array_replace($article, ['id' => $articleId, 'title' => ' ']), 422);
    $admin->request("/admin/articles/delete?id=$articleId", null, 405);
    $admin->request('/admin/posts/delete', ['id' => $articleId, '_token' => $csrf], 303);
    $admin->request("/admin/articles/view?id=$articleId", null, 404);
    $admin->request('/admin/users/delete', ['id' => 1, '_token' => $csrf], 422);
    $admin->request('/admin/users/delete', ['id' => $id, '_token' => $csrf], 303);
    check((int) $db->query("SELECT COUNT(*) FROM users WHERE id=$id")->fetchColumn() === 0, 'User deletion persists');
    $admin->request("/admin/users/view?id=$id", null, 404);
    $student->request('/admin/users', null, 303);
    (new Browser($base))->login($edit['email'], $edit['password'], 422);

    $signup = new Browser($base);
    $signupToken = $signup->token('/register');
    $registration = ['name' => 'Registered student', 'email' => 'registered@example.test',
        'password' => 'Registration123', 'password_confirm' => 'Registration123', '_token' => $signupToken, 'is_admin' => '1'];
    $signup->request('/register', array_replace($registration, ['password_confirm' => 'wrong']), 422);
    $signup->request('/register', array_replace($registration, ['email' => 'ADMIN@EXAMPLE.TEST']), 422);
    $beforeSignup = $signup->cookie;
    $signup->request('/register', $registration, 303);
    check($signup->cookie !== $beforeSignup, 'Session rotates on registration');
    [$home] = $signup->request('/');
    check(str_contains($home, 'Registered student') && !str_contains($home, 'Manage users'), 'Registered user logged in without admin links');
    $record = $db->query("SELECT * FROM users WHERE email='registered@example.test'")->fetch(PDO::FETCH_ASSOC);
    check((int) $record['is_admin'] === 0 && password_verify('Registration123', $record['password']), 'Registration stores safe account');
    $signup->request('/admin/users', null, 403);
    $signup->request('/logout', ['_token' => $signup->token('/')], 303);
    $signup->login('registered@example.test', 'Registration123');
    $admin->request('/logout', ['_token' => $csrf], 303);
    $admin->request('/admin/users', null, 303);
}

$root = dirname(__DIR__);
$php = getenv('PHP_BIN') ?: PHP_BINARY;
$directory = sys_get_temp_dir() . '/learnphp-test-' . bin2hex(random_bytes(8));
mkdir($directory, 0700);
$database = $directory . '/test.sqlite';
$env = array_merge(getenv(), ['DB_DSN' => 'sqlite:' . $database, 'DB_USER' => '', 'DB_PASSWORD' => '',
    'ADMIN_EMAIL' => 'admin@example.test', 'ADMIN_PASSWORD' => 'TestAdminPassword123']);
$server = null;
$db = null;
$exitCode = 0;
try {
    foreach (['Administrator created.', 'password unchanged'] as $expected) {
        $process = proc_open([$php, 'bin/setup.php'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, $env);
        if (!is_resource($process)) {
            throw new RuntimeException('Could not start PHP setup.');
        }
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        check(proc_close($process) === 0 && str_contains($output, $expected), 'Setup: ' . $output . $error);
    }
    $socket = stream_socket_server('tcp://127.0.0.1:0', $errorNumber, $errorText);
    if (!$socket) {
        throw new RuntimeException($errorText);
    }
    $address = stream_socket_get_name($socket, false);
    fclose($socket);
    $server = proc_open([$php, '-d', 'session.save_path=' . $directory, '-d', 'error_reporting=32767', '-d', 'display_errors=1',
        '-S', $address, '-t', 'public', 'public/index.php'],
        [0 => ['pipe', 'r'], 1 => ['file', $directory . '/server.log', 'a'], 2 => ['file', $directory . '/server.log', 'a']], $pipes, $root, $env);
    if (!is_resource($server)) {
        throw new RuntimeException('Could not start PHP server.');
    }
    fclose($pipes[0]);
    $ready = false;
    for ($attempt = 0; $attempt < 50; $attempt++) {
        $connection = @stream_socket_client('tcp://' . $address, $errorNumber, $errorText, 0.1);
        if ($connection) {
            fclose($connection);
            $ready = true;
            break;
        }
        usleep(100000);
    }
    check($ready, 'PHP server started');
    $db = new PDO('sqlite:' . $database, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    runChecks('http://' . $address, $db);
} catch (Throwable $error) {
    fwrite(STDERR, "FAIL: " . $error->getMessage() . "\n");
    $exitCode = 1;
} finally {
    $db = null;
    if (is_resource($server)) {
        proc_terminate($server);
        proc_close($server);
    }
    $log = is_file($directory . '/server.log') ? file_get_contents($directory . '/server.log') : '';
    if (preg_match('/(?:Warning|Fatal error|Deprecated|Uncaught)/', $log)) {
        fwrite(STDERR, "PHP server reported errors:\n" . $log);
        $exitCode = 1;
    }
    foreach (glob($directory . '/*') as $file) {
        unlink($file);
    }
    rmdir($directory);
}
if ($exitCode === 0) {
    echo "PASS: $checks checks (HTTP, CRUD, registration, permissions, passwords, CSRF, escaping, persistence).\n";
}
exit($exitCode);
