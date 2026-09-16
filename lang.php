<?php
function current_lang(): string {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['th','en'], true)) $_SESSION['lang'] = $_GET['lang'];
    return $_SESSION['lang'] ?? 'th';
}
function t(string $key, ?string $fallback=null): string {
    static $dict = [
      'dashboard'=>['th'=>'หน้าหลัก','en'=>'Dashboard'], 'requests'=>['th'=>'รายการใบเบิก','en'=>'Requisitions'],
      'new_request'=>['th'=>'สร้างใบเบิก','en'=>'New Requisition'], 'approvals'=>['th'=>'อนุมัติรายการ','en'=>'Approvals'],
      'issue'=>['th'=>'จ่ายพัสดุ','en'=>'Issue Materials'], 'materials'=>['th'=>'คลังพัสดุ','en'=>'Inventory'],
      'reports'=>['th'=>'รายงาน','en'=>'Reports'], 'settings'=>['th'=>'ตั้งค่า','en'=>'Settings'], 'main_menu'=>['th'=>'เมนูหลัก','en'=>'MAIN MENU'],
      'system'=>['th'=>'ระบบ','en'=>'SYSTEM'], 'soon'=>['th'=>'เร็วๆ นี้','en'=>'Coming soon'], 'logout'=>['th'=>'ออกจากระบบ','en'=>'Sign out'],
      'search'=>['th'=>'ค้นหา (Search...)','en'=>'Search...'], 'language'=>['th'=>'ภาษา','en'=>'Language'],
      'dashboard_title'=>['th'=>'แดชบอร์ดภาพรวม','en'=>'Procurement Dashboard'], 'dashboard_sub'=>['th'=>'ติดตามสถานะการเบิกจ่ายพัสดุ คำสั่งซื้อ และการอนุมัติอย่างมีประสิทธิภาพ','en'=>'Monitor material requests, approvals and inventory in one place.'],
      'total_requests'=>['th'=>'คำขอเบิกทั้งหมด','en'=>'Total Requisitions'], 'pending'=>['th'=>'รออนุมัติ','en'=>'Pending Approval'],
      'approved'=>['th'=>'อนุมัติแล้ว','en'=>'Approved'], 'low_stock'=>['th'=>'พัสดุใกล้หมด','en'=>'Low Stock'],
      'recent_requests'=>['th'=>'รายการเบิกล่าสุด','en'=>'Recent Requisitions'], 'view_all'=>['th'=>'ดูทั้งหมด','en'=>'View all'],
      'quick_actions'=>['th'=>'เมนูด่วน','en'=>'Quick Actions'], 'create_request'=>['th'=>'สร้างใบเบิกใหม่','en'=>'Create Request'],
      'track_status'=>['th'=>'ติดตามสถานะ','en'=>'Track status'], 'inventory'=>['th'=>'คลังพัสดุ','en'=>'Inventory'],
      'approve_items'=>['th'=>'อนุมัติรายการ','en'=>'Approve items'], 'status_overview'=>['th'=>'ภาพรวมสถานะ','en'=>'Status Overview'],
      'approval_rate'=>['th'=>'อนุมัติแล้ว','en'=>'Approved'], 'issued_rate'=>['th'=>'จ่ายพัสดุแล้ว','en'=>'Issued'], 'stock_items'=>['th'=>'รายการพัสดุ','en'=>'Active items'],
      'request_no'=>['th'=>'เลขที่คำขอ','en'=>'Request No.'], 'requester'=>['th'=>'ผู้ขอ','en'=>'Requester'], 'department'=>['th'=>'แผนก','en'=>'Department'], 'date'=>['th'=>'วันที่','en'=>'Date'], 'status'=>['th'=>'สถานะ','en'=>'Status'], 'details'=>['th'=>'รายละเอียด','en'=>'Details'],
      'purpose'=>['th'=>'วัตถุประสงค์','en'=>'Purpose'], 'items'=>['th'=>'รายการพัสดุ','en'=>'Requested Items'], 'material'=>['th'=>'พัสดุ','en'=>'Material'], 'quantity'=>['th'=>'จำนวน','en'=>'Quantity'], 'unit'=>['th'=>'หน่วย','en'=>'Unit'], 'note'=>['th'=>'หมายเหตุ','en'=>'Note'], 'submit'=>['th'=>'ส่งอนุมัติ','en'=>'Submit for Approval'], 'add_item'=>['th'=>'เพิ่มรายการ','en'=>'Add Item'],
      'current_step'=>['th'=>'ขั้นปัจจุบัน','en'=>'Current Step'], 'view'=>['th'=>'ดูรายละเอียด','en'=>'View Details'], 'comment'=>['th'=>'ความเห็น','en'=>'Comment'], 'approve'=>['th'=>'อนุมัติ','en'=>'Approve'], 'reject'=>['th'=>'ไม่อนุมัติ','en'=>'Reject'],
      'approval_workflow'=>['th'=>'ลำดับการอนุมัติ','en'=>'Approval Workflow'], 'history'=>['th'=>'ประวัติรายการ','en'=>'Activity History'],
      'code'=>['th'=>'รหัส','en'=>'Code'], 'name'=>['th'=>'ชื่อพัสดุ','en'=>'Material Name'], 'stock'=>['th'=>'คงเหลือ','en'=>'Stock'], 'minimum'=>['th'=>'ขั้นต่ำ','en'=>'Minimum'], 'price'=>['th'=>'ราคา','en'=>'Price'], 'normal'=>['th'=>'ปกติ','en'=>'Normal'], 'add_material'=>['th'=>'เพิ่มพัสดุ','en'=>'Add Material'],
      'ready_to_issue'=>['th'=>'ใบเบิกที่พร้อมจ่าย','en'=>'Ready to Issue'], 'confirm_issue'=>['th'=>'ยืนยันจ่ายพัสดุ','en'=>'Confirm Issue'], 'issue_note'=>['th'=>'หมายเหตุการจ่าย','en'=>'Issue note'],
      'login'=>['th'=>'เข้าสู่ระบบ','en'=>'Sign in'], 'username'=>['th'=>'ชื่อผู้ใช้','en'=>'Username'], 'password'=>['th'=>'รหัสผ่าน','en'=>'Password'],
      'login_sub'=>['th'=>'กรุณาเข้าสู่ระบบเพื่อใช้งานระบบบริหารจัดการพัสดุ','en'=>'Sign in to access the material management system.'],
      'empty_requests'=>['th'=>'ยังไม่มีรายการคำขอ','en'=>'No requisitions found'], 'no_pending'=>['th'=>'ไม่มีรายการรออนุมัติ','en'=>'No pending approvals'],
      'no_ready'=>['th'=>'ไม่มีใบเบิกที่พร้อมจ่าย','en'=>'No approved requisitions ready to issue'],
    ];
    $lang=current_lang();
    return $dict[$key][$lang] ?? $fallback ?? $key;
}
function status_text(string $status): string {
  $m=['draft'=>['th'=>'แบบร่าง','en'=>'Draft'],'pending'=>['th'=>'รออนุมัติ','en'=>'Pending'],'approved'=>['th'=>'อนุมัติแล้ว','en'=>'Approved'],'rejected'=>['th'=>'ไม่อนุมัติ','en'=>'Rejected'],'issued'=>['th'=>'จ่ายแล้ว','en'=>'Issued'],'cancelled'=>['th'=>'ยกเลิก','en'=>'Cancelled']];
  return $m[$status][current_lang()] ?? $status;
}
function role_text(string $role): string {
  $m=['admin'=>['th'=>'ผู้ดูแลระบบ','en'=>'Administrator'],'requester'=>['th'=>'ผู้ขอเบิก','en'=>'Requester'],'dept_head'=>['th'=>'หัวหน้าแผนก','en'=>'Department Head'],'procurement'=>['th'=>'ฝ่ายจัดซื้อ','en'=>'Procurement'],'director'=>['th'=>'ผู้อำนวยการ','en'=>'Director'],'storekeeper'=>['th'=>'เจ้าหน้าที่คลัง','en'=>'Storekeeper']];
  return $m[$role][current_lang()] ?? $role;
}
