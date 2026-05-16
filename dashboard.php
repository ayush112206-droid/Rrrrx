<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_login();
send_security_headers();
// Obfuscated nonce for JS
$nonce = bin2hex(random_bytes(16));
$_SESSION['nonce'] = $nonce;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?=APP_NAME?> · Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--p:#7c3aed;--p2:#a78bfa;--p3:#c4b5fd;--g:#06b6d4;--g2:#22d3ee;--gold:#f59e0b;--bg:#030307;--c1:#0d0d14;--c2:#12121c;--c3:#1a1a2e;--b1:#1e1e30;--b2:#2a2a40;--t1:#f8fafc;--t2:#94a3b8;--t3:#475569;--red:#ef4444;--green:#22c55e}
html,body{height:100%;overflow:hidden;background:var(--bg)}
body{color:var(--t1);font-family:'Outfit',sans-serif;-webkit-tap-highlight-color:transparent;-webkit-font-smoothing:antialiased}
::-webkit-scrollbar{width:3px;height:3px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#2a2a40;border-radius:2px}
/* ── App Shell ── */
#app{display:flex;flex-direction:column;height:100dvh;max-width:480px;margin:0 auto;position:relative;background:var(--bg)}
/* ── Top Bar ── */
.topbar{flex-shrink:0;height:58px;background:rgba(3,3,7,0.97);border-bottom:1px solid rgba(124,58,237,0.15);display:flex;align-items:center;padding:0 16px;gap:12px;z-index:100;backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
.logo-area{display:flex;align-items:center;gap:10px;flex:1;cursor:pointer}
.logo-icon{width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;flex-shrink:0;box-shadow:0 0 20px rgba(124,58,237,0.4)}
.logo-txt h1{font-size:15px;font-weight:800;color:#fff;line-height:1.1}
.logo-txt p{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.12em}
.hdr-right{display:flex;align-items:center;gap:8px}
.notif-btn{width:34px;height:34px;border-radius:10px;background:var(--c1);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t2);cursor:pointer;font-size:14px;position:relative;transition:background .15s}
.notif-btn:active{background:var(--c2)}
.avatar-ring{width:34px;height:34px;border-radius:50%;border:2px solid rgba(124,58,237,0.5);overflow:hidden;cursor:pointer;background:var(--c2);flex-shrink:0}
.avatar-ring img{width:100%;height:100%;object-fit:cover}
/* ── Back Bar ── */
.backbar{flex-shrink:0;height:52px;background:var(--c1);border-bottom:1px solid var(--b1);display:none;align-items:center;padding:0 12px;gap:10px;z-index:90}
.backbar.on{display:flex}
.back-btn{width:34px;height:34px;border-radius:10px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;font-size:13px;flex-shrink:0;transition:all .15s}
.back-btn:active{background:var(--b1);transform:scale(.95)}
.back-info{flex:1;min-width:0}
.back-ttl{font-size:14px;font-weight:700;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
.back-sub{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.08em}
/* ── Scroll ── */
.scroll{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch}
/* ── Bottom Nav ── */
.bottomnav{flex-shrink:0;height:64px;background:rgba(13,13,20,0.99);border-top:1px solid rgba(124,58,237,0.12);display:flex;align-items:center;z-index:100}
.nav-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:8px;cursor:pointer;color:var(--t3);transition:color .2s;-webkit-tap-highlight-color:transparent;position:relative}
.nav-item.act{color:var(--p2)}
.nav-item .ni{font-size:21px;transition:transform .2s}
.nav-item.act .ni{transform:scale(1.1)}
.nav-item .nl{font-size:9px;font-weight:700;letter-spacing:.1em}
.nav-dot{position:absolute;top:6px;right:calc(50% - 14px);width:6px;height:6px;border-radius:50%;background:var(--red);display:none}
/* ── Pages ── */
.pg{display:none;padding:16px 14px 28px}
.pg.on{display:block;animation:pgIn .2s ease}
@keyframes pgIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
/* ── Loader ── */
.ld{display:flex;flex-direction:column;align-items:center;padding:64px 20px;gap:14px}
.retry-btn{background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);color:var(--p2);padding:9px 22px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;margin-top:6px}
.ring{width:32px;height:32px;border:2px solid var(--b1);border-top-color:var(--p);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-txt{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.12em}
/* ── Hero ── */
.hero{padding:4px 0 20px}
.hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(124,58,237,0.1);border:1px solid rgba(124,58,237,0.25);border-radius:20px;padding:4px 12px;margin-bottom:12px}
.hero-badge span{font-size:10px;color:var(--p3);font-family:'JetBrains Mono',monospace;letter-spacing:.1em}
.hero-greet{font-size:12px;color:var(--t3);font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-bottom:2px}
.hero-name{font-size:26px;font-weight:900;line-height:1.1}
.hero-name b{background:linear-gradient(135deg,var(--p2),var(--g2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-sub{font-size:11px;color:var(--t3);margin-top:4px;font-family:'JetBrains Mono',monospace}
/* ── Stats ── */
.stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px}
.scard{background:var(--c1);border:1px solid var(--b1);border-radius:16px;padding:14px;display:flex;align-items:center;gap:12px;position:relative;overflow:hidden}
.scard::before{content:'';position:absolute;top:-20px;right:-20px;width:60px;height:60px;border-radius:50%;opacity:.05}
.scard.blue::before{background:var(--p);box-shadow:0 0 30px var(--p)}
.scard.teal::before{background:var(--g);box-shadow:0 0 30px var(--g)}
.si{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.si.blue{background:rgba(124,58,237,0.12);color:var(--p2);border:1px solid rgba(124,58,237,0.2)}
.si.teal{background:rgba(6,182,212,0.1);color:var(--g2);border:1px solid rgba(6,182,212,0.15)}
.sv{font-size:22px;font-weight:900;line-height:1}
.sl{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.07em;margin-top:2px}
/* ── Announce ── */
.ann{background:linear-gradient(135deg,rgba(124,58,237,0.1),rgba(6,182,212,0.05));border:1px solid rgba(124,58,237,0.2);border-radius:16px;padding:14px;margin-bottom:20px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:opacity .2s}
.ann:active{opacity:.7}
.ann-ico{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,rgba(124,58,237,.2),rgba(6,182,212,.1));border:1px solid rgba(124,58,237,.2);display:flex;align-items:center;justify-content:center;color:var(--p2);font-size:16px;flex-shrink:0}
.ann-txt h4{font-size:13px;font-weight:700}
.ann-txt p{font-size:10px;color:var(--t3);margin-top:2px;font-family:'JetBrains Mono',monospace}
.ann-chip{margin-left:auto;background:linear-gradient(135deg,var(--p),var(--g));color:#fff;font-size:10px;font-weight:700;padding:5px 11px;border-radius:8px;white-space:nowrap;flex-shrink:0}
/* ── Search ── */
.srch{position:relative;margin-bottom:20px}
.srch i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:13px}
.srch input{width:100%;background:var(--c1);border:1px solid var(--b1);color:#fff;padding:12px 14px 12px 40px;border-radius:14px;font-size:14px;font-family:'Outfit',sans-serif;outline:none;transition:border-color .2s}
.srch input:focus{border-color:rgba(124,58,237,.5);background:var(--c2)}
.srch input::placeholder{color:var(--t3)}
/* ── Section head ── */
.sh{display:flex;align-items:center;margin-bottom:14px}
.st{font-size:16px;font-weight:800;display:flex;align-items:center;gap:8px;color:var(--t1)}
.st i{color:var(--p2);font-size:14px}
.cnt-badge{margin-left:auto;font-size:9px;font-family:'JetBrains Mono',monospace;background:var(--c2);border:1px solid var(--b1);color:var(--t3);padding:3px 9px;border-radius:6px}
/* ── Batch Cards ── */
.bgrid{display:grid;grid-template-columns:1fr;gap:12px}
.bcard{background:var(--c1);border:1px solid var(--b1);border-radius:20px;overflow:hidden;cursor:pointer;transition:transform .12s,border-color .2s;position:relative}
.bcard:active{transform:scale(.97);border-color:rgba(124,58,237,.4)}
.bthumb{width:100%;aspect-ratio:16/7;position:relative;overflow:hidden}
.bthumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s}
.bcard:hover .bthumb img{transform:scale(1.03)}
.boverlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.92) 0%,rgba(0,0,0,.4) 45%,transparent 100%)}
.binfo{position:absolute;bottom:0;left:0;right:0;padding:14px}
.bname{font-size:14px;font-weight:700;color:#fff;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bmeta{display:flex;align-items:center;gap:6px;margin-top:7px}
.chip{font-size:9px;padding:3px 8px;border-radius:6px;font-weight:700;font-family:'JetBrains Mono',monospace}
.chip-live{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.25)}
.chip-price{background:rgba(124,58,237,.12);color:var(--p2);border:1px solid rgba(124,58,237,.25)}
.chip-free{background:rgba(6,182,212,.1);color:var(--g2);border:1px solid rgba(6,182,212,.2)}
.fav{position:absolute;top:10px;right:10px;width:34px;height:34px;border-radius:50%;background:rgba(0,0,0,.65);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .2s;backdrop-filter:blur(6px);z-index:5}
.fav.on{color:#f87171}
.fav:active{transform:scale(1.2)}
/* Study Button on card */
.study-btn{display:flex;align-items:center;justify-content:center;gap:7px;margin:0 14px 14px;background:linear-gradient(135deg,var(--p),#5b21b6);color:#fff;border:none;border-radius:12px;padding:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:opacity .15s;width:calc(100% - 28px)}
.study-btn:active{opacity:.8}
/* ── List Items ── */
.litems{display:flex;flex-direction:column;gap:8px}
.litem{background:var(--c1);border:1px solid var(--b1);border-radius:14px;padding:13px 15px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:all .15s}
.litem:active{background:var(--c2);border-color:rgba(124,58,237,.35)}
.lico{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.lico.s{background:rgba(124,58,237,.1);color:var(--p2);border:1px solid rgba(124,58,237,.2)}
.lico.t{background:rgba(6,182,212,.08);color:var(--g2);border:1px solid rgba(6,182,212,.15)}
.ltxt{flex:1;min-width:0}
.lname{font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lsub{font-size:10px;color:var(--t3);margin-top:2px;font-family:'JetBrains Mono',monospace}
.larr{color:var(--t3);font-size:12px;flex-shrink:0}
/* ── Content Tabs ── */
.ctabs{display:flex;gap:8px;margin-bottom:14px}
.ctab{flex:1;padding:10px;border:1px solid var(--b1);background:var(--c1);border-radius:12px;color:var(--t3);font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;gap:7px}
.ctab.on{background:rgba(124,58,237,.15);border-color:rgba(124,58,237,.4);color:var(--p2)}
/* ── Video Cards ── */
.vcard{background:var(--c1);border:1px solid var(--b1);border-radius:14px;padding:12px 14px;display:flex;gap:12px;cursor:pointer;transition:all .15s;margin-bottom:8px}
.vcard:active{background:var(--c2);border-color:rgba(124,58,237,.35)}
.vthumb{width:52px;height:52px;border-radius:11px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:22px}
.vthumb.v{background:rgba(124,58,237,.1);color:var(--p2);border:1px solid rgba(124,58,237,.2)}
.vthumb.p{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.18)}
.vinfo{flex:1;min-width:0}
.vtitle{font-size:13px;font-weight:600;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.vchips{display:flex;gap:5px;margin-top:6px}
.chip-v{background:rgba(124,58,237,.08);color:var(--p2);border:1px solid rgba(124,58,237,.18)}
.chip-p{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.18)}
/* ── Profile ── */
.prof-hero{background:var(--c1);border:1px solid var(--b1);border-radius:22px;padding:26px 20px;text-align:center;margin-bottom:16px;position:relative;overflow:hidden}
.prof-hero::before{content:'';position:absolute;top:-40px;left:50%;transform:translateX(-50%);width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.1) 0%,transparent 70%)}
.prof-av{width:78px;height:78px;border-radius:50%;margin:0 auto 14px;border:2px solid rgba(124,58,237,.4);overflow:hidden;background:var(--c2);position:relative;z-index:1}
.prof-av img{width:100%;height:100%}
.prof-name{font-size:21px;font-weight:800;position:relative;z-index:1}
.prof-uid{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;background:var(--c2);border:1px solid var(--b1);padding:4px 14px;border-radius:8px;display:inline-block;margin-top:8px}
.prof-ver{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;margin-top:8px;opacity:.5}
.alist{display:flex;flex-direction:column;gap:8px}
.aitem{background:var(--c1);border:1px solid var(--b1);border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:background .15s}
.aitem:active{background:var(--c2)}
.aico{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.aico.b{background:rgba(59,130,246,.1);color:#60a5fa;border:1px solid rgba(59,130,246,.2)}
.aico.g{background:rgba(34,197,94,.08);color:#4ade80;border:1px solid rgba(34,197,94,.15)}
.aico.r{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.15)}
.aico.p{background:rgba(124,58,237,.1);color:var(--p2);border:1px solid rgba(124,58,237,.2)}
.aico.y{background:rgba(245,158,11,.08);color:var(--gold);border:1px solid rgba(245,158,11,.15)}
.albl{font-size:14px;font-weight:600;flex:1}
.albl.r{color:#f87171}
.aarr{color:var(--t3);font-size:12px}
/* ── Empty ── */
.empty{text-align:center;padding:56px 20px;color:var(--t3)}
.empty i{font-size:44px;margin-bottom:14px;opacity:.25;display:block}
.empty p{font-size:13px}
/* ── Quality Modal ── */
.modal{position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.85);display:none;flex-direction:column;align-items:center;justify-content:flex-end;backdrop-filter:blur(4px)}
.modal.on{display:flex;animation:pgIn .2s ease}
.msheet{background:var(--c1);border:1px solid var(--b1);border-radius:24px 24px 0 0;width:100%;max-width:480px;padding:22px 18px 40px;max-height:78vh;overflow-y:auto}
.mhandle{width:36px;height:4px;background:var(--b2);border-radius:2px;margin:0 auto 20px}
.mtitle{font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.mclose{margin-left:auto;width:28px;height:28px;border-radius:8px;background:var(--c2);border:none;color:var(--t2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px}
.qgrid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.qbtn{background:var(--c2);border:1px solid var(--b1);border-radius:12px;padding:15px;text-align:center;font-weight:700;font-size:13px;color:var(--t2);cursor:pointer;transition:all .2s;font-family:'Outfit',sans-serif}
.qbtn:active{background:var(--p);color:#fff;border-color:var(--p);transform:scale(.96)}
.qbtn.auto{background:rgba(124,58,237,.1);border-color:rgba(124,58,237,.3);color:var(--p2)}
/* ── Toast ── */
.toast{position:fixed;top:68px;left:50%;transform:translateX(-50%) translateY(-8px);background:var(--c2);border:1px solid rgba(124,58,237,.3);color:#fff;padding:10px 20px;border-radius:50px;font-size:12px;font-weight:600;z-index:999;opacity:0;transition:all .25s;pointer-events:none;white-space:nowrap;display:flex;align-items:center;gap:8px;box-shadow:0 8px 40px rgba(0,0,0,.5)}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
/* WhatsApp popup styles are in wa-popup.php */
/* ── Welcome Popup ── */
.wpop{position:fixed;inset:0;z-index:400;display:flex;align-items:flex-end;padding:16px;background:rgba(0,0,0,.7);backdrop-filter:blur(6px)}
.wpop.hide{display:none}
.wbox{background:var(--c1);border:1px solid rgba(124,58,237,.25);border-radius:22px;width:100%;padding:28px 22px;animation:slideUp .35s cubic-bezier(.34,1.56,.64,1)}
@keyframes slideUp{from{transform:translateY(100px);opacity:0}to{transform:translateY(0);opacity:1}}
.w-logo{width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,var(--p),var(--g));display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;margin:0 auto 16px;box-shadow:0 8px 30px rgba(124,58,237,.35)}
.w-ttl{font-size:21px;font-weight:900;text-align:center;margin-bottom:6px}
.w-sub{font-size:13px;color:var(--t2);text-align:center;line-height:1.6;margin-bottom:6px}
.w-highlight{background:rgba(124,58,237,.1);border:1px solid rgba(124,58,237,.2);border-radius:12px;padding:12px 16px;margin:14px 0;text-align:left}
.w-highlight p{font-size:12px;color:var(--t2);line-height:1.7}
.w-highlight p i{color:var(--p2);margin-right:6px}
.w-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:16px}
.w-login{background:var(--c2);border:1px solid var(--b1);color:var(--t1);border-radius:12px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif}
.w-start{background:linear-gradient(135deg,var(--p),#5b21b6);color:#fff;border:none;border-radius:12px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px}
.w-start:active,.w-login:active{opacity:.85}
</style>
</head>
<body>

<!-- ════════════════ WELCOME POPUP ════════════════ -->
<div id="wpop" class="wpop">
  <div class="wbox">
    <div class="w-logo"><i class="fas fa-graduation-cap"></i></div>
    <div class="w-ttl"><?=APP_NAME?></div>
    <div class="w-sub">ROZGAR with Ankit ki Official Learning Platform</div>
    <div class="w-highlight">
      <p><i class="fas fa-check-circle"></i> Apni Rozgar ID se login karo</p>
      <p><i class="fas fa-check-circle"></i> Saare Batches Lifetime Access</p>
      <p><i class="fas fa-check-circle"></i> Videos, Notes & More</p>
    </div>
    <p style="font-size:11px;color:var(--t3);text-align:center">rozgarlearning.com ki ID &amp; Password se login karo ya Skip karo</p>
    <div class="w-btns">
      <button class="w-login" onclick="goToLogin()"><i class="fas fa-sign-in-alt" style="margin-right:6px"></i>Login</button>
      <button class="w-start" onclick="closeWelcome()"><i class="fas fa-rocket"></i>Start Now</button>
    </div>
  </div>
</div>

<!-- ════════════════ WHATSAPP POPUP (via wa-popup.php) ════════════════ -->

<!-- ════════════════ APP SHELL ════════════════ -->
<div id="app">
  <!-- Topbar -->
  <div class="topbar">
    <div class="logo-area" onclick="goHome()">
      <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
      <div class="logo-txt">
        <h1><?=APP_NAME?></h1>
        <p>OFFICIAL PORTAL · SECURE</p>
      </div>
    </div>
    <div class="hdr-right">
      <div class="notif-btn" onclick="navClick('saved')" title="Saved"><i class="far fa-heart"></i></div>
      <div class="avatar-ring" onclick="navClick('profile')"><img id="hdr-av" src="" alt=""></div>
    </div>
  </div>

  <!-- Back Bar -->
  <div class="backbar" id="backbar">
    <button class="back-btn" onclick="goBack()"><i class="fas fa-chevron-left"></i></button>
    <div class="back-info">
      <div class="back-ttl" id="bk-ttl">—</div>
      <div class="back-sub" id="bk-sub"></div>
    </div>
  </div>

  <!-- Scroll -->
  <div class="scroll" id="scr">

    <!-- HOME -->
    <div class="pg on" id="pg-home">
      <div class="hero">
        <div class="hero-badge"><span>● LIVE</span></div>
        <div class="hero-greet">Welcome Back</div>
        <div class="hero-name">Hey, <b id="g-name">Student</b> 👋</div>
        <div class="hero-sub" id="g-uid">Rozgar Learning Portal</div>
      </div>
      <div class="stats">
        <div class="scard blue">
          <div class="si blue"><i class="fas fa-layer-group"></i></div>
          <div><div class="sv" id="st-batch">—</div><div class="sl">BATCHES</div></div>
        </div>
        <div class="scard teal">
          <div class="si teal"><i class="fas fa-heart"></i></div>
          <div><div class="sv" id="st-saved">0</div><div class="sl">SAVED</div></div>
        </div>
      </div>
      <div class="ann" onclick="joinWA()" style="border-color:rgba(37,211,102,.25)">
        <div class="ann-ico" style="background:rgba(37,211,102,.12);color:#4ade80;border-color:rgba(37,211,102,.2)"><i class="fab fa-whatsapp"></i></div>
        <div class="ann-txt">
          <h4>Rozgar with Ankit</h4>
          <p>WHATSAPP CHANNEL · FREE MATERIAL</p>
        </div>
        <div class="ann-chip" style="background:rgba(37,211,102,.15);color:#4ade80;border-color:rgba(37,211,102,.3)">JOIN</div>
      </div>
      <div class="srch">
        <i class="fas fa-magnifying-glass"></i>
        <input type="text" id="srch-in" placeholder="Search batches..." oninput="filterB(this.value)">
      </div>
      <div class="sh">
        <div class="st"><i class="fas fa-layer-group"></i> My Batches</div>
        <div class="cnt-badge" id="b-cnt">LOADING</div>
      </div>
      <div id="batch-list" class="bgrid">
        <div class="ld"><div class="ring"></div><div class="ld-txt">FETCHING BATCHES...</div></div>
      </div>
    </div>

    <!-- SUBJECTS -->
    <div class="pg" id="pg-subj">
      <div id="subj-list" class="litems">
        <div class="ld"><div class="ring"></div><div class="ld-txt">LOADING SUBJECTS...</div></div>
      </div>
    </div>

    <!-- TOPICS -->
    <div class="pg" id="pg-topic">
      <div id="topic-list" class="litems">
        <div class="ld"><div class="ring"></div><div class="ld-txt">LOADING TOPICS...</div></div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="pg" id="pg-content">
      <div class="ctabs">
        <button class="ctab on" id="ct-v" onclick="switchTab('v')"><i class="fas fa-play"></i> Videos</button>
        <button class="ctab" id="ct-p" onclick="switchTab('p')"><i class="fas fa-file-pdf"></i> Notes</button>
      </div>
      <div id="content-list"></div>
    </div>

    <!-- SAVED -->
    <div class="pg" id="pg-saved">
      <div class="sh"><div class="st"><i class="fas fa-heart" style="color:#f87171"></i> Saved Batches</div></div>
      <div id="saved-list" class="bgrid"></div>
    </div>

    <!-- PROFILE -->
    <div class="pg" id="pg-profile">
      <div class="prof-hero">
        <div class="prof-av"><img id="p-av" src="" alt=""></div>
        <div class="prof-name" id="p-name">Student</div>
        <div class="prof-uid">Rozgar Learning</div>
        <div class="prof-ver"><?=APP_NAME?> v<?=APP_VERSION?> · SECURE PORTAL</div>
      </div>
      <div class="alist">
        <div class="aitem" onclick="navClick('home')">
          <div class="aico b"><i class="fas fa-home"></i></div>
          <span class="albl">My Batches</span>
          <i class="fas fa-chevron-right aarr"></i>
        </div>
        <div class="aitem" onclick="navClick('saved')">
          <div class="aico g"><i class="fas fa-heart"></i></div>
          <span class="albl">Saved Batches</span>
          <i class="fas fa-chevron-right aarr"></i>
        </div>
        <div class="aitem" onclick="joinWA()">
          <div class="aico" style="background:rgba(37,211,102,.1);color:#4ade80;border:1px solid rgba(37,211,102,.2)"><i class="fab fa-whatsapp"></i></div>
          <span class="albl">Rozgar WhatsApp Channel</span>
          <i class="fas fa-chevron-right aarr"></i>
        </div>
        <div class="aitem" onclick="goToLogin()">
          <div class="aico y"><i class="fas fa-user-lock"></i></div>
          <span class="albl">Login / Switch Account</span>
          <i class="fas fa-chevron-right aarr"></i>
        </div>
        <div class="aitem" onclick="showAbout()">
          <div class="aico p"><i class="fas fa-info-circle"></i></div>
          <span class="albl">About</span>
          <i class="fas fa-chevron-right aarr"></i>
        </div>
      </div>
    </div>

  </div><!-- /scroll -->

  <!-- Bottom Nav -->
  <nav class="bottomnav">
    <div class="nav-item act" id="ni-home" onclick="navClick('home')">
      <i class="fas fa-house-chimney ni"></i><span class="nl">HOME</span>
    </div>
    <div class="nav-item" id="ni-saved" onclick="navClick('saved')">
      <i class="fas fa-heart ni"></i><span class="nl">SAVED</span>
    </div>
    <div class="nav-item" id="ni-profile" onclick="navClick('profile')">
      <i class="fas fa-user ni"></i><span class="nl">PROFILE</span>
    </div>
  </nav>
</div>

<!-- Quality Modal -->
<div class="modal" id="qmodal">
  <div class="msheet">
    <div class="mhandle"></div>
    <div class="mtitle">
      <i class="fas fa-sliders" style="color:var(--p2)"></i> Quality Select
      <button class="mclose" onclick="closeMod('qmodal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="qgrid">
      <button class="qbtn" onclick="playQ('1080p')"><i class="fas fa-crown" style="color:var(--gold)"></i> 1080p HD</button>
      <button class="qbtn" onclick="playQ('720p')"><i class="fas fa-star" style="color:var(--p2)"></i> 720p HD</button>
      <button class="qbtn" onclick="playQ('480p')">480p SD</button>
      <button class="qbtn" onclick="playQ('360p')">360p</button>
      <button class="qbtn" onclick="playQ('240p')">240p Low</button>
      <button class="qbtn auto" onclick="playQ('auto')"><i class="fas fa-wand-magic-sparkles"></i> Auto Best</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"><i class="fas fa-bolt" style="color:var(--p2)"></i><span id="toast-msg"></span></div>

<script>
// ── Security Layer ────────────────────────────────────────────────
(function(){
  'use strict';
  // Block DevTools — redirect to about:blank
  const _esc=()=>{try{document.documentElement.innerHTML='';}catch(e){}window.location.replace('about:blank');};
  const _sz=()=>{if((window.outerWidth-window.innerWidth)>170||(window.outerHeight-window.innerHeight)>220)_esc();};
  setInterval(_sz,800);_sz();
  // Console object getter trick
  const _co={};Object.defineProperty(_co,'id',{get:()=>_esc()});
  setInterval(()=>{console.log(_co);},2000);
  // Disable right-click
  document.addEventListener('contextmenu',e=>e.preventDefault());
  // Disable common shortcuts
  document.addEventListener('keydown',e=>{
    if(e.key==='F12'||(e.ctrlKey&&e.shiftKey&&['I','J','C','U'].includes(e.key))||(e.ctrlKey&&e.key==='U')||(e.ctrlKey&&e.key==='S')){
      e.preventDefault();e.stopPropagation();return false;
    }
  });
  // Disable select/copy on sensitive areas
  document.addEventListener('selectstart',e=>e.preventDefault());
  document.addEventListener('dragstart',e=>e.preventDefault());
  // Console warning
  console.log('%c⛔ STOP!','font-size:50px;font-weight:bold;color:red');
  console.log('%cThis browser feature is for developers only. Do not paste any code here.','font-size:14px;color:red');
})();

// ── API (obfuscated, server-side proxy) ─────────────────────────
function _q(ep){return btoa(ep).replace(/\+/g,'-').replace(/\//g,'_').replace(/=/g,'');}

// ── Custom Rozgar API Client ──────────────────────────────────────
// Uses rozgar-api.php (our own PHP server) — no sensitive data in browser
async function rApi(action,params={},retries=4){
  for(let i=0;i<retries;i++){
    try{
      const qs=new URLSearchParams({action,...params}).toString();
      const r=await fetch('rozgar-api.php?'+qs,{
        headers:{'X-Requested-With':'XMLHttpRequest'},
        cache:'no-cache'
      });
      if(!r.ok)throw new Error('HTTP '+r.status);
      const d=await r.json();
      if(!d.ok&&d.error==='upstream_429'&&i<retries-1){
        await new Promise(r=>setTimeout(r,3000*(i+1)));continue;
      }
      return d;
    }catch(e){
      if(i<retries-1){await new Promise(r=>setTimeout(r,2000*(i+1)));}
      else{return{ok:false,data:[],error:e.message,_err:e.message};}
    }
  }
}

async function api(ep,retries=4){
  for(let i=0;i<retries;i++){
    try{
      const fd=new FormData();fd.append('q',_q(ep));
      const r=await fetch('api.php',{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}});
      if(!r.ok)throw new Error('HTTP '+r.status);
      const d=await r.json();
      // upstream_429: server got rate-limited but has no cache yet — wait and retry
      if(d.retry&&d.e==='upstream_429'){
        if(i<retries-1){await new Promise(r=>setTimeout(r,3000*(i+1)));continue;}
        return{data:[],_err:'Rate limited by upstream. Try again in a moment.'};
      }
      if(d.e&&d.e!=='upstream_429'&&i<retries-1)throw new Error(d.e);
      return d;
    }catch(e){
      if(i<retries-1){await new Promise(r=>setTimeout(r,2000*(i+1)));}
      else{return{data:[],_err:e.message};}
    }
  }
}

// AES decryption moved server-side (play.php / pdf.php)

// ── State ────────────────────────────────────────────────────────
const S={
  batches:[],subjects:[],topics:[],content:[],
  saved:JSON.parse(localStorage.getItem('rz_sv')||'[]'),
  cb:{},cs:{},ct2:{},tmpV:null,
  hist:['home']
};

// ── Init ─────────────────────────────────────────────────────────
window.addEventListener('DOMContentLoaded',()=>{
  const av='https://api.dicebear.com/7.x/bottts-neutral/svg?seed=rozgar&baseColor=7c3aed';
  document.getElementById('hdr-av').src=av;
  document.getElementById('p-av').src=av;
  document.getElementById('p-name').textContent='Rozgar Student';
  document.getElementById('g-name').textContent='Student';
  document.getElementById('g-uid').textContent='Rozgar with Ankit · Official Portal';
  updSaved();
  fetchBatches();
  // Show welcome popup after 300ms
  setTimeout(()=>{
    const seen=sessionStorage.getItem('rz_welcome');
    if(!seen){document.getElementById('wpop').style.display='flex';sessionStorage.setItem('rz_welcome','1');}
    // WhatsApp popup handled by wa-popup.php
  },300);
});

// ── Welcome Popup ────────────────────────────────────────────────
function closeWelcome(){
  document.getElementById('wpop').style.display='none';
  // WhatsApp popup is shown by wa-popup.php automatically
}
function goToLogin(){
  document.getElementById('wpop').style.display='none';
  window.location.href='login.php';
}

// ── WhatsApp Popup — handled by wa-popup.php (included before </body>) ──
function showWA(){ /* auto-shown by wa-popup.php after 1.5s */ }
function closeWA(){ if(typeof waClose==='function') waClose(); }
function joinTG(){ joinWA(); }
function joinWA(){
  window.open('<?=WA_CHANNEL?>','_blank');
  closeWA();
}

// ── Fetch Batches ────────────────────────────────────────────────
async function fetchBatches(){
  document.getElementById('batch-list').innerHTML='<div class="ld"><div class="ring"></div><div class="ld-txt">FETCHING BATCHES...</div></div>';
  const r=await rApi('batches');
  S.batches=r.data||[];
  if(!S.batches.length&&r._err){
    document.getElementById('batch-list').innerHTML='<div class="empty"><i class="fas fa-wifi-slash"></i><p>Network error. Please retry.</p><button class="retry-btn" onclick="fetchBatches()"><i class="fas fa-redo"></i> Retry</button></div>';
    document.getElementById('st-batch').textContent='0';
    document.getElementById('b-cnt').textContent='ERROR';
    return;
  }
  document.getElementById('st-batch').textContent=S.batches.length;
  document.getElementById('b-cnt').textContent=S.batches.length+' TOTAL';
  renderBatches(S.batches,'batch-list');
}

function filterB(q){
  const f=S.batches.filter(b=>(b.course_name||'').toLowerCase().includes(q.toLowerCase()));
  renderBatches(f,'batch-list');
}

function renderBatches(list,cid){
  const el=document.getElementById(cid);if(!el)return;
  if(!list.length){el.innerHTML='<div class="empty"><i class="fas fa-box-open"></i><p>No batches found</p></div>';return;}
  el.innerHTML=list.map(b=>{
    const fv=S.saved.includes(String(b.id));
    const th=b.course_thumbnail||'https://placehold.co/400x175/0d0d14/7c3aed?text=Batch';
    const pr=b.price?'₹'+b.price:'FREE';
    return`<div class="bcard">
      <div class="bthumb">
        <img src="${H(th)}" loading="lazy" onerror="this.src='https://placehold.co/400x175/0d0d14/7c3aed?text=No+Image'">
        <div class="boverlay"></div>
        <button class="fav${fv?' on':''}" onclick="event.stopPropagation();toggleSave('${b.id}')" id="fv-${b.id}">
          <i class="${fv?'fas':'far'} fa-heart"></i>
        </button>
        <div class="binfo">
          <div class="bname">${H(b.course_name)}</div>
          <div class="bmeta">
            <span class="chip chip-live">LIVE</span>
            <span class="chip ${b.price?'chip-price':'chip-free'}">${H(pr)}</span>
          </div>
        </div>
      </div>
      <button class="study-btn" onclick="event.stopPropagation();goBatch(${b.id})">
        <i class="fas fa-play-circle"></i> Let's Study
      </button>
    </div>`;
  }).join('');
}

// ── Batch → Subjects ─────────────────────────────────────────────
function goBatch(id){
  const b=S.batches.find(x=>x.id==id||String(x.id)===String(id));
  const name=b?b.course_name:'Batch';
  window.location.href='subjects.php?bid='+id+'&bn='+encodeURIComponent(name);
}

async function openBatch(id,name){
  S.cb={id,name};S.hist.push('subj');
  showPg('subj');setBK(name,'SELECT SUBJECT');
  document.getElementById('subj-list').innerHTML='<div class="ld"><div class="ring"></div><div class="ld-txt">LOADING SUBJECTS...</div></div>';
  const r=await rApi('subjects',{bid:id});
  S.subjects=r.data||[];
  renderList(S.subjects,'subj-list','s');
}

// ── Subject → Topics ─────────────────────────────────────────────
async function openSubject(sid,sname){
  S.cs={id:sid,name:sname};S.hist.push('topic');
  showPg('topic');setBK(sname,'TOPIC · '+S.cb.name);
  document.getElementById('topic-list').innerHTML='<div class="ld"><div class="ring"></div><div class="ld-txt">LOADING TOPICS...</div></div>';
  const r=await rApi('topics',{bid:S.cb.id,sid:sid});
  S.topics=(r.data||[]).sort((a,b)=>a.topicid-b.topicid);
  renderList(S.topics,'topic-list','t');
}

// ── Topic → Content ──────────────────────────────────────────────
async function openTopic(tid,tname){
  S.ct2={id:tid,name:tname};S.hist.push('content');
  showPg('content');setBK(tname,S.cs.name+' · '+S.cb.name);
  document.getElementById('content-list').innerHTML='<div class="ld"><div class="ring"></div><div class="ld-txt">LOADING CONTENT...</div></div>';
  const r=await rApi('content',{bid:S.cb.id,sid:S.cs.id,tid:tid});
  S.content=(r.data||[]).sort((a,b)=>a.id-b.id);
  switchTab('v');
}

function renderList(list,eid,type){
  const el=document.getElementById(eid);
  if(!list.length){el.innerHTML='<div class="empty"><i class="fas fa-folder-open"></i><p>Nothing here</p></div>';return;}
  const isS=type==='s';
  el.innerHTML=list.map((item,i)=>{
    const id=isS?item.subjectid:item.topicid;
    const nm=isS?item.subject_name:item.topic_name;
    const cnt=item.topic_count||item.content_count||'';
    const fn=isS?`openSubject('${id}','${J(nm)}')`:`openTopic('${id}','${J(nm)}')`;
    return`<div class="litem" onclick="${fn}" style="animation-delay:${i*.04}s">
      <div class="lico ${isS?'s':'t'}"><i class="fas fa-${isS?'book-open':'lightbulb'}"></i></div>
      <div class="ltxt">
        <div class="lname">${H(nm)}</div>
        <div class="lsub">${cnt?cnt+' items':'TAP TO OPEN'}</div>
      </div>
      <i class="fas fa-chevron-right larr"></i>
    </div>`;
  }).join('');
}

function switchTab(t){
  document.getElementById('ct-v').classList.toggle('on',t==='v');
  document.getElementById('ct-p').classList.toggle('on',t==='p');
  const el=document.getElementById('content-list');
  if(!S.content.length){el.innerHTML='<div class="empty"><i class="fas fa-folder-open"></i><p>No content found</p></div>';return;}
  if(t==='v'){
    const vids=S.content.filter(i=>i.id);
    if(!vids.length){el.innerHTML='<div class="empty"><i class="fas fa-video-slash"></i><p>No videos</p></div>';return;}
    el.innerHTML=vids.map((v,i)=>`<div class="vcard" onclick="askQ('${v.id}')" style="animation-delay:${i*.04}s">
      <div class="vthumb v"><i class="fas fa-circle-play"></i></div>
      <div class="vinfo">
        <div class="vtitle">${H(v.Title||v.title||'Untitled Video')}</div>
        <div class="vchips"><span class="chip chip-v"><i class="fas fa-play"></i> VIDEO</span>${v.is_free==1?'<span class="chip chip-free">FREE</span>':''}</div>
      </div>
    </div>`).join('');
  }else{
    const pdfs=S.content.filter(i=>i.pdf_link||i.pdf_link2);
    if(!pdfs.length){el.innerHTML='<div class="empty"><i class="fas fa-file-circle-xmark"></i><p>No PDFs</p></div>';return;}
    el.innerHTML=pdfs.map((p,i)=>{
      const raw=p.pdf_link||p.pdf_link2||'';
      const isPlain=raw.startsWith('http');
      const link=encodeURIComponent(raw);
      return`<div class="vcard" onclick="openPDF('${link}','${isPlain?'plain':'enc'}')" style="animation-delay:${i*.04}s">
        <div class="vthumb p"><i class="fas fa-file-pdf"></i></div>
        <div class="vinfo">
          <div class="vtitle">${H(p.Title||p.title||'PDF Notes')}</div>
          <div class="vchips"><span class="chip chip-p"><i class="fas fa-file-pdf"></i> PDF</span></div>
        </div>
      </div>`;
    }).join('');
  }
}

// ── Video playback ───────────────────────────────────────────────
function askQ(id){S.tmpV=id;openMod('qmodal');}

async function playQ(q){
  closeMod('qmodal');
  if(!S.tmpV||!S.cb.id){toast('Video nahi mila!');return;}
  toast('Video load ho rahi hai...');
  const r=await rApi('video',{vid:S.tmpV,bid:S.cb.id,q:q});
  if(!r.ok||!r.url){toast('Error: '+(r.error||'Video nahi mili'));return;}
  window.open(r.url,'_blank');
}

async function openPDF(enc,type){
  const raw=decodeURIComponent(enc);
  toast('PDF load ho raha hai...');
  const r=await rApi('pdf',{l:enc});
  if(!r.ok||!r.url){toast('PDF nahi mila: '+(r.error||'Unknown error'));return;}
  window.open(r.url,'_blank');
}

// ── Saved ────────────────────────────────────────────────────────
function toggleSave(id){
  const sid=String(id);
  const btn=document.getElementById('fv-'+id);
  if(S.saved.includes(sid)){
    S.saved=S.saved.filter(x=>x!==sid);
    if(btn){btn.classList.remove('on');btn.innerHTML='<i class="far fa-heart"></i>';}
    toast('Removed from saved');
  }else{
    S.saved.push(sid);
    if(btn){btn.classList.add('on');btn.innerHTML='<i class="fas fa-heart"></i>';}
    toast('Saved! ❤️');
  }
  localStorage.setItem('rz_sv',JSON.stringify(S.saved));
  updSaved();
}
function updSaved(){document.getElementById('st-saved').textContent=S.saved.length;}
function renderSaved(){
  const list=S.batches.filter(b=>S.saved.includes(String(b.id)));
  const el=document.getElementById('saved-list');
  if(!list.length){el.innerHTML='<div class="empty"><i class="fas fa-heart-crack"></i><p>No saved batches yet</p></div>';return;}
  renderBatches(list,'saved-list');
}

// ── Navigation ───────────────────────────────────────────────────
function showPg(name){
  document.querySelectorAll('.pg').forEach(p=>p.classList.remove('on'));
  const pg=document.getElementById('pg-'+name);
  if(pg){pg.classList.add('on');document.getElementById('scr').scrollTop=0;}
  const main=['home','saved','profile'].includes(name);
  document.getElementById('backbar').classList.toggle('on',!main);
}
function setBK(t,s){
  document.getElementById('bk-ttl').textContent=t;
  document.getElementById('bk-sub').textContent=s||'';
}
function goBack(){
  S.hist.pop();const prev=S.hist[S.hist.length-1]||'home';
  showPg(prev);
  if(prev==='subj')setBK(S.cb.name,'SELECT SUBJECT');
  else if(prev==='topic')setBK(S.cs.name,'TOPIC · '+S.cb.name);
  else document.getElementById('backbar').classList.remove('on');
}
function goHome(){
  S.hist=['home'];showPg('home');
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('act'));
  document.getElementById('ni-home').classList.add('act');
}
function navClick(name){
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('act'));
  const el=document.getElementById('ni-'+name);if(el)el.classList.add('act');
  S.hist=[name];showPg(name);
  if(name==='saved')renderSaved();
}

// ── Modal ────────────────────────────────────────────────────────
function openMod(id){document.getElementById(id).classList.add('on');}
function closeMod(id){document.getElementById(id).classList.remove('on');}

// ── Toast ────────────────────────────────────────────────────────
let _tt;
function toast(msg){
  const t=document.getElementById('toast');
  document.getElementById('toast-msg').textContent=msg;
  t.classList.add('on');clearTimeout(_tt);
  _tt=setTimeout(()=>t.classList.remove('on'),2800);
}

// ── About ────────────────────────────────────────────────────────
function showAbout(){
  toast('<?=APP_NAME?> v<?=APP_VERSION?> · Secure Portal');
}

// ── Utils ────────────────────────────────────────────────────────
function H(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function J(s){return String(s||'').replace(/'/g,"\\'").replace(/\\/g,'\\\\');}
</script>
<?php include 'wa-popup.php'; ?>
</body>
</html>
