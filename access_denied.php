<?php
http_response_code(403);
$home = 'index.php';
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ไม่มีสิทธิ์ใช้งาน</title>
<style>
:root{--brown:#8a654a;--brown-dark:#604735;--cream:#f7f2ea;--line:#e6dbcf;--text:#493a31;--muted:#7f7065}
*{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:system-ui,-apple-system,"Segoe UI",Tahoma,sans-serif;color:var(--text);background:radial-gradient(circle at 15% 10%,rgba(198,167,132,.18),transparent 26%),radial-gradient(circle at 85% 85%,rgba(176,138,103,.16),transparent 25%),var(--cream);display:grid;place-items:center;overflow:hidden}
.shape{position:fixed;border:1px solid rgba(138,101,74,.22);border-radius:45% 55% 50% 50%;pointer-events:none}.shape.one{width:330px;height:220px;left:-100px;top:-110px}.shape.two{width:390px;height:240px;right:-130px;bottom:-125px}.wrap{width:min(92%,620px);text-align:center;padding:38px 18px;position:relative}.icon{width:112px;height:112px;margin:0 auto 26px;border-radius:50%;background:rgba(255,255,255,.46);display:grid;place-items:center;color:var(--brown);box-shadow:0 12px 35px rgba(99,72,52,.08)}.lock{font-size:58px;line-height:1;filter:grayscale(.15)}h1{margin:0;font-size:clamp(34px,6vw,54px);letter-spacing:-.04em;font-weight:750;color:var(--brown-dark)}.lead{margin:18px 0 8px;font-size:20px}.desc{margin:0 auto;color:var(--muted);font-size:16px;line-height:1.7;max-width:520px}.actions{display:flex;justify-content:center;gap:14px;margin-top:34px;flex-wrap:wrap}.btn{min-width:190px;height:54px;padding:0 22px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;gap:10px;text-decoration:none;font-weight:700;font-size:16px;transition:.2s}.primary{background:var(--brown);color:#fff;box-shadow:0 10px 24px rgba(100,70,48,.18)}.primary:hover{background:#76543e;transform:translateY(-1px)}.secondary{border:1px solid #bda998;color:var(--brown-dark);background:rgba(255,255,255,.34)}.secondary:hover{background:#fffaf5}.help{margin:46px auto 0;padding-top:25px;border-top:1px solid var(--line);color:#8b7d72;font-size:14px}.help span{font-size:20px;margin-right:7px}@media(max-width:560px){.wrap{padding:28px 10px}.lead{font-size:18px}.desc{font-size:15px}.btn{width:100%;min-width:0}.shape.one{left:-170px}.shape.two{right:-190px}}
</style>
</head>
<body>
<div class="shape one"></div><div class="shape two"></div>
<main class="wrap">
  <div class="icon"><div class="lock">🔒</div></div>
  <h1>ไม่มีสิทธิ์ใช้งาน</h1>
  <p class="lead">คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้</p>
  <p class="desc">กรุณาติดต่อผู้ดูแลระบบ หากคุณเชื่อว่าควรมีสิทธิ์ในการใช้งาน</p>
  <div class="actions">
    <a class="btn primary" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>">⌂&nbsp; กลับหน้าหลัก</a>
    <a class="btn secondary" href="javascript:history.back()">←&nbsp; ย้อนกลับ</a>
  </div>
  <div class="help"><span>◔</span> หากพบปัญหา กรุณาติดต่อผู้ดูแลระบบ</div>
</main>
</body>
</html>
