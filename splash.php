<?php
/**
 * DARK UNIVERSE — Splash Screen
 * Include at the very start of <body> on index.php / batches.php
 * Fades out automatically after 2.2s
 */
?>
<div id="spl">
  <div class="spl-bg"></div>
  <div class="spl-stars" id="spl-stars"></div>
  <div class="spl-content">
    <div class="spl-logo">
      <div class="spl-ring spl-r1"></div>
      <div class="spl-ring spl-r2"></div>
      <div class="spl-ring spl-r3"></div>
      <div class="spl-icon"><i class="fas fa-graduation-cap"></i></div>
    </div>
    <div class="spl-name">𝑫𝑨𝑹𝑲 𝑼𝑵𝑰𝑽𝑬𝑹𝑺𝑬</div>
    <div class="spl-tagline">OFFICIAL LEARNING PORTAL</div>
    <div class="spl-bar-wrap">
      <div class="spl-bar"><div class="spl-fill" id="spl-fill"></div></div>
      <div class="spl-pct" id="spl-pct">0%</div>
    </div>
    <div class="spl-powered">Powered by <span>Rozgar with Ankit</span></div>
  </div>
</div>

<style>
#spl{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:#020208;transition:opacity .6s ease,visibility .6s ease}
#spl.hide{opacity:0;visibility:hidden;pointer-events:none}
.spl-bg{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 30%,rgba(124,58,237,.18) 0%,transparent 70%),radial-gradient(ellipse 50% 40% at 80% 80%,rgba(6,182,212,.08) 0%,transparent 60%)}
.spl-stars{position:absolute;inset:0;overflow:hidden}
.spl-star{position:absolute;border-radius:50%;background:#fff;animation:twinkle var(--d,3s) ease-in-out infinite var(--del,0s)}
@keyframes twinkle{0%,100%{opacity:var(--o,.2);transform:scale(1)}50%{opacity:1;transform:scale(1.4)}}
.spl-content{position:relative;display:flex;flex-direction:column;align-items:center;gap:0}
.spl-logo{position:relative;width:110px;height:110px;display:flex;align-items:center;justify-content:center;margin-bottom:28px}
.spl-ring{position:absolute;border-radius:50%;border:1.5px solid transparent;animation:rotateRing linear infinite}
.spl-r1{width:100%;height:100%;border-top-color:rgba(124,58,237,.8);border-right-color:rgba(124,58,237,.3);animation-duration:3s}
.spl-r2{width:80%;height:80%;border-top-color:rgba(6,182,212,.7);border-left-color:rgba(6,182,212,.2);animation-duration:2s;animation-direction:reverse}
.spl-r3{width:60%;height:60%;border-bottom-color:rgba(167,139,250,.6);border-right-color:rgba(167,139,250,.2);animation-duration:1.5s}
@keyframes rotateRing{to{transform:rotate(360deg)}}
.spl-icon{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,#7c3aed,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;box-shadow:0 0 40px rgba(124,58,237,.6),0 0 80px rgba(124,58,237,.2);animation:iconPulse 2s ease-in-out infinite}
@keyframes iconPulse{0%,100%{box-shadow:0 0 30px rgba(124,58,237,.6),0 0 60px rgba(124,58,237,.15)}50%{box-shadow:0 0 50px rgba(124,58,237,.9),0 0 100px rgba(124,58,237,.3)}}
.spl-name{font-size:26px;font-weight:900;letter-spacing:.05em;text-align:center;background:linear-gradient(135deg,#fff 30%,#a78bfa 60%,#22d3ee 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:nameIn .8s cubic-bezier(.34,1.25,.64,1) both;animation-delay:.3s;margin-bottom:6px}
@keyframes nameIn{from{opacity:0;transform:translateY(16px) scale(.9)}to{opacity:1;transform:translateY(0) scale(1)}}
.spl-tagline{font-size:9px;font-family:'JetBrains Mono',monospace;letter-spacing:.22em;color:#475569;animation:nameIn .6s ease both;animation-delay:.6s;margin-bottom:36px}
.spl-bar-wrap{display:flex;flex-direction:column;align-items:center;gap:8px;width:200px;animation:nameIn .5s ease both;animation-delay:.8s}
.spl-bar{width:100%;height:3px;background:rgba(255,255,255,.07);border-radius:3px;overflow:hidden}
.spl-fill{height:100%;width:0%;background:linear-gradient(90deg,#7c3aed,#06b6d4,#a78bfa);border-radius:3px;transition:width .05s linear;box-shadow:0 0 10px rgba(124,58,237,.8)}
.spl-pct{font-size:10px;font-family:'JetBrains Mono',monospace;color:#475569;letter-spacing:.1em}
.spl-powered{font-size:10px;color:#2a2a40;font-family:'JetBrains Mono',monospace;letter-spacing:.1em;margin-top:40px;animation:nameIn .5s ease both;animation-delay:1s}
.spl-powered span{color:#7c3aed}
</style>
<script>
(function(){
  // Stars
  const sc=document.getElementById('spl-stars');
  for(let i=0;i<60;i++){
    const s=document.createElement('div');
    s.className='spl-star';
    const sz=Math.random()*2.5+.5;
    s.style.cssText=`width:${sz}px;height:${sz}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*4}s;--del:${Math.random()*4}s;--o:${.1+Math.random()*.4}`;
    sc.appendChild(s);
  }

  // Progress bar animation
  const fill=document.getElementById('spl-fill');
  const pct=document.getElementById('spl-pct');
  let p=0;
  const steps=[
    {target:30,speed:18},
    {target:60,speed:25},
    {target:85,speed:35},
    {target:95,speed:60},
    {target:100,speed:20}
  ];
  let stepIdx=0;
  function tick(){
    if(stepIdx>=steps.length)return;
    const st=steps[stepIdx];
    if(p<st.target){
      p+=.8;
      fill.style.width=p+'%';
      pct.textContent=Math.floor(p)+'%';
      setTimeout(tick,st.speed);
    } else { stepIdx++; tick(); }
  }
  tick();

  // Hide after 2.4s
  setTimeout(function(){
    document.getElementById('spl').classList.add('hide');
    setTimeout(function(){
      const el=document.getElementById('spl');
      if(el)el.remove();
    },650);
  },2400);
})();
</script>
