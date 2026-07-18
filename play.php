<?php
// ── Server-side Video Proxy ───────────────────────────────────────
// Fetches video URL server-side, decrypts, and redirects.
// AES key NEVER touches the browser.
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
auto_login();
send_security_headers();

$vid = intval($_GET['vid'] ?? 0);
$bid = intval($_GET['bid'] ?? 0);
$q   = preg_replace('/[^a-z0-9]/', '', $_GET['q'] ?? 'auto');

if(!$vid || !$bid){ http_response_code(400); die('Bad request'); }

$token  = $_SESSION['token'];
$userid = $_SESSION['userid'];

$url = API_BASE.'/get/fetchVideoDetailsById?course_id='.$bid.'&video_id='.$vid.'&ytflag=0&folder_wise_course=0&userid='.urlencode($userid);

$ch=curl_init($url);
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>20,CURLOPT_CONNECTTIMEOUT=>8,
    CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_ENCODING=>'',
    CURLOPT_HTTPHEADER=>[
        'Client-Service: Appx','Auth-Key: appxapi',
        'Authorization: '.$token,'User-ID: '.$userid,
        'User-Agent: Mozilla/5.0 (Linux; Android 12) AppleWebKit/537.36',
    ],
]);
$resp=curl_exec($ch);curl_close($ch);

$data=json_decode($resp,true);
$d=$data['data']??null;
if(!$d){
    if(preg_match('/\{"status":[\s\S]*?\}/U',$resp??'',$m)){
        $temp=json_decode($m[0],true);$d=$temp['data']??null;
    }
}
if(!$d){ http_response_code(404); die('Video not found'); }

$player='https://dark-rwa.vercel.app/player?url=';

// YouTube
if(!empty($d['video_id'])&&empty($d['download_link'])){
    $yt=strlen($d['video_id'])>20?decrypt_appx($d['video_id']):$d['video_id'];
    header('Location: https://www.youtube.com/watch?v='.urlencode($yt));exit;
}
// Direct download link
if(!empty($d['download_link'])){
    $u=decrypt_appx($d['download_link']);
    if($q!=='auto') $u=preg_replace('/(1080p|720p|480p|360p|240p)/',$q,$u);
    header('Location: '.$player.urlencode($u));exit;
}
// Encrypted links array
$lnks=$d['encrypted_links']??[];
foreach($lnks as $lnk){
    if(!empty($lnk['path'])){
        $u=decrypt_appx($lnk['path']);
        if($q!=='auto') $u=preg_replace('/(1080p|720p|480p|360p|240p)/',$q,$u);
        if($u){header('Location: '.$player.urlencode($u));exit;}
    }
}
http_response_code(404); die('No playable source');
