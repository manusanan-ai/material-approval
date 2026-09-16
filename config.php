<?php
declare(strict_types=1);
session_start();
require_once __DIR__.'/lang.php';
current_lang();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'material_approval';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}

/** Create optional inventory/vendor tables automatically for existing databases. */
function ensure_feature_tables(): void {
    static $done = false; if ($done) return; $done = true;
    $pdo = db();
    $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL UNIQUE,
        code VARCHAR(50) DEFAULT NULL UNIQUE,
        active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    if ((int)$pdo->query('SELECT COUNT(*) FROM departments')->fetchColumn()===0) {
        $pdo->exec("INSERT INTO departments(name,code) VALUES ('บริหาร','ADMIN'),('ไอที','IT'),('พัสดุ','PROC'),('การเงิน','FIN'),('บุคคล','HR')");
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS vendors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        contact_name VARCHAR(150) DEFAULT '',
        phone VARCHAR(80) DEFAULT '',
        email VARCHAR(150) DEFAULT '',
        active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS purchase_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        po_no VARCHAR(50) NOT NULL UNIQUE,
        vendor_id INT NOT NULL,
        total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
        status ENUM('draft','ordered','received','cancelled') NOT NULL DEFAULT 'draft',
        ordered_at DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_po_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS stock_movements (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        material_id INT NOT NULL,
        movement_type ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
        qty DECIMAL(12,2) NOT NULL,
        balance_after DECIMAL(12,2) NOT NULL,
        reference_type VARCHAR(40) DEFAULT '',
        reference_id INT DEFAULT NULL,
        user_id INT DEFAULT NULL,
        note VARCHAR(255) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_stock_material (material_id),
        CONSTRAINT fk_stock_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $count=(int)$pdo->query('SELECT COUNT(*) FROM vendors')->fetchColumn();
    if($count===0){
      $pdo->exec("INSERT INTO vendors(name,contact_name,phone,email) VALUES
        ('บริษัท เอเชีย ออฟฟิศ จำกัด','สมชาย ใจดี','02-111-1111','sales@asiaoffice.local'),
        ('Modern Office Systems','Nina Brown','02-222-2222','sales@modernoffice.local'),
        ('TechOne Services','David Lee','02-333-3333','sales@techone.local'),
        ('Campus Furniture Co.','Anan K.','02-444-4444','sales@campus.local'),
        ('Book World','May P.','02-555-5555','sales@bookworld.local')");
      $pdo->exec("INSERT INTO purchase_orders(po_no,vendor_id,total_amount,status,ordered_at) VALUES
        ('PO-2026-001',1,124500,'received','2026-01-10'),('PO-2026-002',3,86000,'received','2026-02-12'),
        ('PO-2026-003',2,62500,'ordered','2026-03-01'),('PO-2026-004',4,48000,'received','2026-04-05'),('PO-2026-005',5,31500,'ordered','2026-05-14')");
    }
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
/** Format quantities and amounts without unnecessary trailing .00 */
function fmt_number($value): string {
    $n=(float)$value;
    if (abs($n-round($n)) < 0.000001) return number_format($n,0);
    return rtrim(rtrim(number_format($n,2,'.',','),'0'),'.');
}
function redirect(string $url): never { header("Location: $url"); exit; }
function flash(?string $type=null, ?string $message=null) {
    if ($type !== null) $_SESSION['flash'] = [$type, $message];
    $x = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $x;
}
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('CSRF token ไม่ถูกต้อง');
    }
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function login_required(): void { if (!user()) redirect('login.php'); }
function role_required(array $roles): void {
    login_required();
    if (!in_array(user()['role_code'], $roles, true)) { require __DIR__.'/access_denied.php'; exit; }
}
ensure_feature_tables();

function log_action(?int $requestId, int $userId, string $action, string $detail=''): void {
    $s = db()->prepare("INSERT INTO audit_logs(request_id,user_id,action,detail) VALUES(?,?,?,?)");
    $s->execute([$requestId,$userId,$action,$detail]);
}
