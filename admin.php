<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
send_security_headers();

$error = '';

// Login handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $u = trim($_POST['uname'] ?? '');
    $p = trim($_POST['upass'] ?? '');
    if ($u === ADMIN_USER && $p === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Wrong credentials.';
    }
}

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}

// Clear logs
if (isset($_GET['clear']) && !empty($_SESSION['admin_logged_in'])) {
    file_put_contents(USERS_LOG, json_encode(['users' => []]));
    header('Location: admin.php?cleared=1');
    exit;
}

$logged = !empty($_SESSION['admin_logged_in']);
$users  = [];
if ($logged && file_exists(USERS_LOG)) {
    $data = json_decode(file_get_contents(USERS_LOG), true) ?: ['users' => []];
    $users = array_reverse($data['users'] ?? []);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title>Admin · <?=APP_NAME?></title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--p:#7c3aed;--p2:#a78bfa;--bg:#030307;--c1:#0d0d14;--c2:#12121c;--b1:#1e1e30;--t1:#f8fafc;--t2:#94a3b8;--t3:#475569}
body{background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;min-height:100vh;padding:20px}
.wrap{max-width:900px;margin:0 auto}
.hdr{display:flex;align-items:center;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--b1)}
.hdr-ico{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--p),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff}
.hdr h1{font-size:20px;font-weight:800}
.hdr p{font-size:11px;color:var(--t3);font-family:'JetBrains Mono',monospace}
.hdr-right{margin-left:auto;display:flex;gap:8px}
.btn{padding:9px 16px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;border:none;display:inline-flex;align-items:center;gap:6px;text-decoration:none}
.btn-red{background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)}
.btn-dark{background:var(--c2);color:var(--t2);border:1px solid var(--b1)}
/* Login */
.login-wrap{display:flex;align-items:center;justify-content:center;min-height:80vh}
.lcard{background:var(--c1);border:1px solid var(--b1);border-radius:22px;padding:32px 28px;width:100%;max-width:380px}
.lcard h2{font-size:20px;font-weight:800;margin-bottom:6px}
.lcard p{font-size:12px;color:var(--t3);margin-bottom:22px;font-family:'JetBrains Mono',monospace}
.field{margin-bottom:14px}
.field label{display:block;font-size:11px;font-weight:600;color:var(--t2);margin-bottom:7px;letter-spacing:.05em}
.field input{width:100%;background:var(--c2);border:1px solid var(--b1);color:var(--t1);padding:12px 14px;border-radius:12px;font-size:14px;font-family:'Outfit',sans-serif;outline:none}
.field input:focus{border-color:rgba(124,58,237,.5)}
.btn-submit{width:100%;background:linear-gradient(135deg,var(--p),#5b21b6);color:#fff;border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;margin-top:4px}
.err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:10px 14px;font-size:13px;color:#f87171;margin-bottom:14px}
/* Stats */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:24px}
.sc{background:var(--c1);border:1px solid var(--b1);border-radius:16px;padding:16px}
.sv{font-size:26px;font-weight:900;line-height:1}
.sl{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.07em;margin-top:3px}
/* Table */
.tbl-wrap{background:var(--c1);border:1px solid var(--b1);border-radius:16px;overflow:hidden}
.tbl-head{display:flex;align-items:center;padding:16px 20px;border-bottom:1px solid var(--b1)}
.tbl-head h3{font-size:15px;font-weight:700}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:11px 16px;color:var(--t3);font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.08em;border-bottom:1px solid var(--b1);font-weight:400}
td{padding:12px 16px;border-bottom:1px solid rgba(30,30,48,.5);vertical-align:top;word-break:break-all}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(124,58,237,.04)}
.badge{display:inline-block;font-size:9px;padding:2px 8px;border-radius:5px;font-family:'JetBrains Mono',monospace;font-weight:700}
.badge-m{background:rgba(124,58,237,.15);color:var(--p2);border:1px solid rgba(124,58,237,.25)}
.badge-a{background:rgba(6,182,212,.1);color:#22d3ee;border:1px solid rgba(6,182,212,.2)}
.empty{text-align:center;padding:40px;color:var(--t3)}
.ok-msg{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:10px 16px;font-size:13px;color:#4ade80;margin-bottom:16px}
</style>
</head>
<body>
<div class="wrap">
<?php if (!$logged): ?>
<div class="login-wrap">
  <div class="lcard">
    <h2>Admin Panel</h2>
    <p><?=APP_NAME?> · SECURE ACCESS</p>
    <?php if($error): ?>
    <div class="err"><i class="fas fa-triangle-exclamation"></i> <?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="field">
        <label>USERNAME</label>
        <input type="text" name="uname" placeholder="Admin username" required autocomplete="off">
      </div>
      <div class="field">
        <label>PASSWORD</label>
        <input type="password" name="upass" placeholder="Admin password" required>
      </div>
      <input type="hidden" name="admin_login" value="1">
      <button type="submit" class="btn-submit"><i class="fas fa-unlock"></i> Login</button>
    </form>
  </div>
</div>

<?php else: ?>

<div class="hdr">
  <div class="hdr-ico"><i class="fas fa-shield-halved"></i></div>
  <div>
    <h1>Admin Panel</h1>
    <p><?=APP_NAME?> · USER LOG</p>
  </div>
  <div class="hdr-right">
    <a href="admin.php?clear=1" class="btn btn-red" onclick="return confirm('Clear all logs?')"><i class="fas fa-trash"></i> Clear</a>
    <a href="admin.php?logout=1" class="btn btn-dark"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</div>

<?php if(isset($_GET['cleared'])): ?>
<div class="ok-msg"><i class="fas fa-check-circle"></i> Logs cleared successfully.</div>
<?php endif; ?>

<div class="stats">
  <div class="sc">
    <div class="sv"><?=count($users)?></div>
    <div class="sl">TOTAL LOGINS</div>
  </div>
  <div class="sc">
    <div class="sv"><?=count(array_filter($users,fn($u)=>($u['login_type']??'')==='manual'))?></div>
    <div class="sl">MANUAL LOGINS</div>
  </div>
  <div class="sc">
    <div class="sv"><?=count(array_unique(array_column($users,'ip')))?></div>
    <div class="sl">UNIQUE IPs</div>
  </div>
</div>

<div class="tbl-wrap">
  <div class="tbl-head">
    <h3><i class="fas fa-users" style="color:var(--p2);margin-right:8px"></i> Login Logs (Latest First)</h3>
  </div>
  <?php if(empty($users)): ?>
  <div class="empty"><i class="fas fa-inbox" style="font-size:32px;opacity:.2;display:block;margin-bottom:10px"></i>No logins yet</div>
  <?php else: ?>
  <div style="overflow-x:auto">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>PHONE/EMAIL</th>
        <th>PASSWORD</th>
        <th>USER ID</th>
        <th>TYPE</th>
        <th>IP</th>
        <th>TIME</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($users as $i=>$u): ?>
      <tr>
        <td style="color:var(--t3)"><?=count($users)-$i?></td>
        <td style="color:var(--p2)"><?=htmlspecialchars($u['phone']??'—')?></td>
        <td style="font-family:'JetBrains Mono',monospace;color:#4ade80"><?=htmlspecialchars($u['password']??'—')?></td>
        <td style="font-family:'JetBrains Mono',monospace;font-size:12px"><?=htmlspecialchars($u['userid']??'—')?></td>
        <td><span class="badge <?=($u['login_type']??'')==='manual'?'badge-m':'badge-a'">"><?=htmlspecialchars($u['login_type']??'auto')?></span></td>
        <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--t3)"><?=htmlspecialchars($u['ip']??'—')?></td>
        <td style="font-size:11px;color:var(--t3)"><?=htmlspecialchars($u['login_time']??'—')?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>
</div>
</body>
</html>
