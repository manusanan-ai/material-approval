# Material Flow — Procurement & Material Approval

ระบบบริหารจัดการเบิกจ่ายพัสดุและอนุมัติตามลำดับขั้น (Multi-level Approval Workflow)

## สิ่งที่ปรับปรุงในเวอร์ชัน UI ใหม่

- 🤎 ธีมสีน้ำตาลอ่อน / ครีมแบบ Minimal Enterprise
- 🇹🇭 🇬🇧 สลับภาษาไทยและอังกฤษจาก Topbar
- 🧭 Sidebar และ Topbar แบบ Modern Dashboard
- 📊 Dashboard Cards, Progress, Recent Requisitions และ Quick Actions
- 🔄 แสดง Approval Workflow แบบ 3 ระดับ
- 📦 ปรับหน้าคลังพัสดุ, ใบเบิก, อนุมัติ และจ่ายพัสดุให้มี UI เดียวกัน
- 📱 Responsive สำหรับ Tablet และ Mobile
- 🚪 เพิ่มระบบ Logout ที่สมบูรณ์

## การติดตั้ง

1. สร้างฐานข้อมูลและนำเข้า `schema.sql`
2. ตรวจสอบค่า DB ใน `config.php`
3. วางโปรเจกต์ใน Web Server ที่รองรับ PHP 8+
4. เปิด `login.php`

## ภาษา

- `?lang=th` ภาษาไทย
- `?lang=en` English

ภาษาที่เลือกจะถูกเก็บไว้ใน PHP Session.


## Updated role permissions
- Procurement (procurement): can issue approved materials and deduct stock.
- Storekeeper (storekeeper): inventory management only; cannot access issue.php.
- Admin: full access including issuing materials.


### อัปเดตคลังพัสดุ
- ราคาพัสดุรองรับทศนิยม 2 ตำแหน่ง เช่น 120.50 บาท (สตางค์)
- จำนวนเริ่มต้นและจำนวนเติมสต็อกรองรับทศนิยม 2 ตำแหน่ง
- ราคาที่แสดงในตารางคลังจะแสดง 2 ตำแหน่ง เช่น ฿ 120.50
