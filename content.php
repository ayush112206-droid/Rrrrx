<?php
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
require_login();
send_security_headers();
$bid = intval($_GET['bid'] ?? 0);
$bn  = htmlspecialchars(urldecode($_GET['bn'] ?? 'Batch'), ENT_QUOTES);
$sid = intval($_GET['sid'] ?? 0);
$sn  = htmlspecialchars(urldecode($_GET['sn'] ?? 'Subject'), ENT_QUOTES);
$tid = intval($_GET['tid'] ?? 0);
$tn  = htmlspecialchars(urldecode($_GET['tn'] ?? 'Topic'), ENT_QUOTES);
if(!$bid||!$sid||!$tid){ header('Location: batches.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#020208">
<title>Content · 𝑫𝑨𝑹𝑲 𝑼𝑵𝑰𝑽𝑬𝑹𝑺𝑬</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
:root{--p:#7c3aed;--p2:#a78bfa;--g:#06b6d4;--g2:#22d3ee;--gold:#f59e0b;--red:#ef4444;--bg:#020208;--c1:#0b0b12;--c2:#10101a;--b1:#1c1c28;--b2:#252535;--t1:#f1f5f9;--t2:#94a3b8;--t3:#475569;--t4:#334155}
html,body{height:100%;overflow:hidden;background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;-webkit-font-smoothing:antialiased;-webkit-user-select:none;user-select:none}
::-webkit-scrollbar{width:2px}::-webkit-scrollbar-thumb{background:#1c1c28;border-radius:2px}
#app{display:flex;flex-direction:column;height:100dvh;max-width:480px;margin:0 auto;background:var(--bg)}
/* Topbar */
.tb{flex-shrink:0;height:64px;display:flex;align-items:center;padding:0 16px;gap:12px;background:rgba(2,2,8,.94);border-bottom:1px solid rgba(124,58,237,.1);backdrop-filter:blur(24px);z-index:100}
.back{width:42px;height:42px;border-radius:14px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t1);font-size:15px;cursor:pointer;flex-shrink:0;transition:all .15s}
.back:active{background:var(--b1);transform:scale(.92)}
.tb-info{flex:1;min-width:0}
.tb-title{font-size:14px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tb-sub{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.1em;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ib{width:38px;height:38px;border-radius:13px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t2);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .15s}
.ib:active{background:var(--b1);transform:scale(.92)}
/* Scroll */
.scroll{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}
/* Crumb */
.crumb{display:flex;align-items:center;gap:5px;padding:10px 16px;overflow-x:auto;background:rgba(124,58,237,.03);border-bottom:1px solid rgba(124,58,237,.07)}
.crumb::-webkit-scrollbar{display:none}
.crumb a{font-size:9px;color:var(--t4);font-family:'JetBrains Mono',monospace;letter-spacing:.06em;white-space:nowrap;cursor:pointer;flex-shrink:0}
.crumb a.act{color:var(--gold);font-weight:700}
.crumb-sep{color:var(--b2);font-size:9px;flex-shrink:0}
/* Tabs */
.tabs-area{padding:14px 16px 0}
.tabs{display:flex;gap:8px;margin-bottom:0}
.tab{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px 10px;border:1px solid var(--b1);background:var(--c1);border-radius:15px;color:var(--t3);font-size:13px;font-weight:800;cursor:pointer;transition:all .2s;font-family:'Outfit',sans-serif;position:relative}
.tab .tcnt{font-size:10px;background:var(--c2);border-radius:6px;padding:2px 8px;font-family:'JetBrains Mono',monospace}
.tab.on-v{background:rgba(124,58,237,.1);border-color:rgba(124,58,237,.35);color:var(--p2);box-shadow:0 0 20px rgba(124,58,237,.1)}
.tab.on-p{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.28);color:#f87171;box-shadow:0 0 20px rgba(239,68,68,.08)}
/* Content list */
.clist{padding:12px 16px 90px;display:flex;flex-direction:column;gap:9px}
/* Video card */
.vcard{background:var(--c1);border:1px solid var(--b1);border-radius:18px;padding:14px 15px;display:flex;gap:13px;cursor:pointer;transition:all .13s;animation:up .32s ease both;position:relative;overflow:hidden}
@keyframes up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.vcard:active{background:var(--c2);border-color:rgba(124,58,237,.3);transform:scale(.98)}
/* Left accent bar */
.vcard::before{content:'';position:absolute;left:0;top:14px;bottom:14px;width:3px;background:linear-gradient(to bottom,var(--p),var(--g));border-radius:0 3px 3px 0;opacity:.7}
.vcard.pdf-card::before{background:linear-gradient(to bottom,#ef4444,#f97316)}
.vthumb{width:52px;height:52px;border-radius:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:22px}
.vt-vid{background:rgba(124,58,237,.1);color:var(--p2);border:1px solid rgba(124,58,237,.2)}
.vt-pdf{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.18)}
.vinfo{flex:1;min-width:0}
.vtitle{font-size:13px;font-weight:700;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:7px;letter-spacing:-.005em}
.vchips{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
.chip{font-size:9px;padding:3px 8px;border-radius:6px;font-weight:800;font-family:'JetBrains Mono',monospace;display:inline-flex;align-items:center;gap:4px}
.c-v{background:rgba(124,58,237,.12);color:var(--p2);border:1px solid rgba(124,58,237,.22)}
.c-p{background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)}
.c-free{background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2)}
.c-n{background:var(--c2);color:var(--t3);border:1px solid var(--b1)}
.varr{color:var(--p2);font-size:18px;flex-shrink:0;align-self:center;transition:transform .15s}
.vcard:active .varr{transform:scale(.85)}
.parr{color:#f87171}
/* Loader/empty */
.ld-wrap{display:flex;flex-direction:column;align-items:center;padding:70px 20px;gap:14px}
.spin{width:34px;height:34px;border:2.5px solid var(--b1);border-top-color:var(--p);border-radius:50%;animation:spin .75s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-txt{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.14em}
.empty-wrap{display:flex;flex-direction:column;align-items:center;padding:70px 20px;gap:12px;text-align:center;color:var(--t3)}
.empty-wrap i{font-size:42px;opacity:.3}
.empty-wrap h3{font-size:15px;font-weight:700;color:var(--t2)}
.empty-wrap p{font-size:12px;line-height:1.6}
.retry{margin-top:6px;background:rgba(124,58,237,.14);border:1px solid rgba(124,58,237,.28);color:var(--p2);padding:11px 24px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif}
/* Bottom nav */
.bnav{flex-shrink:0;height:70px;background:rgba(8,8,16,.98);border-top:1px solid var(--b1);display:flex;align-items:center;padding-bottom:env(safe-area-inset-bottom)}
.ni{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;cursor:pointer;color:var(--t4);padding:8px}
.ni.on{color:var(--p2)}
.ni i{font-size:20px}
.ni span{font-size:9px;font-weight:800;font-family:'JetBrains Mono',monospace;letter-spacing:.1em}
/* Quality Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.78);z-index:5000;backdrop-filter:blur(10px);align-items:flex-end;justify-content:center}
.modal-bg.on{display:flex}
.msheet{width:100%;max-width:480px;background:linear-gradient(180deg,#0e0e1a,#080810);border:1px solid rgba(124,58,237,.2);border-bottom:none;border-radius:28px 28px 0 0;padding:12px 20px 48px;animation:slideUp .35s cubic-bezier(.32,1.2,.55,1)}
@keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.mhandle{width:40px;height:4px;background:var(--b2);border-radius:2px;margin:0 auto 18px}
.mtitle{font-size:17px;font-weight:900;margin-bottom:18px;display:flex;align-items:center;gap:10px}
.mtitle i{color:var(--p2)}
.mclose{margin-left:auto;background:var(--c2);border:1px solid var(--b1);color:var(--t2);width:34px;height:34px;border-radius:11px;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.qgrid{display:grid;grid-template-columns:1fr 1fr;gap:9px}
.qbtn{background:var(--c2);border:1px solid var(--b1);color:var(--t1);padding:15px 12px;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px}
.qbtn:active{transform:scale(.95);background:var(--b1)}
.qbtn.qauto{grid-column:1/-1;background:linear-gradient(135deg,rgba(124,58,237,.18),rgba(6,182,212,.1));border-color:rgba(124,58,237,.35);color:var(--p2);font-size:14px;padding:16px}
/* Loading overlay */
.load-ov{display:none;position:fixed;inset:0;background:rgba(2,2,8,.85);z-index:4000;align-items:center;justify-content:center;flex-direction:column;gap:14px;backdrop-filter:blur(8px)}
.load-ov.on{display:flex}
.lo-spin{width:44px;height:44px;border:3px solid rgba(124,58,237,.2);border-top-color:var(--p);border-radius:50%;animation:spin .7s linear infinite}
.lo-txt{font-size:12px;color:var(--p3);font-family:'JetBrains Mono',monospace;letter-spacing:.12em}
/* Toast */
.toast{position:fixed;bottom:86px;left:50%;transform:translateX(-50%) translateY(10px);background:rgba(12,12,22,.97);border:1px solid var(--b2);color:var(--t1);padding:12px 22px;border-radius:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:9px;opacity:0;transition:all .3s;z-index:3999;max-width:88vw;backdrop-filter:blur(16px);white-space:nowrap;pointer-events:none}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
</style>
</head>
<body>
<div id="app">
  <!-- Topbar -->
  <div class="tb">
    <div class="back" onclick="history.back()"><i class="fas fa-chevron-left"></i></div>
    <div class="tb-info">
      <div class="tb-title"><?=htmlspecialchars($tn)?></div>
      <div class="tb-sub"><?=htmlspecialchars($sn)?> · <?=htmlspecialchars($bn)?></div>
    </div>
    <div class="ib" onclick="window.location.href='batches.php'"><i class="fas fa-home"></i></div>
  </div>

  <!-- Scroll -->
  <div class="scroll" id="scr">
    <!-- Breadcrumb -->
    <div class="crumb">
      <a onclick="window.location.href='batches.php'">BATCHES</a>
      <span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>
      <a onclick="goSubjects()"><?=htmlspecialchars($bn)?></a>
      <span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>
      <a onclick="history.back()"><?=htmlspecialchars($sn)?></a>
      <span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>
      <a class="act"><?=htmlspecialchars($tn)?></a>
    </div>

    <!-- Tabs -->
    <div class="tabs-area">
      <div class="tabs">
        <div class="tab on-v" id="t-v" onclick="switchTab('v')">
          <i class="fas fa-play-circle"></i> Videos
          <span class="tcnt" id="vc">0</span>
        </div>
        <div class="tab" id="t-p" onclick="switchTab('p')">
          <i class="fas fa-file-pdf"></i> PDFs
          <span class="tcnt" id="pc">0</span>
        </div>
      </div>
    </div>

    <!-- Content list -->
    <div class="clist" id="clist">
      <div class="ld-wrap"><div class="spin"></div><div class="ld-txt">CONTENT LOAD HO RAHA HAI…</div></div>
    </div>
  </div>

  <!-- Bottom Nav -->
  <nav class="bnav">
    <div class="ni" onclick="window.location.href='batches.php'"><i class="fas fa-home"></i><span>HOME</span></div>
    <div class="ni on"><i class="fas fa-play-circle"></i><span>CONTENT</span></div>
    <div class="ni" onclick="history.back()"><i class="fas fa-arrow-left"></i><span>BACK</span></div>
    <div class="ni" onclick="wapJoin&&wapJoin()"><i class="fab fa-whatsapp" style="color:#4ade80"></i><span>CHANNEL</span></div>
  </nav>
</div>

<!-- Quality Picker Modal -->
<div class="modal-bg" id="qmod" onclick="if(event.target===this)closeMod()">
  <div class="msheet">
    <div class="mhandle"></div>
    <div class="mtitle"><i class="fas fa-sliders"></i> Video Quality<button class="mclose" onclick="closeMod()"><i class="fas fa-times"></i></button></div>
    <div class="qgrid">
      <button class="qbtn" onclick="playQ('1080p')"><i class="fas fa-crown" style="color:var(--gold)"></i> 1080p HD+</button>
      <button class="qbtn" onclick="playQ('720p')"><i class="fas fa-star" style="color:var(--p2)"></i> 720p HD</button>
      <button class="qbtn" onclick="playQ('480p')"><i class="fas fa-circle-half-stroke"></i> 480p SD</button>
      <button class="qbtn" onclick="playQ('360p')"><i class="fas fa-signal" style="font-size:11px"></i> 360p</button>
      <button class="qbtn" onclick="playQ('240p')"><i class="fas fa-battery-quarter" style="color:#64748b"></i> 240p Low</button>
      <button class="qbtn qauto" onclick="playQ('auto')"><i class="fas fa-wand-magic-sparkles"></i> Auto — Best Quality</button>
    </div>
  </div>
</div>

<!-- Loading Overlay -->
<div class="load-ov" id="lo">
  <div class="lo-spin"></div>
  <div class="lo-txt" id="lo-txt">LOADING…</div>
</div>

<!-- Toast -->
<div class="toast" id="toast"><i class="fas fa-circle-info" style="color:var(--p2)"></i><span id="tmsg"></span></div>

<?php include 'wa-popup.php'; ?>

<script>
(function(){const esc=()=>{try{document.documentElement.innerHTML='';}catch(e){}window.location.replace('about:blank');};setInterval(()=>{if((window.outerWidth-window.innerWidth)>180||(window.outerHeight-window.innerHeight)>230)esc();},900);document.addEventListener('contextmenu',e=>e.preventDefault());document.addEventListener('keydown',e=>{if(e.key==='F12'||(e.ctrlKey&&e.shiftKey&&['I','J','C','U'].includes(e.key))||(e.ctrlKey&&['U','S'].includes(e.key)))e.preventDefault();});document.addEventListener('selectstart',e=>e.preventDefault());const _d={};Object.defineProperty(_d,'id',{get:()=>esc()});setInterval(()=>console.log(_d),2500);})();

const BID=<?=intval($bid)?>,BN=<?=json_encode(urldecode($_GET['bn']??'Batch'))?>;
const SID=<?=intval($sid)?>,SN=<?=json_encode(urldecode($_GET['sn']??'Subject'))?>;
const TID=<?=intval($tid)?>,TN=<?=json_encode(urldecode($_GET['tn']??'Topic'))?>;

let _content=[],_tab='v',_pendVid=null;
function H(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

async function rApi(action,params={},tries=4){
  for(let i=0;i<tries;i++){
    try{
      const qs=new URLSearchParams({action,...params}).toString();
      const r=await fetch('rozgar-api.php?'+qs,{headers:{'X-Requested-With':'XMLHttpRequest'},cache:'no-cache'});
      if(!r.ok)throw new Error('HTTP '+r.status);
      const d=await r.json();
      if(!d.ok&&i<tries-1)throw new Error(d.error||'err');
      return d;
    }catch(e){
      if(i<tries-1)await new Promise(r=>setTimeout(r,1800*(i+1)));
      else return{ok:false,data:[],error:e.message};
    }
  }
}

document.addEventListener('DOMContentLoaded',load);

async function load(){
  document.getElementById('clist').innerHTML='<div class="ld-wrap"><div class="spin"></div><div class="ld-txt">CONTENT LOAD HO RAHA HAI…</div></div>';
  const r=await rApi('content',{bid:BID,sid:SID,tid:TID});
  _content=(r.data||[]).sort((a,b)=>(a.id||0)-(b.id||0));

  if(!_content.length){
    document.getElementById('clist').innerHTML=`<div class="empty-wrap"><i class="fas fa-satellite-dish"></i><h3>Content nahi mila</h3><p>${H(r.error||'Is topic mein abhi koi content nahi hai')}</p><button class="retry" onclick="load()"><i class="fas fa-rotate-right"></i> Retry</button></div>`;
    document.getElementById('vc').textContent='0';
    document.getElementById('pc').textContent='0';
    return;
  }

  const vids=_content.filter(i=>i._type==='video'||i._type==='both');
  const pdfs=_content.filter(i=>i._type==='pdf'||i._type==='both');
  document.getElementById('vc').textContent=vids.length;
  document.getElementById('pc').textContent=pdfs.length;
  switchTab('v');
}

function switchTab(t){
  _tab=t;
  const tv=document.getElementById('t-v'),tp=document.getElementById('t-p');
  tv.className='tab'+(t==='v'?' on-v':'');
  tp.className='tab'+(t==='p'?' on-p':'');
  renderTab(t);
  document.getElementById('scr').scrollTop=0;
}

function renderTab(t){
  const el=document.getElementById('clist');
  if(t==='v'){
    const list=_content.filter(i=>i._type==='video'||i._type==='both'||(!i._type&&!i._pdf_ref));
    if(!list.length){el.innerHTML='<div class="empty-wrap"><i class="fas fa-video-slash"></i><h3>Koi video nahi hai</h3><p>Is topic mein abhi video nahi hai</p></div>';return;}
    el.innerHTML=list.map((v,i)=>`
      <div class="vcard" onclick="askQ(${v.id})" style="animation-delay:${i*.04}s">
        <div class="vthumb vt-vid"><i class="fas fa-circle-play"></i></div>
        <div class="vinfo">
          <div class="vtitle">${H(v.Title||v.title||v.class_name||'Video Lecture')}</div>
          <div class="vchips">
            <span class="chip c-v"><i class="fas fa-play" style="font-size:8px"></i> VIDEO</span>
            ${v.is_free==1?'<span class="chip c-free">FREE</span>':''}
            <span class="chip c-n">#${i+1}</span>
          </div>
        </div>
        <i class="fas fa-play-circle varr"></i>
      </div>`).join('');
  } else {
    const list=_content.filter(i=>i._type==='pdf'||i._type==='both');
    if(!list.length){el.innerHTML='<div class="empty-wrap"><i class="fas fa-file-circle-xmark"></i><h3>Koi PDF nahi hai</h3><p>Is topic mein abhi PDF nahi hai</p></div>';return;}
    el.innerHTML=list.map((p,i)=>`
      <div class="vcard pdf-card" onclick="openPDF('${p._pdf_ref||''}')" style="animation-delay:${i*.04}s">
        <div class="vthumb vt-pdf"><i class="fas fa-file-pdf"></i></div>
        <div class="vinfo">
          <div class="vtitle">${H(p.Title||p.title||p.class_name||'PDF Notes')}</div>
          <div class="vchips">
            <span class="chip c-p"><i class="fas fa-file-pdf" style="font-size:8px"></i> PDF</span>
            <span class="chip c-n">#${i+1}</span>
          </div>
        </div>
        <i class="fas fa-download parr" style="font-size:16px;flex-shrink:0;align-self:center"></i>
      </div>`).join('');
  }
}

/* ── Video flow ── */
function askQ(vid){_pendVid=vid;document.getElementById('qmod').classList.add('on');}
function closeMod(){document.getElementById('qmod').classList.remove('on');}
async function playQ(q){
  closeMod();
  if(!_pendVid){toast('Video nahi chuna!');return;}
  showLoader('VIDEO LOAD HO RAHI HAI…');
  const r=await rApi('video',{vid:_pendVid,bid:BID,q:q});
  hideLoader();
  if(!r.ok||!r.url){toast('❌ Video nahi mili: '+(r.error||'Unknown error'));return;}
  window.open(r.url,'_blank');
}

/* ── PDF flow ── */
async function openPDF(enc){
  if(!enc){toast('PDF link nahi mila!');return;}
  showLoader('PDF LOAD HO RAHA HAI…');
  const r=await rApi('pdf',{l:enc});
  hideLoader();
  if(!r.ok||!r.url){toast('❌ PDF nahi mila: '+(r.error||'Unknown error'));return;}
  window.open(r.url,'_blank');
}

function showLoader(txt){const l=document.getElementById('lo');document.getElementById('lo-txt').textContent=txt||'LOADING…';l.classList.add('on');}
function hideLoader(){document.getElementById('lo').classList.remove('on');}
function goSubjects(){window.location.href='subjects.php?bid='+BID+'&bn='+encodeURIComponent(BN);}
let _tt;function toast(m){const t=document.getElementById('toast');document.getElementById('tmsg').textContent=m;t.classList.add('on');clearTimeout(_tt);_tt=setTimeout(()=>t.classList.remove('on'),3200);}
</script>
</body>
</html>
