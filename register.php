<?php
require 'config.php';
if (user()) redirect('index.php');

$pdo = db();
$err = '';
$deps = $pdo->query('SELECT name FROM departments WHERE active=1 ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '' || $username === '' || $password === '') {
            throw new Exception(current_lang() === 'th' ? 'กรุณากรอกข้อมูลให้ครบถ้วน' : 'Please complete all required fields.');
        }
        if (strlen($username) < 3) {
            throw new Exception(current_lang() === 'th' ? 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร' : 'Username must be at least 3 characters.');
        }
        if (strlen($password) < 4) {
            throw new Exception(current_lang() === 'th' ? 'รหัสผ่านอย่างน้อย 4 ตัวอักษร' : 'Password must be at least 4 characters.');
        }
        if ($password !== $confirmPassword) {
            throw new Exception(current_lang() === 'th' ? 'ยืนยันรหัสผ่านไม่ตรงกัน' : 'Password confirmation does not match.');
        }

        $stmt = $pdo->prepare("INSERT INTO users(name,username,password_hash,role_code,department) VALUES(?,?,?,'requester',?)");
        $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), $department]);

        flash('ok', current_lang() === 'th' ? 'สร้างบัญชีสำเร็จ กรุณาเข้าสู่ระบบ' : 'Account created successfully. Please sign in.');
        redirect('login.php');
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head><?php include 'partials/head.php'; ?></head>
<body>
<div class="login-page register-page">
    <section class="login-brand register-brand">
        <div class="login-logo">♙</div>
        <h1><?= current_lang()==='th' ? 'สมัครสมาชิก' : 'Create account' ?></h1>
        <p><?= current_lang()==='th'
            ? 'เข้าร่วมระบบบริหารจัดการพัสดุ เพื่อความสะดวก รวดเร็ว และมีประสิทธิภาพในการเบิกจ่ายและควบคุมพัสดุของหน่วยงาน'
            : 'Join the material management system for a faster, simpler and more efficient way to request and manage inventory.' ?></p>
        <div class="feature-list">
            <div>✓ <?= current_lang()==='th' ? 'ใช้งานง่าย สะดวก รวดเร็ว' : 'Simple, convenient and fast' ?></div>
            <div>✓ <?= current_lang()==='th' ? 'จัดการพัสดุได้อย่างเป็นระบบ' : 'Manage materials in one system' ?></div>
            <div>✓ <?= current_lang()==='th' ? 'รองรับการใช้งานได้ทุกอุปกรณ์' : 'Ready for every device' ?></div>
        </div>
    </section>

    <section class="login-side register-side">
        <div class="auth-topbar">
            <div class="lang-switch">
                <a class="<?= current_lang()==='th'?'active':'' ?>" href="register.php?lang=th">🇹🇭 TH</a>
                <a class="<?= current_lang()==='en'?'active':'' ?>" href="register.php?lang=en">🇬🇧 EN</a>
            </div>
        </div>

        <div class="login-card register-card">
            <h2><?= current_lang()==='th' ? 'สมัครสมาชิก' : 'Create account' ?></h2>
            <div class="sub"><?= current_lang()==='th' ? 'กรุณากรอกข้อมูลของคุณให้ครบถ้วน เพื่อสร้างบัญชีใหม่' : 'Fill in your information below to create a new account.' ?></div>

            <?php if ($err): ?>
                <div class="alert error"><?= e($err) ?></div>
            <?php endif; ?>

            <form method="post" class="auth-form">
                <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

                <div class="field">
                    <label><?= current_lang()==='th' ? 'ชื่อผู้ใช้ (Username)' : 'Username' ?></label>
                    <div class="auth-input"><span>♙</span><input name="username" placeholder="<?= current_lang()==='th' ? 'ชื่อผู้ใช้ (Username)' : 'Username' ?>" required autofocus value="<?= e($_POST['username'] ?? '') ?>"></div>
                </div>

                <div class="field">
                    <label><?= current_lang()==='th' ? 'ชื่อ-นามสกุล' : 'Full name' ?></label>
                    <div class="auth-input"><span>♙</span><input name="name" placeholder="<?= current_lang()==='th' ? 'ชื่อ-นามสกุล' : 'Full name' ?>" required value="<?= e($_POST['name'] ?? '') ?>"></div>
                </div>

                <div class="field">
                    <label><?= current_lang()==='th' ? 'แผนก' : 'Department' ?></label>
                    <div class="auth-input auth-select"><span>⌘</span>
                        <select name="department" required>
                            <option value=""><?= current_lang()==='th' ? 'เลือกแผนก' : 'Select department' ?></option>
                            <?php foreach ($deps as $d): $selected = (($_POST['department'] ?? '') === $d['name']) ? 'selected' : ''; ?>
                                <option value="<?= e($d['name']) ?>" <?= $selected ?>><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label><?= current_lang()==='th' ? 'รหัสผ่าน' : 'Password' ?></label>
                    <div class="auth-input password-input"><span>♧</span><input id="regPassword" type="password" name="password" placeholder="<?= current_lang()==='th' ? 'รหัสผ่าน' : 'Password' ?>" required><button type="button" class="password-toggle" data-target="regPassword">◉</button></div>
                </div>

                <div class="field">
                    <label><?= current_lang()==='th' ? 'ยืนยันรหัสผ่าน' : 'Confirm password' ?></label>
                    <div class="auth-input password-input"><span>♧</span><input id="regConfirm" type="password" name="confirm_password" placeholder="<?= current_lang()==='th' ? 'ยืนยันรหัสผ่าน' : 'Confirm password' ?>" required><button type="button" class="password-toggle" data-target="regConfirm">◉</button></div>
                </div>

                <button class="submit auth-submit" type="submit"><?= current_lang()==='th' ? 'สมัครสมาชิก' : 'Create account' ?> →</button>
            </form>

            <div class="login-link"><?= current_lang()==='th' ? 'มีบัญชีอยู่แล้ว?' : 'Already have an account?' ?> <a href="login.php"><?= current_lang()==='th' ? 'เข้าสู่ระบบ' : 'Sign in' ?></a></div>
        </div>
    </section>
</div>

<script>
document.querySelectorAll('.password-toggle').forEach(function(btn){
    btn.addEventListener('click', function(){
        var input = document.getElementById(this.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        this.textContent = input.type === 'password' ? '◉' : '◌';
    });
});
</script>
</body>
</html>
