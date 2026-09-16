<?php
$current=basename($_SERVER['PHP_SELF']); $role=user()['role_code']; $initial=mb_substr(user()['name'],0,1,'UTF-8'); $lang=current_lang();
function lang_url(string $l): string { $q=$_GET; $q['lang']=$l; return basename($_SERVER['PHP_SELF']).'?'.http_build_query($q); }
?>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
  <div class="brand"><div class="brand-mark">◇</div><div><div class="brand-title">Material Approval</div><div class="brand-sub"><?= current_lang()==='th'?'ระบบบริหารจัดการเบิกจ่ายพัสดุ':'Procurement & Asset Disbursement' ?></div></div></div>
  <div class="nav-section"><?= current_lang()==='th'?'เมนูหลัก':'MAIN MENU' ?></div>
  <nav class="side-nav">
    <a href="index.php" class="<?=$current==='index.php'?'active':''?>"><span class="nav-icon">⌂</span><span><?=e(t('dashboard'))?></span><small>Dashboard</small></a>
    <a href="requests.php" class="<?=$current==='requests.php'?'active':''?>"><span class="nav-icon">▤</span><span><?=e(t('requests'))?></span><small>Requisition</small></a>
    <?php if(in_array($role,['requester','admin'],true)):?><a href="request_new.php" class="<?=$current==='request_new.php'?'active':''?>"><span class="nav-icon">＋</span><span><?=e(t('new_request'))?></span><small>New Request</small></a><?php endif;?>
    <?php if(in_array($role,['dept_head','procurement','director','admin'],true)):?><a href="approvals.php" class="<?=$current==='approvals.php'?'active':''?>"><span class="nav-icon">✓</span><span><?=e(t('approvals'))?></span><small>Approval Flow</small></a><?php endif;?>
    <?php if(in_array($role,['procurement','admin'],true)):?><a href="issue.php" class="<?=$current==='issue.php'?'active':''?>"><span class="nav-icon">⇢</span><span><?=e(t('issue'))?></span><small>Disbursement</small></a><?php endif;?>
    <a href="materials.php" class="<?=$current==='materials.php'?'active':''?>"><span class="nav-icon">▦</span><span><?=e(t('materials'))?></span><small>Inventory</small></a>
    <?php if(in_array($role,['procurement','admin'],true)):?><a href="vendors.php" class="<?=$current==='vendors.php'?'active':''?>"><span class="nav-icon">♙</span><span><?= current_lang()==='th'?'ผู้ขาย':'Vendors' ?></span><small>Top Vendors</small></a><?php endif;?>
  </nav>
  <?php if($role==='admin'):?><div class="nav-section">จัดการองค์กร</div><nav class="side-nav"><a href="departments.php" class="<?=$current==='departments.php'?'active':''?>"><span class="nav-icon">▦</span><span>แผนก</span><small>Departments</small></a></nav><?php endif;?>
  <div class="nav-section"><?=e(t('system'))?></div>
  <nav class="side-nav muted-nav"><a href="#" onclick="return false"><span class="nav-icon">▥</span><span><?=e(t('reports'))?></span><small><?=e(t('soon'))?></small></a><a href="#" onclick="return false"><span class="nav-icon">⚙</span><span><?=e(t('settings'))?></span><small><?=e(t('soon'))?></small></a></nav>
  <div class="sidebar-bottom"><div class="help-card"><span>◉</span><div><b><?= current_lang()==='th'?'ศูนย์ช่วยเหลือ':'Help & Support'?></b><small><?= current_lang()==='th'?'ติดต่อฝ่าย IT':'Contact IT Support'?></small></div><em>›</em></div><div class="profile-mini"><div class="avatar"><?=e($initial)?></div><div><div class="name"><?=e(user()['name'])?></div><div class="role"><?=e(role_text($role))?></div></div><a class="logout-link" href="logout.php" title="<?=e(t('logout'))?>">↪</a></div></div>
</aside>
<div class="main-area"><header class="topbar"><div class="top-left"><button class="mobile-menu" type="button" onclick="toggleSidebar()">☰</button><div class="search-box"><span>⌕</span><input type="text" placeholder="<?= current_lang()==='th'?'ค้นหา (Search...)':'Search...' ?>"></div></div><div class="top-actions"><div class="lang-switch"><a class="<?=$lang==='th'?'active':''?>" href="<?=e(lang_url('th'))?>">🇹🇭 TH</a><a class="<?=$lang==='en'?'active':''?>" href="<?=e(lang_url('en'))?>">🇬🇧 EN</a></div><div class="date-chip">▣ <?=date('d M Y')?></div><button class="icon-btn" type="button" title="Notifications">♧<i></i></button><div class="user-top"><div class="avatar"><?=e($initial)?></div><div><div class="name"><?=e(user()['name'])?></div><div class="role"><?=e(role_text($role))?></div></div><b class="user-chevron">⌄</b></div></div></header>
