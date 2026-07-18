<?php
define('API_BASE',    'https://rozgarapinew.teachx.in');
define('APP_NAME',    'Rozgar Learning');
define('APP_TAGLINE', 'ROZGAR with Ankit · Official Portal');
define('APP_VERSION', '4.0');
define('TG_CHANNEL',  'https://t.me/+rmB8RrKIm8A0NGVl');
define('TG_NAME',     'Dark Universe');
define('WA_CHANNEL',  'https://whatsapp.com/channel/0029VbAvDSX0QeahEg4kkE3U');
define('WA_NAME',     'Rozgar with Ankit');
define('ADMIN_PASS',  'Admin@Rozgar2024');
define('ADMIN_USER',  'admin');
define('MASTER_TOKEN',  'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjY0MDUxMzYiLCJ0aW1lc3RhbXAiOjE3ODA3MzU3NzYsIml2X3ZlciI6MTk5LCJzZXNzaW9uIjoiZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKSVV6STFOaUo5LmV5SnBaQ0k2SWpZME1EVXhNellpTENKbGJXRnBiQ0k2SW1abGVXNXNaV0Z5YmpnMk4wQm5iV0ZwYkM1amIyMGlMQ0p1WVcxbElqb2lRV0ZyWVhOb0lGTnBibWhoSWl3aWRHVnVZVzUwVkhsd1pTSTZJblZ6WlhJaUxDSjBaVzVoYm5ST1lXMWxJam9pY205NloyRnlYMlJpSWl3aWRHVnVZVzUwU1dRaU9pSWlMQ0prYVhOd2IzTmhZbXhsSWpwbVlXeHpaWDAuanMya2FUQ19jRk9kYzdZZjdFcHJNS0c1TFpjMjViTXhsZ1NpZXh6UkNXRSJ9.eoJOdPiGw76DpYd5v0cfQc4BRYSx8GdT7p6cusdTeec');
define('MASTER_USERID', '6405136');
define('AES_KEY', '638udh3829162018');
define('AES_IV',  'fedcba9876543210');
define('DATA_DIR',  __DIR__ . '/data/');
define('USERS_LOG', DATA_DIR . 'users.json');
ini_set('session.gc_maxlifetime', 86400 * 30);
session_set_cookie_params(86400 * 30);
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!file_exists(USERS_LOG)) file_put_contents(USERS_LOG, json_encode(['users' => []]));
function send_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: no-referrer');
    header('Cache-Control: no-store, no-cache, must-revalidate, private');
}
function decrypt_appx(string $enc): string {
    if (empty($enc)) return '';
    try {
        $parts = explode(':', $enc);
        $enc_data = base64_decode($parts[0]);
        $result = openssl_decrypt($enc_data, 'AES-128-CBC', AES_KEY, OPENSSL_RAW_DATA, AES_IV);
        return $result !== false ? rtrim($result, "\0") : '';
    } catch (\Exception $e) { return ''; }
}
function log_user(array $info): void {
    $data = json_decode(file_get_contents(USERS_LOG), true) ?: ['users' => []];
    $data['users'][] = array_merge($info, [
        'login_time' => date('Y-m-d H:i:s'),
        'ip'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'device' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 150),
        'id' => uniqid('u_', true),
    ]);
    if (count($data['users']) > 2000)
        $data['users'] = array_slice($data['users'], -2000);
    file_put_contents(USERS_LOG, json_encode($data, JSON_PRETTY_PRINT));
}
function is_logged_in(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_SESSION['token']) && !empty($_SESSION['userid']);
}
function auto_login(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = MASTER_TOKEN;
        $_SESSION['userid'] = MASTER_USERID;
        $_SESSION['phone'] = 'Rozgar Student';
        $_SESSION['login_type'] = 'auto';
    }
}
function require_login(): void { auto_login(); }
function require_admin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_logged_in'])) { header('Location: admin.php'); exit; }
}
