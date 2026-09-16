<?php
require 'config.php'; role_required(['requester','storekeeper','procurement','admin']);
$canManage=in_array(user()['role_code'],['storekeeper','admin'],true); // Procurement can view inventory only
$pdo = db();
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf(); if(!$canManage){exit('ไม่มีสิทธิ์');} $a=$_POST['act']??'';
try{
if($a==='add'){$pdo->prepare("INSERT INTO materials(code,name,unit,stock,min_stock,price) VALUES(?,?,?,?,?,?)")->execute([trim($_POST['code']),trim($_POST['name']),trim($_POST['unit']),(float)$_POST['stock'],(float)$_POST['min_stock'],(float)$_POST['price']]);}
if($a==='edit'){$pdo->prepare("UPDATE materials SET code=?,name=?,unit=?,min_stock=?,price=? WHERE id=?")->execute([trim($_POST['code']),trim($_POST['name']),trim($_POST['unit']),(float)$_POST['min_stock'],(float)$_POST['price'],(int)$_POST['id']]);}
if($a==='delete'){$id=(int)$_POST['id'];$pdo->prepare("DELETE FROM stock_movements WHERE material_id=?")->execute([$id]);$pdo->prepare("DELETE FROM materials WHERE id=?")->execute([$id]);}
if($a==='restock'){
  $id=(int)$_POST['id']; $q=(float)$_POST['qty'];
  if($q<=0) throw new Exception('จำนวนเติมต้องมากกว่า 0');
  $pdo->beginTransaction();
  $st=$pdo->prepare("SELECT stock FROM materials WHERE id=? FOR UPDATE"); $st->execute([$id]); $old=$st->fetchColumn();
  if($old===false) throw new Exception('ไม่พบพัสดุ');
  $new=(float)$old+$q;
  $pdo->prepare("UPDATE materials SET stock=? WHERE id=?")->execute([$new,$id]);
  $pdo->prepare("INSERT INTO stock_movements(material_id,movement_type,qty,balance_after,reference_type,reference_id,user_id,note) VALUES(?,?,?,?,?,?,?,?)")->execute([$id,'IN',$q,$new,'RESTOCK',$id,user()['id'],'เติมสต็อก']);
  $pdo->commit();
}
flash('ok','บันทึกข้อมูลเรียบร้อย');
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error',$e->getMessage());} redirect('materials.php');}
$msg=flash();
$filter=$_GET['filter']??'all';
$sql="SELECT * FROM materials";
if($filter==='low') $sql.=" WHERE stock > 0 AND stock < 10";
elseif($filter==='out') $sql.=" WHERE stock <= 0";
$sql.=" ORDER BY id DESC";
$rows=$pdo->query($sql)->fetchAll();$edit=null;if(isset($_GET['edit'])&&$canManage){$st=$pdo->prepare("SELECT * FROM materials WHERE id=?");$st->execute([(int)$_GET['edit']]);$edit=$st->fetch();}
?><!doctype html><html><head><?php include 'partials/head.php';?></head><body><?php include 'partials/nav.php';?><main class="container"><div class="page-head"><div><h2>📦 <?=current_lang()==='th'?'คลังพัสดุ':'Inventory'?></h2><p>เพิ่ม แก้ไข ลบ และเติมยอดคงเหลือ</p></div><div class="inventory-filters"><a href="materials.php" class="small-btn">ทั้งหมด</a><a href="materials.php?filter=low" class="small-btn">ใกล้หมด (&lt;10)</a><a href="materials.php?filter=out" class="small-btn">หมดแล้ว</a></div></div><?php if($msg):?><div class="alert <?=e($msg[0])?>"><?=e($msg[1])?></div><?php endif;?>
<?php if($canManage):?><form method="post" class="panel inventory-form"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="<?=$edit?'edit':'add'?>"><?php if($edit):?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif;?><div class="panel-head"><h3><?=$edit?'แก้ไขพัสดุ':'เพิ่มพัสดุ'?></h3></div><div class="row"><div><label>รหัส</label><input name="code" required value="<?=e($edit['code']??'')?>"></div><div><label>ชื่อพัสดุ</label><input name="name" required value="<?=e($edit['name']??'')?>"></div><div><label>หน่วย</label><input name="unit" required value="<?=e($edit['unit']??'')?>"></div><?php if(!$edit):?><div><label>ยอดคงเหลือ</label><input name="stock" type="number" step="0.01" min="0" inputmode="decimal" value="0"></div><?php endif;?><div><label>ขั้นต่ำ</label><input name="min_stock" type="number" step="0.01" min="0" inputmode="decimal" value="<?=e($edit['min_stock']??0)?>"></div><div><label>ราคา</label><input name="price" type="number" step="0.01" min="0" inputmode="decimal" placeholder="เช่น 120.50" value="<?=e($edit['price']??0)?>"></div></div><br><button><?=$edit?'บันทึกการแก้ไข':'＋ เพิ่มพัสดุ'?></button> <?php if($edit):?><a class="small-btn" href="materials.php">ยกเลิก</a><?php endif;?></form><?php endif;?>
<?php $lowRows=array_filter($rows,fn($x)=>(float)$x['stock']>0 && (float)$x['stock']<10); $outRows=array_filter($rows,fn($x)=>(float)$x['stock']<=0); if($lowRows||$outRows):?><div class="alert error">⚠️ พัสดุใกล้หมด <?=count($lowRows)?> รายการ<?=count($outRows)?' · หมดแล้ว '.count($outRows).' รายการ':''?> กรุณาตรวจสอบและเติมสต็อก</div><?php endif;?><div class="panel"><div class="table-wrap"><table class="table inventory-table"><thead><tr><th>รหัส</th><th>ชื่อพัสดุ</th><th>หน่วย</th><th>ยอดคงเหลือ</th><th>ขั้นต่ำ</th><th>ราคา</th><th class="right">จัดการ</th></tr></thead><tbody><?php foreach($rows as $m):?><tr><td><?=e($m['code'])?></td><td><?=e($m['name'])?></td><td><?=e($m['unit'])?></td><td><?php $isOut=((float)$m['stock']<=0); $isLow=(!$isOut && (float)$m['stock']<10);?><b class="<?= ($isLow||$isOut)?'stock-low':'' ?>"><?=fmt_number($m['stock'])?></b><?php if($isOut):?> <span class="out-badge">หมดแล้ว</span><?php elseif($isLow):?> <span class="low-badge">ใกล้หมด</span><?php endif;?></td><td><?=fmt_number($m['min_stock'])?></td><td><b class="price-value">฿ <?=number_format((float)$m['price'],2,'.',',')?></b></td><td class="right"><?php if($canManage):?><div class="action-menu"><button type="button" class="action-toggle" aria-label="จัดการ" onclick="toggleAction(this)">⋮</button><div class="action-dropdown"><button type="button" onclick="openRestock(<?=$m['id']?>,'<?=e($m['name'])?>')">＋ เติมยอด</button><a href="materials.php?edit=<?=$m['id']?>">✎ แก้ไข</a><button type="button" class="delete-action" onclick='openDeleteModal(<?=json_encode(["id"=>(int)$m["id"],"name"=>$m["name"]], JSON_UNESCAPED_UNICODE)?>)'>⌫ ลบพัสดุ</button></div></div><?php endif;?></td></tr><?php endforeach;?></tbody></table></div></div>
<div id="restockModal" class="stock-modal"><div class="stock-modal-card"><div class="stock-modal-head"><h3>เติมยอดคงเหลือ</h3><button class="modal-close" type="button" onclick="closeRestock()">×</button></div><p id="restockName" style="margin:0 0 12px;color:#8a7b70;font-size:12px"></p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="restock"><input type="hidden" name="id" id="restockId"><label>จำนวนที่ต้องการเติม</label><input name="qty" type="number" min="0.01" step="0.01" inputmode="decimal" placeholder="เช่น 10 หรือ 2.50" required autofocus><div class="actions"><button type="button" class="small-btn" onclick="closeRestock()">ยกเลิก</button><button type="submit">บันทึก</button></div></form></div></div>

<div id="deleteModal" class="delete-modal" aria-hidden="true">
  <div class="delete-modal-card" role="dialog" aria-modal="true" aria-labelledby="deleteTitle">
    <button type="button" class="delete-modal-close" onclick="closeDeleteModal()" aria-label="ปิด">×</button>
    <div class="delete-icon-wrap">🗑</div>
    <h3 id="deleteTitle">ยืนยันการลบพัสดุ</h3>
    <p>คุณกำลังจะลบ <b id="deleteItemName"></b><br><span>ข้อมูลนี้จะถูกลบออกจากคลังอย่างถาวร</span></p>
    <form method="post" class="delete-confirm-form">
      <input type="hidden" name="csrf" value="<?=e(csrf())?>">
      <input type="hidden" name="act" value="delete">
      <input type="hidden" name="id" id="deleteItemId">
      <button type="button" class="delete-cancel-btn" onclick="closeDeleteModal()">ยกเลิก</button>
      <button type="submit" class="delete-confirm-btn">ยืนยันการลบ</button>
    </form>
  </div>
</div>
<script>function toggleAction(b){document.querySelectorAll('.action-menu').forEach(x=>{if(x!==b.parentElement)x.classList.remove('open')});b.parentElement.classList.toggle('open')}function openRestock(id,name){document.getElementById('restockId').value=id;document.getElementById('restockName').textContent=name;document.getElementById('restockModal').classList.add('show')}function closeRestock(){document.getElementById('restockModal').classList.remove('show')}function openDeleteModal(item){document.getElementById('deleteItemId').value=item.id;document.getElementById('deleteItemName').textContent=item.name;document.getElementById('deleteModal').classList.add('show');document.getElementById('deleteModal').setAttribute('aria-hidden','false')}function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('show');document.getElementById('deleteModal').setAttribute('aria-hidden','true')}document.addEventListener('click',e=>{if(!e.target.closest('.action-menu'))document.querySelectorAll('.action-menu').forEach(x=>x.classList.remove('open'));if(e.target.id==='restockModal')closeRestock();if(e.target.id==='deleteModal')closeDeleteModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeRestock();closeDeleteModal()}})</script>
</main></div></div></body></html>