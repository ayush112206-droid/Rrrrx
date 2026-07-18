<?php
// ── Server-side PDF Redirect ─────────────────────────────────────
// Decrypts PDF link server-side and redirects. AES key never in browser.
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
auto_login();
send_security_headers();

$enc = $_GET['l'] ?? '';
if(empty($enc)){ http_response_code(400); die('Bad request'); }

$u = decrypt_appx(urldecode($enc));
if(empty($u)){
    // Maybe it's already a plain URL
    $u = urldecode($enc);
    if(strpos($u,'http')!==0){ http_response_code(400); die('Invalid link'); }
}

$viewer='https://dark-rwa.vercel.app/pdf-viewer?url=';
header('Location: '.$viewer.urlencode($u));
exit;
