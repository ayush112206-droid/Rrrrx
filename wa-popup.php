<?php /* WhatsApp Popup — included on every page, no cooldown */ ?>
<style>
#wap{position:fixed;inset:0;z-index:9990;display:flex;align-items:flex-end;justify-content:center;background:rgba(0,0,0,.7);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);opacity:0;transition:opacity .35s;pointer-events:none}
#wap.on{opacity:1;pointer-events:all}
#wap-sh{width:100%;max-width:480px;background:linear-gradient(180deg,#0e0e1a 0%,#080810 100%);border:1px solid rgba(37,211,102,.2);border-bottom:none;border-radius:28px 28px 0 0;padding:10px 20px 46px;transform:translateY(100%);transition:transform .45s cubic-bezier(.32,1.25,.55,1)}
#wap.on #wap-sh{transform:translateY(0)}
.wph{width:44px;height:4px;border-radius:3px;background:#1e1e30;margin:0 auto 20px}
.wpi{width:82px;height:82px;border-radius:28px;background:linear-gradient(135deg,#25d366,#128c7e);display:flex;align-items:center;justify-content:center;font-size:40px;color:#fff;margin:0 auto 14px;box-shadow:0 12px 40px rgba(37,211,102,.5),0 0 0 1px rgba(37,211,102,.2);position:relative;flex-shrink:0}
.wpi::after{content:'';position:absolute;inset:-8px;border-radius:34px;border:1.5px solid rgba(37,211,102,.15);animation:wpulse 2.5s ease-in-out infinite}
@keyframes wpulse{0%,100%{transform:scale(1);opacity:.6}50%{transform:scale(1.06);opacity:0}}
.wpt{font-size:21px;font-weight:900;color:#fff;text-align:center;letter-spacing:-.02em;margin-bottom:6px}
.wps{font-size:12.5px;color:#64748b;text-align:center;line-height:1.65;margin-bottom:18px;padding:0 10px}
.wp-perks{display:flex;gap:8px;margin-bottom:20px}
.wp-perk{flex:1;background:rgba(37,211,102,.06);border:1px solid rgba(37,211,102,.12);border-radius:14px;padding:12px 8px;display:flex;flex-direction:column;align-items:center;gap:6px}
.wp-perk i{font-size:20px;color:#4ade80}
.wp-perk span{font-size:8.5px;color:#94a3b8;font-weight:800;font-family:'JetBrains Mono',monospace;letter-spacing:.08em;text-align:center}
.wp-name{display:flex;align-items:center;gap:8px;background:rgba(37,211,102,.07);border:1px solid rgba(37,211,102,.18);border-radius:30px;padding:8px 18px;margin:0 auto 18px;cursor:pointer;width:fit-content}
.wp-name i{color:#4ade80;font-size:16px}
.wp-name span{font-size:11px;color:#4ade80;font-family:'JetBrains Mono',monospace;font-weight:700;letter-spacing:.06em}
.wp-join{width:100%;background:linear-gradient(135deg,#25d366,#1aad57);border:none;border-radius:17px;padding:17px;color:#fff;font-size:16px;font-weight:800;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;gap:11px;margin-bottom:10px;box-shadow:0 8px 28px rgba(37,211,102,.4);transition:transform .12s,box-shadow .12s;letter-spacing:.01em}
.wp-join:active{transform:scale(.97);box-shadow:0 3px 12px rgba(37,211,102,.25)}
.wp-join i{font-size:22px}
.wp-skip{width:100%;background:transparent;border:1px solid #1e1e30;border-radius:14px;padding:13px;color:#475569;font-size:13px;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;transition:background .15s}
.wp-skip:active{background:#0d0d14}
</style>

<div id="wap" onclick="if(event.target===this)wapClose()">
  <div id="wap-sh">
    <div class="wph"></div>
    <div class="wpi"><i class="fab fa-whatsapp"></i></div>
    <div class="wpt">Join WhatsApp Channel</div>
    <div class="wps">Rozgar with Ankit ke Official Channel se judo. Free study material, live updates aur exclusive resources pao!</div>
    <div class="wp-perks">
      <div class="wp-perk"><i class="fas fa-book-open"></i><span>FREE NOTES</span></div>
      <div class="wp-perk"><i class="fas fa-video"></i><span>LIVE CLASS</span></div>
      <div class="wp-perk"><i class="fas fa-bell"></i><span>ALERTS</span></div>
      <div class="wp-perk"><i class="fas fa-gift"></i><span>RESOURCES</span></div>
    </div>
    <div class="wp-name" onclick="wapJoin()">
      <i class="fab fa-whatsapp"></i>
      <span><?=WA_NAME?></span>
    </div>
    <button class="wp-join" onclick="wapJoin()">
      <i class="fab fa-whatsapp"></i> Abhi Join Karo — Free!
    </button>
    <button class="wp-skip" onclick="wapClose()">Baad mein join karunga</button>
  </div>
</div>
<script>
setTimeout(()=>document.getElementById('wap').classList.add('on'),1500);
function wapJoin(){window.open('<?=WA_CHANNEL?>','_blank');wapClose();}
function wapClose(){const el=document.getElementById('wap');el.style.opacity='0';document.getElementById('wap-sh').style.transform='translateY(100%)';setTimeout(()=>{if(el)el.remove();},420);}
// Also expose joinWA globally for other buttons
function joinWA(){wapJoin();}
function closeWA(){wapClose();}
</script>
