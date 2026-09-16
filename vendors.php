<?php
require 'config.php'; role_required(['procurement','admin']); $pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();try{
 $act=$_POST['act']??'';
 if($act==='add_vendor'){$pdo->prepare('INSERT INTO vendors(name,contact_name,phone,email) VALUES(?,?,?,?)')->execute([trim($_POST['name']),trim($_POST['contact_name']??''),trim($_POST['phone']??''),trim($_POST['email']??'')]);}
 if($act==='edit_vendor'){$pdo->prepare('UPDATE vendors SET name=?,contact_name=?,phone=?,email=? WHERE id=?')->execute([trim($_POST['name']),trim($_POST['contact_name']??''),trim($_POST['phone']??''),trim($_POST['email']??''),(int)$_POST['id']]);}
 if($act==='delete_vendor'){$pdo->prepare('DELETE FROM purchase_orders WHERE vendor_id=?')->execute([(int)$_POST['id']]);$pdo->prepare('DELETE FROM vendors WHERE id=?')->execute([(int)$_POST['id']]);}
 if($act==='add_po'){$pdo->prepare('INSERT INTO purchase_orders(po_no,vendor_id,total_amount,status,ordered_at) VALUES(?,?,?,?,?)')->execute([trim($_POST['po_no']),(int)$_POST['vendor_id'],(float)$_POST['total_amount'],$_POST['status'],$_POST['ordered_at']?:null]);}
 flash('ok',current_lang()==='th'?'บันทึกข้อมูลเรียบร้อย':'Saved successfully');
}catch(Throwable $e){flash('error',$e->getMessage());}redirect('vendors.php');}
$msg=flash();$vendors=$pdo->query("SELECT v.*,COUNT(p.id) po_count,COALESCE(SUM(p.total_amount),0) total_spend FROM vendors v LEFT JOIN purchase_orders p ON p.vendor_id=v.id AND p.status<>'cancelled' GROUP BY v.id ORDER BY total_spend DESC,po_count DESC")->fetchAll();
?><!doctype html><html lang="<?=e(current_lang())?>"><head><?php include 'partials/head.php';?></head><body><?php include 'partials/nav.php';?><main class="container"><div class="page-head"><div><h2><?=current_lang()==='th'?'ผู้ขายยอดนิยม':'Top Vendors'?></h2><p><?=current_lang()==='th'?'จัดการผู้ขายและติดตามยอดสั่งซื้อ':'Manage suppliers and track purchase order value.'?></p></div></div><?php if($msg):?><div class="alert <?=e($msg[0])?>"><?=e($msg[1])?></div><?php endif;?>
<div class="simple-page-actions">
  <button type="button" class="btn-minimal" onclick="openVendorModal()">＋ <?=current_lang()==='th'?'เพิ่มผู้ขาย':'Add Vendor'?></button>
</div>
<div id="vendorModal" class="stock-modal" aria-hidden="true"><div class="stock-modal-card">
  <div class="stock-modal-head"><h3 id="vendorModalTitle"><?=current_lang()==='th'?'เพิ่มผู้ขาย':'Add Vendor'?></h3><button type="button" class="modal-close" onclick="closeVendorModal()">×</button></div>
  <form method="post" id="vendorForm">
    <input type="hidden" name="csrf" value="<?=e(csrf())?>">
    <input type="hidden" name="act" id="vendorAct" value="add_vendor">
    <input type="hidden" name="id" id="vendorId">
    <label><?=current_lang()==='th'?'ชื่อผู้ขาย':'Vendor name'?></label><input name="name" id="vendorName" required>
    <label><?=current_lang()==='th'?'ผู้ติดต่อ':'Contact'?></label><input name="contact_name" id="vendorContact">
    <label><?=current_lang()==='th'?'โทรศัพท์':'Phone'?></label><input name="phone" id="vendorPhone">
    <label>Email</label><input name="email" id="vendorEmail">
    <div class="actions"><button type="button" class="small-btn" onclick="closeVendorModal()"><?=current_lang()==='th'?'ยกเลิก':'Cancel'?></button><button><?=current_lang()==='th'?'บันทึก':'Save'?></button></div>
  </form>
</div></div>
<section class="vendor-grid"><article class="panel po-panel"><div class="panel-head"><h3><?=current_lang()==='th'?'บันทึกคำสั่งซื้อ':'Record Purchase Order'?></h3></div><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="add_po"><div class="row"><div><label>PO No.</label><input name="po_no" required placeholder="PO-2026-006"></div><div><label><?=current_lang()==='th'?'ผู้ขาย':'Vendor'?></label><select name="vendor_id"><?php foreach($vendors as $v):?><option value="<?=$v['id']?>"><?=e($v['name'])?></option><?php endforeach;?></select></div><div><label><?=current_lang()==='th'?'ยอดเงิน':'Amount'?></label><input name="total_amount" type="number" step="1" min="0" required></div><div><label><?=current_lang()==='th'?'สถานะ':'Status'?></label><select name="status"><option value="ordered">Ordered</option><option value="received">Received</option><option value="draft">Draft</option></select></div><div><label><?=current_lang()==='th'?'วันที่':'Date'?></label><input name="ordered_at" type="date"></div></div><br><button class="success">✓ <?=current_lang()==='th'?'บันทึก PO':'Save PO'?></button></form></article></section>
<article class="panel"><div class="panel-head"><h3><?=current_lang()==='th'?'ผู้ขายยอดนิยม':'Top Vendors by Spend'?></h3></div><div class="vendor-list"><?php if(!$vendors):?><div class="empty-state"><?=current_lang()==='th'?'ยังไม่มีผู้ขาย':'No vendors yet'?></div><?php endif;?><?php foreach($vendors as $i=>$v):?><div class="vendor-row">
<div class="rank"><?=$i+1?></div>
<div class="vendor-info"><b><?=e($v['name'])?></b><span><?=e($v['contact_name'])?><?= $v['phone']?' · '.e($v['phone']):''?></span></div>
<div class="vendor-bar"><i style="width:<?=max(8,min(100,($vendors[0]['total_spend']>0?$v['total_spend']/$vendors[0]['total_spend']*100:0)))?>%"></i></div>
<div class="vendor-total"><b>฿ <?=fmt_number($v['total_spend'])?></b><small><?=$v['po_count']?> PO</small></div>
<div class="action-menu"><button type="button" class="action-toggle" onclick="toggleVendorMenu(this)">⋮</button><div class="action-dropdown">
<button type="button" onclick='editVendor(<?=json_encode(["id"=>(int)$v["id"],"name"=>$v["name"],"contact"=>$v["contact_name"],"phone"=>$v["phone"],"email"=>$v["email"]], JSON_UNESCAPED_UNICODE)?>)'>✎ <?=current_lang()==='th'?'แก้ไข':'Edit'?></button>
<button type="button" class="delete-action" onclick='openVendorDelete(<?=json_encode(["id"=>(int)$v["id"],"name"=>$v["name"]], JSON_UNESCAPED_UNICODE)?>)'>⌫ <?=current_lang()==='th'?'ลบผู้ขาย':'Delete'?></button>
</div></div></div><?php endforeach;?></div></article>

<div id="vendorDeleteModal" class="delete-modal" aria-hidden="true">
  <div class="delete-modal-card" role="dialog" aria-modal="true" aria-labelledby="vendorDeleteTitle">
    <button type="button" class="delete-modal-close" onclick="closeVendorDelete()" aria-label="Close">×</button>
    <div class="delete-icon-wrap">🗑</div>
    <h3 id="vendorDeleteTitle"><?=current_lang()==='th'?'ยืนยันการลบผู้ขาย':'Delete vendor?'?></h3>
    <p><?=current_lang()==='th'?'คุณกำลังจะลบ':'You are about to delete'?> <b id="vendorDeleteName"></b><br><span><?=current_lang()==='th'?'ข้อมูลผู้ขายและคำสั่งซื้อที่เกี่ยวข้องจะถูกลบ':'Related purchase orders will also be removed.'?></span></p>
    <form method="post" class="delete-confirm-form">
      <input type="hidden" name="csrf" value="<?=e(csrf())?>">
      <input type="hidden" name="act" value="delete_vendor">
      <input type="hidden" name="id" id="vendorDeleteId">
      <button type="button" class="delete-cancel-btn" onclick="closeVendorDelete()"><?=current_lang()==='th'?'ยกเลิก':'Cancel'?></button>
      <button type="submit" class="delete-confirm-btn"><?=current_lang()==='th'?'ยืนยันการลบ':'Delete'?></button>
    </form>
  </div>
</div>
<script>
function toggleVendorMenu(b){document.querySelectorAll('.action-menu').forEach(x=>{if(x!==b.parentElement)x.classList.remove('open')});b.parentElement.classList.toggle('open')}
function openVendorModal(){document.getElementById('vendorForm').reset();document.getElementById('vendorAct').value='add_vendor';document.getElementById('vendorId').value='';document.getElementById('vendorModalTitle').textContent='<?=current_lang()==='th'?'เพิ่มผู้ขาย':'Add Vendor'?>';document.getElementById('vendorModal').classList.add('show')}
function closeVendorModal(){document.getElementById('vendorModal').classList.remove('show')}
function openVendorDelete(v){document.getElementById('vendorDeleteId').value=v.id;document.getElementById('vendorDeleteName').textContent=v.name;document.getElementById('vendorDeleteModal').classList.add('show');document.getElementById('vendorDeleteModal').setAttribute('aria-hidden','false')}function closeVendorDelete(){document.getElementById('vendorDeleteModal').classList.remove('show');document.getElementById('vendorDeleteModal').setAttribute('aria-hidden','true')}
function editVendor(v){document.getElementById('vendorAct').value='edit_vendor';document.getElementById('vendorId').value=v.id;document.getElementById('vendorName').value=v.name||'';document.getElementById('vendorContact').value=v.contact||'';document.getElementById('vendorPhone').value=v.phone||'';document.getElementById('vendorEmail').value=v.email||'';document.getElementById('vendorModalTitle').textContent='<?=current_lang()==='th'?'แก้ไขผู้ขาย':'Edit Vendor'?>';document.getElementById('vendorModal').classList.add('show')}
document.addEventListener('click',e=>{if(!e.target.closest('.action-menu'))document.querySelectorAll('.action-menu').forEach(x=>x.classList.remove('open'));if(e.target.id==='vendorDeleteModal')closeVendorDelete();if(e.target.id==='vendorModal')closeVendorModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeVendorDelete();closeVendorModal()}});
</script></main></div></div></body></html>
