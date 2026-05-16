<?php
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
require_login();
send_security_headers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#020208">
<title>𝑫𝑨𝑹𝑲 𝑼𝑵𝑰𝑽𝑬𝑹𝑺𝑬</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
:root{
  --p:#7c3aed;--p2:#a78bfa;--p3:#c4b5fd;
  --g:#06b6d4;--g2:#22d3ee;
  --gold:#f59e0b;--gold2:#fbbf24;
  --red:#ef4444;--green:#22c55e;
  --bg:#020208;--c1:#0b0b12;--c2:#10101a;--c3:#16161f;
  --b1:#1c1c28;--b2:#252535;--b3:#2e2e42;
  --t1:#f1f5f9;--t2:#94a3b8;--t3:#475569;--t4:#334155;
  --r:16px;--r2:20px;--r3:24px;
}
html,body{height:100%;overflow:hidden;background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;-webkit-font-smoothing:antialiased}
body{-webkit-user-select:none;user-select:none}
::-webkit-scrollbar{width:2px}::-webkit-scrollbar-thumb{background:#1c1c28;border-radius:2px}

/* ── App Shell ── */
#app{display:flex;flex-direction:column;height:100dvh;max-width:480px;margin:0 auto;background:var(--bg);position:relative;overflow:hidden}

/* ── Status glow ── */
#app::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:200px;height:1px;background:linear-gradient(90deg,transparent,rgba(124,58,237,.6),transparent);z-index:200}

/* ── Topbar ── */
.tb{flex-shrink:0;height:64px;display:flex;align-items:center;padding:0 18px;gap:13px;background:rgba(2,2,8,.92);border-bottom:1px solid rgba(124,58,237,.12);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);z-index:100;position:relative}
.tb-logo{width:42px;height:42px;border-radius:14px;background:linear-gradient(135deg,#7c3aed,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0;box-shadow:0 0 24px rgba(124,58,237,.45)}
.tb-info{flex:1;min-width:0}
.tb-title{font-size:16px;font-weight:900;letter-spacing:-.01em;line-height:1}
.tb-sub{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.14em;margin-top:2px}
.tb-act{display:flex;gap:8px;align-items:center}
.ib{width:38px;height:38px;border-radius:13px;background:var(--c2);border:1px solid var(--b1);display:flex;align-items:center;justify-content:center;color:var(--t2);font-size:14px;cursor:pointer;transition:all .15s;flex-shrink:0}
.ib:active{background:var(--b1);transform:scale(.92)}
.ib.live-dot::after{content:'';position:absolute;top:9px;right:9px;width:7px;height:7px;background:#22c55e;border-radius:50%;border:2px solid var(--bg);box-shadow:0 0 8px #22c55e}

/* ── Scroll ── */
.scroll{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}

/* ── Hero Banner ── */
.hero{padding:20px 16px 0;position:relative}
.hero-glow{position:absolute;top:0;left:50%;transform:translateX(-50%);width:280px;height:140px;background:radial-gradient(ellipse,rgba(124,58,237,.15) 0%,transparent 70%);pointer-events:none}
.live-chip{display:inline-flex;align-items:center;gap:6px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:20px;padding:5px 13px;margin-bottom:14px}
.live-dot2{width:6px;height:6px;border-radius:50%;background:#22c55e;box-shadow:0 0 8px #22c55e;animation:blink 2s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.live-chip span{font-size:9px;color:#4ade80;font-family:'JetBrains Mono',monospace;font-weight:700;letter-spacing:.14em}
.hero-h{font-size:30px;font-weight:900;line-height:1.05;letter-spacing:-.02em;margin-bottom:5px}
.hero-h em{font-style:normal;background:linear-gradient(135deg,#a78bfa,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-s{font-size:11px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.06em}
/* Stats row */
.stats{display:flex;gap:9px;padding:18px 16px 0}
.stat{flex:1;background:var(--c1);border:1px solid var(--b1);border-radius:16px;padding:13px 12px;display:flex;flex-direction:column;gap:4px}
.stat-val{font-size:22px;font-weight:900;background:linear-gradient(135deg,var(--p2),var(--g2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.stat-lbl{font-size:9px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.1em}

/* ── Search ── */
.srch-wrap{padding:16px 16px 0}
.srch{position:relative}
.srch-i{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:13px;pointer-events:none}
.srch input{width:100%;background:var(--c1);border:1px solid var(--b1);color:var(--t1);padding:14px 15px 14px 44px;border-radius:16px;font-size:14px;font-family:'Outfit',sans-serif;outline:none;transition:border-color .2s,background .2s}
.srch input:focus{border-color:rgba(124,58,237,.5);background:var(--c2)}
.srch input::placeholder{color:var(--t4)}

/* ── Section header ── */
.sh{display:flex;align-items:center;padding:20px 16px 12px}
.sh-left{display:flex;align-items:center;gap:9px;flex:1}
.sh-ico{width:32px;height:32px;border-radius:10px;background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.2);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--p2)}
.sh-txt{font-size:15px;font-weight:800}
.sh-cnt{font-size:9px;font-family:'JetBrains Mono',monospace;background:var(--c2);border:1px solid var(--b1);color:var(--t3);padding:4px 10px;border-radius:8px}

/* ── Batch Cards ── */
.bgrid{display:flex;flex-direction:column;gap:12px;padding:0 16px 100px}
.bcard{background:var(--c1);border:1px solid var(--b1);border-radius:22px;overflow:hidden;cursor:pointer;transition:transform .12s,border-color .2s;animation:up .35s ease both}
@keyframes up{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.bcard:active{transform:scale(.975);border-color:rgba(124,58,237,.35)}
/* Thumbnail */
.bthumb{position:relative;width:100%;aspect-ratio:16/7;overflow:hidden;background:var(--c2)}
.bthumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s ease}
.bcard:active .bthumb img{transform:scale(1.03)}
.boverlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(5,5,15,.96) 0%,rgba(5,5,15,.5) 45%,transparent 100%)}
/* Inside overlay */
.binfo{position:absolute;bottom:0;left:0;right:0;padding:14px 15px}
.btitle{font-size:15px;font-weight:800;color:#fff;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:8px;letter-spacing:-.01em}
.bchips{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
.chip{font-size:9px;padding:3px 9px;border-radius:7px;font-weight:800;font-family:'JetBrains Mono',monospace;display:inline-flex;align-items:center;gap:4px;letter-spacing:.04em}
.c-live{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.25)}
.c-price{background:rgba(124,58,237,.15);color:var(--p2);border:1px solid rgba(124,58,237,.3)}
.c-free{background:rgba(6,182,212,.1);color:var(--g2);border:1px solid rgba(6,182,212,.2)}
/* Top-right fav button */
.bfav{position:absolute;top:11px;right:11px;width:36px;height:36px;border-radius:50%;background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.08);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;backdrop-filter:blur(8px);transition:transform .2s;z-index:2}
.bfav:active{transform:scale(1.25)}
.bfav.on{color:#f87171}
/* CTA button inside card */
.bcta{display:flex;align-items:center;justify-content:center;gap:9px;margin:0 14px 14px;background:linear-gradient(135deg,#7c3aed,#5b21b6);border:none;border-radius:14px;padding:13px 16px;color:#fff;font-size:14px;font-weight:800;cursor:pointer;font-family:'Outfit',sans-serif;width:calc(100% - 28px);letter-spacing:.02em;box-shadow:0 4px 20px rgba(124,58,237,.3);transition:opacity .15s}
.bcta:active{opacity:.82}

/* ── Empty / Loader / Error ── */
.ld-wrap{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:70px 20px;gap:14px}
.spin{width:36px;height:36px;border:2.5px solid var(--b1);border-top-color:var(--p);border-radius:50%;animation:spin .75s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-txt{font-size:10px;color:var(--t3);font-family:'JetBrains Mono',monospace;letter-spacing:.14em}
.empty-wrap{display:flex;flex-direction:column;align-items:center;padding:70px 20px;gap:12px;text-align:center;color:var(--t3)}
.empty-wrap i{font-size:44px;opacity:.3}
.empty-wrap h3{font-size:16px;font-weight:700;color:var(--t2)}
.empty-wrap p{font-size:12px;line-height:1.6;max-width:260px}
.retry{margin-top:8px;background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);color:var(--p2);padding:11px 26px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif}
.retry:active{background:rgba(124,58,237,.25)}

/* ── Bottom Nav ── */
.bnav{flex-shrink:0;height:70px;background:rgba(8,8,16,.98);border-top:1px solid var(--b1);display:flex;align-items:center;padding-bottom:env(safe-area-inset-bottom);z-index:100}
.ni{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;cursor:pointer;color:var(--t4);transition:color .2s;padding:8px}
.ni.on{color:var(--p2)}
.ni i{font-size:20px;transition:transform .2s}
.ni.on i{transform:scale(1.1)}
.ni span{font-size:9px;font-weight:800;font-family:'JetBrains Mono',monospace;letter-spacing:.1em}
/* Nav indicator */
.ni.on::after{content:'';display:block;width:24px;height:2.5px;border-radius:2px;background:var(--p2);margin-top:-2px}

/* ── Toast ── */
.toast{position:fixed;bottom:86px;left:50%;transform:translateX(-50%) translateY(10px);background:rgba(12,12,22,.97);border:1px solid var(--b2);color:var(--t1);padding:12px 22px;border-radius:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:9px;opacity:0;transition:all .3s;z-index:9998;max-width:88vw;backdrop-filter:blur(16px);white-space:nowrap;pointer-events:none}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
</style>
</head>
<body>
<?php include 'splash.php'; ?>
<div id="app">
  <!-- Topbar -->
  <div class="tb">
    <div class="tb-logo"><i class="fas fa-graduation-cap"></i></div>
    <div class="tb-info">
      <div class="tb-title">𝑫𝑨𝑹𝑲 𝑼𝑵𝑰𝑽𝑬𝑹𝑺𝑬</div>
      <div class="tb-sub">OFFICIAL LEARNING PORTAL</div>
    </div>
    <div class="tb-act">
      <div class="ib" onclick="fetchBatches()"><i class="fas fa-rotate-right"></i></div>
      <div class="ib" style="position:relative" onclick="wapJoin&&wapJoin()"><i class="fab fa-whatsapp" style="color:#4ade80"></i></div>
    </div>
  </div>

  <!-- Scroll -->
  <div class="scroll" id="scr">
    <!-- Hero -->
    <div class="hero">
      <div class="hero-glow"></div>
      <div class="live-chip"><div class="live-dot2"></div><span>ALL COURSES LIVE</span></div>
      <div class="hero-h">Mera<br><em>Dashboard</em></div>
      <div class="hero-s" id="hero-sub">Apne enrolled batches dekho</div>
    </div>

    <!-- Stats -->
    <div class="stats">
      <div class="stat">
        <div class="stat-val" id="st-total">—</div>
        <div class="stat-lbl">BATCHES</div>
      </div>
      <div class="stat">
        <div class="stat-val" id="st-free">—</div>
        <div class="stat-lbl">FREE</div>
      </div>
      <div class="stat">
        <div class="stat-val" id="st-paid">—</div>
        <div class="stat-lbl">PAID</div>
      </div>
    </div>

    <!-- Search -->
    <div class="srch-wrap">
      <div class="srch">
        <i class="srch-i fas fa-search"></i>
        <input type="text" placeholder="Batch search karo…" id="srch" oninput="filter(this.value)" autocomplete="off">
      </div>
    </div>

    <!-- Section header -->
    <div class="sh">
      <div class="sh-left">
        <div class="sh-ico"><i class="fas fa-layer-group"></i></div>
        <div class="sh-txt">Enrolled Courses</div>
      </div>
      <div class="sh-cnt" id="sh-cnt">…</div>
    </div>

    <!-- Cards grid -->
    <div class="bgrid" id="grid">
      <div class="ld-wrap"><div class="spin"></div><div class="ld-txt">LOADING BATCHES…</div></div>
    </div>
  </div>

  <!-- Bottom Nav -->
  <nav class="bnav">
    <div class="ni on" onclick="navTo('batches.php')"><i class="fas fa-home"></i><span>HOME</span></div>
    <div class="ni" onclick="navTo('dashboard.php')"><i class="fas fa-th-large"></i><span>EXPLORE</span></div>
    <div class="ni" onclick="wapJoin&&wapJoin()"><i class="fab fa-whatsapp" style="color:#4ade80"></i><span>CHANNEL</span></div>
    <div class="ni" onclick="navTo('logout.php')"><i class="fas fa-sign-out-alt"></i><span>LOGOUT</span></div>
  </nav>
</div>

<!-- Toast -->
<div class="toast" id="toast"><i class="fas fa-circle-info" style="color:var(--p2)"></i><span id="tmsg"></span></div>

<?php include 'wa-popup.php'; ?>

<script>
/* ── Security ── */
(function(){
  const esc=()=>{try{document.documentElement.innerHTML='';}catch(e){}window.location.replace('about:blank');};
  setInterval(()=>{if((window.outerWidth-window.innerWidth)>180||(window.outerHeight-window.innerHeight)>230)esc();},900);
  document.addEventListener('contextmenu',e=>e.preventDefault());
  document.addEventListener('keydown',e=>{if(e.key==='F12'||(e.ctrlKey&&e.shiftKey&&['I','J','C','U'].includes(e.key))||(e.ctrlKey&&['U','S'].includes(e.key))){e.preventDefault();}});
  document.addEventListener('selectstart',e=>e.preventDefault());
  const _d={};Object.defineProperty(_d,'id',{get:()=>esc()});
  setInterval(()=>console.log(_d),2500);
})();

/* ── rApi ── */
async function rApi(action,params={},tries=4){
  for(let i=0;i<tries;i++){
    try{
      const qs=new URLSearchParams({action,...params}).toString();
      const r=await fetch('rozgar-api.php?'+qs,{headers:{'X-Requested-With':'XMLHttpRequest'},cache:'no-cache'});
      if(!r.ok)throw new Error('HTTP '+r.status);
      const d=await r.json();
      if(!d.ok&&i<tries-1)throw new Error(d.error||'API err');
      return d;
    }catch(e){
      if(i<tries-1)await new Promise(r=>setTimeout(r,1800*(i+1)));
      else return{ok:false,data:[],error:e.message};
    }
  }
}

/* ── State ── */
let _all=[];
const _sv=JSON.parse(localStorage.getItem('du_sv')||'[]');
function H(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

/* ── Init ── */
document.addEventListener('DOMContentLoaded',fetchBatches);

async function fetchBatches(){
  document.getElementById('grid').innerHTML='<div class="ld-wrap"><div class="spin"></div><div class="ld-txt">LOADING BATCHES…</div></div>';
  document.getElementById('sh-cnt').textContent='…';
  const r=await rApi('batches');
  _all=r.data||[];
  if(!_all.length){
    const em=r.error||'Network error aayi';
    document.getElementById('grid').innerHTML=`<div class="empty-wrap"><i class="fas fa-satellite-dish"></i><h3>Batches nahi aaye</h3><p>${H(em)}</p><button class="retry" onclick="fetchBatches()"><i class="fas fa-rotate-right"></i> Dobara Try Karo</button></div>`;
    document.getElementById('sh-cnt').textContent='ERR';
    document.getElementById('hero-sub').textContent='Load nahi hua';
    upStats(0,0,0);
    return;
  }
  const free=_all.filter(b=>!b.price||b.price==0).length;
  upStats(_all.length,free,_all.length-free);
  document.getElementById('hero-sub').textContent=_all.length+' courses enrolled ✨';
  render(_all);
}

function upStats(t,f,p){
  document.getElementById('st-total').textContent=t||'0';
  document.getElementById('st-free').textContent=f||'0';
  document.getElementById('st-paid').textContent=p||'0';
}

function filter(q){
  const f=q?_all.filter(b=>(b.course_name||'').toLowerCase().includes(q.toLowerCase())):_all;
  render(f);
}

function render(list){
  document.getElementById('sh-cnt').textContent=list.length+' TOTAL';
  const el=document.getElementById('grid');
  if(!list.length){el.innerHTML='<div class="empty-wrap"><i class="fas fa-box-open"></i><h3>Koi batch nahi mila</h3><p>Search query change karo</p></div>';return;}
  el.innerHTML=list.map((b,i)=>{
    const fv=_sv.includes(String(b.id));
    const th=b.course_thumbnail||'https://placehold.co/640x280/0b0b12/7c3aed?text='+encodeURIComponent(b.course_name||'Batch');
    const pr=b.price&&b.price>0?'₹'+b.price:'FREE';
    return`<div class="bcard" style="animation-delay:${i*.055}s">
      <div class="bthumb">
        <img src="${H(th)}" loading="${i<3?'eager':'lazy'}" onerror="this.src='https://placehold.co/640x280/0b0b12/7c3aed?text=Dark+Universe'">
        <div class="boverlay"></div>
        <button class="bfav${fv?' on':''}" onclick="event.stopPropagation();toggleSave('${b.id}',this)">
          <i class="${fv?'fas':'far'} fa-heart"></i>
        </button>
        <div class="binfo">
          <div class="btitle">${H(b.course_name)}</div>
          <div class="bchips">
            <span class="chip c-live"><i class="fas fa-circle" style="font-size:5px"></i> LIVE</span>
            <span class="chip ${b.price&&b.price>0?'c-price':'c-free'}">${H(pr)}</span>
          </div>
        </div>
      </div>
      <button class="bcta" onclick="go(${b.id})">
        <i class="fas fa-play-circle" style="font-size:18px"></i> Let's Study
      </button>
    </div>`;
  }).join('');
}

function go(id){
  const b=_all.find(x=>String(x.id)===String(id));
  window.location.href='subjects.php?bid='+id+'&bn='+encodeURIComponent(b?.course_name||'Batch');
}

function toggleSave(id,btn){
  const idx=_sv.indexOf(String(id));
  if(idx>-1){_sv.splice(idx,1);btn.classList.remove('on');btn.innerHTML='<i class="far fa-heart"></i>';toast('Saved list se remove kiya');}
  else{_sv.push(String(id));btn.classList.add('on');btn.innerHTML='<i class="fas fa-heart"></i>';toast('Saved ❤️');}
  localStorage.setItem('du_sv',JSON.stringify(_sv));
}

function navTo(u){window.location.href=u;}
let _tt;
function toast(m){const t=document.getElementById('toast');document.getElementById('tmsg').textContent=m;t.classList.add('on');clearTimeout(_tt);_tt=setTimeout(()=>t.classList.remove('on'),2800);}
</script>
</body>
</html>
