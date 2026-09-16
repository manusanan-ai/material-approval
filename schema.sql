CREATE DATABASE IF NOT EXISTS material_approval CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE material_approval;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 username VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role_code ENUM('requester','dept_head','procurement','director','storekeeper','admin') NOT NULL DEFAULT 'requester',
 department VARCHAR(150) DEFAULT '',
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE materials (
 id INT AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(50) NOT NULL UNIQUE,
 name VARCHAR(200) NOT NULL,
 unit VARCHAR(50) NOT NULL,
 stock DECIMAL(12,2) NOT NULL DEFAULT 0,
 min_stock DECIMAL(12,2) NOT NULL DEFAULT 0,
 price DECIMAL(12,2) NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE requests (
 id INT AUTO_INCREMENT PRIMARY KEY,
 request_no VARCHAR(40) NOT NULL UNIQUE,
 requester_id INT NOT NULL,
 department VARCHAR(150) NOT NULL,
 purpose TEXT,
 status ENUM('draft','pending','approved','rejected','issued','cancelled') NOT NULL DEFAULT 'draft',
 current_level INT NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(requester_id) REFERENCES users(id)
);

CREATE TABLE request_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 request_id INT NOT NULL,
 material_id INT NOT NULL,
 qty DECIMAL(12,2) NOT NULL,
 note VARCHAR(255) DEFAULT '',
 FOREIGN KEY(request_id) REFERENCES requests(id) ON DELETE CASCADE,
 FOREIGN KEY(material_id) REFERENCES materials(id)
);

CREATE TABLE approval_steps (
 id INT AUTO_INCREMENT PRIMARY KEY,
 request_id INT NOT NULL,
 level_no INT NOT NULL,
 role_code ENUM('dept_head','procurement','director') NOT NULL,
 approver_id INT DEFAULT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 comment TEXT,
 acted_at DATETIME DEFAULT NULL,
 UNIQUE KEY uq_request_level(request_id,level_no),
 FOREIGN KEY(request_id) REFERENCES requests(id) ON DELETE CASCADE,
 FOREIGN KEY(approver_id) REFERENCES users(id)
);

CREATE TABLE issues (
 id INT AUTO_INCREMENT PRIMARY KEY,
 request_id INT NOT NULL,
 issuer_id INT NOT NULL,
 issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 note TEXT,
 FOREIGN KEY(request_id) REFERENCES requests(id),
 FOREIGN KEY(issuer_id) REFERENCES users(id)
);

CREATE TABLE issue_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 issue_id INT NOT NULL,
 material_id INT NOT NULL,
 qty DECIMAL(12,2) NOT NULL,
 FOREIGN KEY(issue_id) REFERENCES issues(id) ON DELETE CASCADE,
 FOREIGN KEY(material_id) REFERENCES materials(id)
);

CREATE TABLE audit_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 request_id INT NULL,
 user_id INT NOT NULL,
 action VARCHAR(100) NOT NULL,
 detail TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(request_id) REFERENCES requests(id) ON DELETE SET NULL,
 FOREIGN KEY(user_id) REFERENCES users(id)
);

INSERT INTO users(name,username,password_hash,role_code,department) VALUES
('ผู้ดูแลระบบ','admin', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'admin','บริหาร'),
('ผู้ขอเบิก','requester', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'requester','ไอที'),
('หัวหน้าแผนก','head', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'dept_head','ไอที'),
('จัดซื้อ','procure', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'procurement','พัสดุ'),
('ผู้อำนวยการ','director', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'director','บริหาร'),
('เจ้าหน้าที่คลัง','store', '$2y$12$am4ty8R/o/2xA3WjiFQvfecQgplSJ1d.aho6IU9SCOyAwk7yZ4S86', 'storekeeper','พัสดุ');

INSERT INTO materials(code,name,unit,stock,min_stock,price) VALUES
('MAT-001','กระดาษ A4 80 แกรม','รีม',100,20,120),
('MAT-002','ปากกาลูกลื่นสีน้ำเงิน','ด้าม',200,30,12),
('MAT-003','หมึกเครื่องพิมพ์','ตลับ',20,5,950),
('MAT-004','แฟ้มเอกสาร','เล่ม',80,15,45);

-- ผู้ใช้ตัวอย่างทุกบัญชีใช้รหัสผ่าน: password

-- Optional supplier / popular vendor module
CREATE TABLE vendors (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(200) NOT NULL,
 contact_name VARCHAR(150) DEFAULT '',
 phone VARCHAR(80) DEFAULT '',
 email VARCHAR(150) DEFAULT '',
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE purchase_orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 po_no VARCHAR(50) NOT NULL UNIQUE,
 vendor_id INT NOT NULL,
 total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 status ENUM('draft','ordered','received','cancelled') NOT NULL DEFAULT 'draft',
 ordered_at DATE DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(vendor_id) REFERENCES vendors(id)
);
CREATE TABLE stock_movements (
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
 FOREIGN KEY(material_id) REFERENCES materials(id) ON DELETE CASCADE
);
