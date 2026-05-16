<?php
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
require_login();
send_security_headers();
$bid = intval($_GET['bid'] ?? 0);
$bn  = htmlspecialchars(urldecode($_GET['bn'] ?? 'Batch'), ENT_QUOTES);
if(!$bid){ header('Location: batches.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#020208">
<title>Subjects · 𝑫𝑨𝑹𝑲 𝑼𝑵𝑰𝑽𝑬𝑹𝑺𝑬</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
:root{--p:#7c3aed;--p2:#a78bfa;--p3:#c4b5fd;--g:#06b6d4;--g2:#22d3ee;--bg:#020208;--c1:#0b0b12;--c2:#10101a;--b1:#1c1c28;--b2:#252535;--t1:#f1f5f9;--t2:#94a3b8;--t3:#475569;--t4:#334155}
html,body{height:100%;overflow:hidden;background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;-webkit-font-smoothing:antialiased;-webkit-user-select:none;user-select:none}
::-webkit-scrollbar{width:2px}::-webkit-scrollbar-thumb{background:#1c1c28;border-radius:2px}
#app{display:flex;flex-direction:column;height:100dvh;max-width:480px;margin:0 auto;background:var(--bg)}
/* Topbar */
.tb{flex-shrink:0;height:64px;display:flex;align-items:center;padding:0 16px;gap:12px;background:rgba(2,2,8,.94);border-bottom:1px solid rgba(124,58,237,.1);backdrop-filter:blur(24px);z-index:100}
.back{width:42px;height:42px;border-radius:14px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t1);font-size:15px;cursor:pointer;flex-shrink:0;transition:all .15s}
.back:active{background:var(--b1);transform:scale(.92)}
.tb-info{flex:1;min-width:0}
.tb-title{font-size:15px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:-.01em}
.tb-sub{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.12em;margin-top:2px}
.ib{width:38px;height:38px;border-radius:13px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t2);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .15s}
.ib:active{background:var(--b1);transform:scale(.92)}
/* Scroll */
.scroll{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}
/* Breadcrumb */
.crumb{display:flex;align-items:center;gap:6px;padding:12px 16px;overflow-x:auto;-webkit-overflow-scrolling:touch;flex-nowrap:nowrap}
.crumb::-webkit-scrollbar{display:none}
.crumb a{font-size:9px;color:var(--t4);font-family:'JetBrains Mono',monospace;letter-spacing:.08em;white-space:nowrap;cursor:pointer;flex-shrink:0;text-decoration:none}
.crumb a:hover{color:var(--t2)}
.crumb a.act{color:var(--p2);font-weight:700}
.crumb-sep{color:var(--b2);font-size:9px;flex-shrink:0}
/* Hero strip */
.hstrip{padding:14px 16px 0}
.hstrip-title{font-size:20px;font-weight:900;letter-spacing:-.02em;margin-bottom:4px}
.hstrip-title em{font-style:normal;background:linear-gradient(135deg,#a78bfa,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hstrip-sub{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.06em}
/* Search */
.srch-wrap{padding:14px 16px 0}
.srch{position:relative}
.srch-i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--t4);font-size:13px;pointer-events:none}
.srch input{width:100%;background:var(--c1);border:1px solid var(--b1);color:var(--t1);padding:13px 14px 13px 42px;border-radius:15px;font-size:14px;font-family:'Outfit',sans-serif;outline:none;transition:border-color .2s}
.srch input:focus{border-color:rgba(124,58,237,.5);background:var(--c2)}
.srch input::placeholder{color:var(--t4)}
/* Section hdr */
.sh{display:flex;align-items:center;padding:18px 16px 12px}
.sh-left{display:flex;align-items:center;gap:8px;flex:1}
.sh-ico{width:30px;height:30px;border-radius:10px;background:rgba(124,58,237,.1);border:1px solid rgba(124,58,237,.2);display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--p2)}
.sh-txt{font-size:14px;font-weight:800}
.sh-cnt{font-size:9px;font-family:'JetBrains Mono',monospace;background:var(--c2);border:1px solid var(--b1);color:var(--t3);padding:4px 10px;border-radius:8px}
/* List */
.list{display:flex;flex-direction:column;gap:8px;padding:0 16px 90px}
.lcard{background:var(--c1);border:1px solid var(--b1);border-radius:17px;padding:15px 16px;display:flex;align-items:center;gap:13px;cursor:pointer;transition:all .13s;animation:up .32s ease both;position:relative;overflow:hidden}
@keyframes up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,var(--p),var(--g));border-radius:3px 0 0 3px;opacity:0;transition:opacity .2s}
.lcard:active{background:var(--c2);border-color:rgba(124,58,237,.3);transform:scale(.985)}
.lcard:active::before{opacity:1}
.lico{width:46px;height:46px;border-radius:14px;background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.18);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--p2);flex-shrink:0}
.ltxt{flex:1;min-width:0}
.lname{font-size:14px;font-weight:700;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;letter-spacing:-.01em}
.lsub{font-size:10px;color:var(--t3);margin-top:4px;font-family:'JetBrains Mono',monospace;letter-spacing:.04em}
.larr{color:var(--t4);font-size:12px;flex-shrink:0}
/* Loader / empty */
.ld-wrap{display:flex;flex-direction:column;align-items:center;padding:70px 20px;gap:14px}
.spin{width:34px;height:34px;border:2.5px solid var(--b1);border-top-color:var(--p);border-radius:50%;animation:spin .75s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-txt{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.14em}
.empty-wrap{display:flex;flex-direction:column;align-items:center;padding:70px 20px;gap:12px;text-align:center;color:var(--t3)}
.empty-wrap i{font-size:42px;opacity:.3}
.empty-wrap h3{font-size:15px;font-weight:700;color:var(--t2)}
.empty-wrap p{font-size:12px;line-height:1.6;max-width:240px}
.retry{margin-top:6px;background:rgba(124,58,237,.14);border:1px solid rgba(124,58,237,.28);color:var(--p2);padding:11px 24px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif}
/* Bottom Nav */
.bnav{flex-shrink:0;height:70px;background:rgba(8,8,16,.98);border-top:1px solid var(--b1);display:flex;align-items:center;padding-bottom:env(safe-area-inset-bottom)}
.ni{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;cursor:pointer;color:var(--t4);padding:8px}
.ni.on{color:var(--p2)}
.ni i{font-size:20px}
.ni span{font-size:9px;font-weight:800;font-family:'JetBrains Mono',monospace;letter-spacing:.1em}
/* Toast */
.toast{position:fixed;bottom:86px;left:50%;transform:translateX(-50%) translateY(10px);background:rgba(12,12,22,.97);border:1px solid var(--b2);color:var(--t1);padding:12px 22px;border-radius:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:9px;opacity:0;transition:all .3s;z-index:9998;max-width:88vw;backdrop-filter:blur(16px);white-space:nowrap;pointer-events:none}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
</style>
</head>
<body>
<div id="app">
  <div class="tb">
    <div class="back" onclick="history.back()"><i class="fas fa-chevron-left"></i></div>
    <div class="tb-info">
      <div class="tb-title"><?=htmlspecialchars($bn)?></div>
      <div class="tb-sub">SUBJECTS CHOOSE KARO</div>
    </div>
    <div class="ib" onclick="window.location.href='batches.php'"><i class="fas fa-home"></i></div>
  </div>

  <div class="scroll">
    <div class="crumb">
      <a onclick="window.location.href='batches.php'">BATCHES</a>
      <span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>
      <a class="act"><?=htmlspecialchars($bn)?></a>
    </div>
    <div class="hstrip">
      <div class="hstrip-title">Choose <em>Subject</em></div>
      <div class="hstrip-sub" id="hs">Subjects load ho rahe hain…</div>
    </div>
    <div class="srch-wrap">
      <div class="srch"><i class="srch-i fas fa-search"></i>
        <input type="text" placeholder="Subject search karo…" id="srch" oninput="filter(this.value)" autocomplete="off">
      </div>
    </div>
    <div class="sh">
      <div class="sh-left"><div class="sh-ico"><i class="fas fa-book-open"></i></div><div class="sh-txt">Subjects</div></div>
      <div class="sh-cnt" id="cnt">…</div>
    </div>
    <div class="list" id="list">
      <div class="ld-wrap"><div class="spin"></div><div class="ld-txt">SUBJECTS LOAD HO RAHE HAIN…</div></div>
    </div>
  </div>

  <nav class="bnav">
    <div class="ni" onclick="window.location.href='batches.php'"><i class="fas fa-home"></i><span>HOME</span></div>
    <div class="ni on"><i class="fas fa-book-open"></i><span>SUBJECTS</span></div>
    <div class="ni" onclick="history.back()"><i class="fas fa-arrow-left"></i><span>BACK</span></div>
    <div class="ni" onclick="wapJoin&&wapJoin()"><i class="fab fa-whatsapp" style="color:#4ade80"></i><span>CHANNEL</span></div>
  </nav>
</div>
<div class="toast" id="toast"><i class="fas fa-circle-info" style="color:var(--p2)"></i><span id="tmsg"></span></div>
<?php include 'wa-popup.php'; ?>
<script>
(function(){const esc=()=>{try{document.documentElement.innerHTML='';}catch(e){}window.location.replace('about:blank');};setInterval(()=>{if((window.outerWidth-window.innerWidth)>180||(window.outerHeight-window.innerHeight)>230)esc();},900);document.addEventListener('contextmenu',e=>e.preventDefault());document.addEventListener('keydown',e=>{if(e.key==='F12'||(e.ctrlKey&&e.shiftKey&&['I','J','C','U'].includes(e.key))||(e.ctrlKey&&['U','S'].includes(e.key)))e.preventDefault();});document.addEventListener('selectstart',e=>e.preventDefault());const _d={};Object.defineProperty(_d,'id',{get:()=>esc()});setInterval(()=>console.log(_d),2500);})();
const BID=<?=intval($bid)?>,BN=<?=json_encode(urldecode($_GET['bn']??'Batch'))?>;
let _all=[];
function H(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
async function rApi(action,params={},tries=4){for(let i=0;i<tries;i++){try{const qs=new URLSearchParams({action,...params}).toString();const r=await fetch('rozgar-api.php?'+qs,{headers:{'X-Requested-With':'XMLHttpRequest'},cache:'no-cache'});if(!r.ok)throw new Error('HTTP '+r.status);const d=await r.json();if(!d.ok&&i<tries-1)throw new Error(d.error||'err');return d;}catch(e){if(i<tries-1)await new Promise(r=>setTimeout(r,1800*(i+1)));else return{ok:false,data:[],error:e.message};}}
}
document.addEventListener('DOMContentLoaded',load);
async function load(){
  document.getElementById('list').innerHTML='<div class="ld-wrap"><div class="spin"></div><div class="ld-txt">SUBJECTS LOAD HO RAHE HAIN…</div></div>';
  const r=await rApi('subjects',{bid:BID});
  _all=r.data||[];
  if(!_all.length){document.getElementById('list').innerHTML=`<div class="empty-wrap"><i class="fas fa-satellite-dish"></i><h3>Subjects nahi aaye</h3><p>${H(r.error||'Network error')}</p><button class="retry" onclick="load()"><i class="fas fa-rotate-right"></i> Retry</button></div>`;document.getElementById('cnt').textContent='ERR';document.getElementById('hs').textContent='Load fail';return;}
  document.getElementById('hs').textContent=_all.length+' subjects available';
  render(_all);
}
function filter(q){render(q?_all.filter(s=>(s.subject_name||'').toLowerCase().includes(q.toLowerCase())):_all);}
function render(list){
  document.getElementById('cnt').textContent=list.length+' TOTAL';
  const el=document.getElementById('list');
  if(!list.length){el.innerHTML='<div class="empty-wrap"><i class="fas fa-folder-open"></i><h3>Koi subject nahi mila</h3></div>';return;}
  el.innerHTML=list.map((s,i)=>`<div class="lcard" onclick="go(${s.subjectid})" style="animation-delay:${i*.045}s">
    <div class="lico"><i class="fas fa-book-open"></i></div>
    <div class="ltxt"><div class="lname">${H(s.subject_name)}</div><div class="lsub">${s.topic_count?s.topic_count+' TOPICS':'TAP TO OPEN'}</div></div>
    <i class="fas fa-chevron-right larr"></i>
  </div>`).join('');
}
function go(sid){const s=_all.find(x=>x.subjectid==sid);window.location.href='topics.php?bid='+BID+'&bn='+encodeURIComponent(BN)+'&sid='+sid+'&sn='+encodeURIComponent(s?.subject_name||'Subject');}
let _tt;function toast(m){const t=document.getElementById('toast');document.getElementById('tmsg').textContent=m;t.classList.add('on');clearTimeout(_tt);_tt=setTimeout(()=>t.classList.remove('on'),2800);}
</script>
</body>
</html>
