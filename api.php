<?php
// ── Secure API Proxy — 429 Rate-Limit Safe ───────────────────────
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
send_security_headers();
header('Content-Type: application/json');

// Block direct browser access
$xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
$ref = $_SERVER['HTTP_REFERER'] ?? '';
if (empty($xhr) && empty($ref)) {
    http_response_code(403);
    echo json_encode(['e' => 'Forbidden']);
    exit;
}

// Minimal rate-limit on our own proxy (120 req/min per IP)
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0';
$rk  = DATA_DIR . 'rl_' . md5($ip) . '.json';
$now = time();
if (file_exists($rk)) {
    $rl = json_decode(@file_get_contents($rk), true) ?: ['c'=>0,'t'=>$now];
    if ($now - $rl['t'] < 60) {
        if ($rl['c'] > 150) { http_response_code(429); echo json_encode(['e'=>'Rate limit']); exit; }
        $rl['c']++;
    } else { $rl = ['c'=>1,'t'=>$now]; }
} else { $rl = ['c'=>1,'t'=>$now]; }
@file_put_contents($rk, json_encode($rl));

auto_login();
$token  = $_SESSION['token'];
$userid = $_SESSION['userid'];

$raw = $_POST['q'] ?? $_GET['q'] ?? '';
if (empty($raw)) { echo json_encode(['e'=>'Bad request']); exit; }

$endpoint = base64_decode(strtr($raw, '-_', '+/'));
if (empty($endpoint)) { echo json_encode(['e'=>'Invalid']); exit; }
$endpoint = str_replace(API_BASE, '', $endpoint);

// Whitelist
$allowed = [
    '/get/mycoursev2',
    '/get/allsubjectfrmlivecourseclass',
    '/get/alltopicfrmlivecourseclass',
    '/get/livecourseclassbycoursesubtopconceptapiv3',
    '/get/fetchVideoDetailsById',
    '/post/userLogin',
    '/get/userdetails'
];
$ok = false;
foreach ($allowed as $a) {
    if (strpos($endpoint, $a) === 0) { $ok = true; break; }
}
if (!$ok) { echo json_encode(['e'=>'Not allowed']); exit; }

// ── Cache Layer ──────────────────────────────────────────────────
// These endpoints are cached. fetchVideoDetailsById is NOT cached (per-video).
$cacheEPs = [
    '/get/mycoursev2',
    '/get/allsubjectfrmlivecourseclass',
    '/get/alltopicfrmlivecourseclass',
    '/get/livecourseclassbycoursesubtopconceptapiv3',
];
$cacheTTL  = 600; // 10 minutes fresh cache
$staleTTL  = 86400; // 24 hours stale-on-error fallback
$useCache  = false;
foreach ($cacheEPs as $ce) {
    if (strpos($endpoint, $ce) === 0) { $useCache = true; break; }
}
$cacheFile = DATA_DIR . 'c_' . md5($userid . $endpoint) . '.json';

// Serve fresh cache if within TTL
if ($useCache && file_exists($cacheFile)) {
    $age = time() - filemtime($cacheFile);
    if ($age < $cacheTTL) {
        $cached = @file_get_contents($cacheFile);
        if ($cached) {
            $cd = json_decode($cached, true);
            if ($cd && !empty($cd['data'])) {
                header('X-Cache: HIT');
                echo $cached;
                exit;
            }
        }
    }
}

// ── cURL to external API ─────────────────────────────────────────
$sep = (strpos($endpoint,'?') !== false) ? '&' : '?';
$url = API_BASE . $endpoint . $sep . 'userid=' . urlencode($userid);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING       => '',
    CURLOPT_HTTPHEADER     => [
        'Client-Service: Appx',
        'source: website',
        'Auth-Key: appxapi',
        'Authorization: ' . $token,
        'User-ID: '       . $userid,
        'User-Agent: Mozilla/5.0 (Linux; Android 12) AppleWebKit/537.36 Chrome/124.0.0.0 Mobile Safari/537.36',
    ],
]);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

// ── Handle 429 from upstream — ALWAYS serve cache ────────────────
if ($http_code === 429 || $http_code === 503 || $http_code === 502) {
    if ($useCache && file_exists($cacheFile)) {
        $stale = @file_get_contents($cacheFile);
        if ($stale) {
            $sd = json_decode($stale, true);
            if ($sd && !empty($sd['data'])) {
                header('X-Cache: STALE');
                echo $stale; // Serve stale data silently
                exit;
            }
        }
    }
    // No cache at all — tell JS to retry later
    echo json_encode(['e' => 'upstream_429', 'retry' => true]);
    exit;
}

// ── Handle curl failure ──────────────────────────────────────────
if ($response === false || empty($response)) {
    // Try stale cache on network error
    if ($useCache && file_exists($cacheFile)) {
        $stale = @file_get_contents($cacheFile);
        if ($stale) {
            $sd = json_decode($stale, true);
            if ($sd && !empty($sd['data'])) {
                header('X-Cache: STALE-ERR');
                echo $stale;
                exit;
            }
        }
    }
    echo json_encode(['e' => 'Network error', 'm' => $curl_err]);
    exit;
}

// ── Parse response ────────────────────────────────────────────────
$data = json_decode($response, true);
if ($data === null) {
    // Try to extract embedded JSON (some responses have junk prefix)
    if (preg_match('/\{"status":[\s\S]*?\}/U', $response, $m)) {
        $j='';$o=0;$c=0;$s=false;
        for($i=0;$i<strlen($m[0]);$i++){
            $ch2=$m[0][$i];
            if($ch2==='{'){$o++;$s=true;} if($ch2==='}')$c++;
            if($s)$j.=$ch2;
            if($s&&$o===$c&&$o>0)break;
        }
        $data = json_decode($j, true);
    }
    if ($data === null) {
        // Serve stale cache if parse fails
        if ($useCache && file_exists($cacheFile)) {
            $stale = @file_get_contents($cacheFile);
            if ($stale) {
                $sd = json_decode($stale, true);
                if ($sd && !empty($sd['data'])) {
                    header('X-Cache: STALE-PARSE');
                    echo $stale;
                    exit;
                }
            }
        }
        echo json_encode(['e'=>'API parse error','h'=>$http_code]);
        exit;
    }
}

// ── Save to cache if valid data received ─────────────────────────
if ($useCache && !empty($data['data'])) {
    @file_put_contents($cacheFile, json_encode($data));
}

echo json_encode($data);
