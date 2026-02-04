<?php
include 'db.php';

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

// [แก้ไข] เปลี่ยนสิทธิ์: ให้ Approver เป็นคนจัดการ (แก้ไข/ลบ) แทน Admin
if ($_SESSION['role'] == 'approver') {
    
    // ลบออเดอร์
    if (isset($_GET['del_order'])) {
        $oid = $_GET['del_order'];
        $conn->query("DELETE FROM orders WHERE id=$oid");
        echo "<script>window.location='history.php';</script>";
    }
    
    // อัปเดตข้อมูล
    if (isset($_POST['update_order'])) {
        $edit_id = $_POST['edit_id'];
        $edit_product = $_POST['edit_product'];
        $edit_qty = $_POST['edit_qty'];
        $conn->query("UPDATE orders SET product_name='$edit_product', quantity='$edit_qty' WHERE id=$edit_id");
        echo "<script>alert('แก้ไขข้อมูลเรียบร้อย'); window.location='history.php';</script>";
    }
}

$edit_data = null;
// [แก้ไข] เช็คสิทธิ์ตรงนี้ด้วย
if (isset($_GET['edit_order']) && $_SESSION['role'] == 'approver') {
    $eid = $_GET['edit_order'];
    $res = $conn->query("SELECT * FROM orders WHERE id=$eid");
    $edit_data = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการสั่งซื้อ</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" width="40" class="me-2">
            <h2>✅ ประวัติการอนุมัติ (Approved)</h2>
        </div>
        <a href="<?php echo ($_SESSION['role'] == 'user') ? 'order.php' : 'dashboard.php'; ?>" class="btn btn-secondary">⬅️ กลับหน้าหลัก</a>
    </div>

    <?php if ($edit_data): ?>
    <div class="card p-3 mb-4 bg-light border-warning">
        <h4>📝 แก้ไขรายการ ID: <?php echo $edit_data['id']; ?></h4>
        <form method="post">
            <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
            <div class="row">
                <div class="col-md-4"><label>สินค้า</label><input type="text" name="edit_product" class="form-control" value="<?php echo $edit_data['product_name']; ?>" required></div>
                <div class="col-md-2"><label>จำนวน</label><input type="number" name="edit_qty" class="form-control" value="<?php echo $edit_data['quantity']; ?>" required></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" name="update_order" class="btn btn-success w-100">บันทึก</button></div>
                <div class="col-md-2 d-flex align-items-end"><a href="history.php" class="btn btn-outline-danger w-100">ยกเลิก</a></div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>เวลา</th>
                <th>ผู้สั่ง</th>
                <th>สินค้า</th>
                <th>จำนวน</th>
                <?php if($_SESSION['role']=='approver') echo '<th>จัดการ</th>'; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM orders WHERE status = 'approved' ORDER BY id DESC";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['order_time']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><span class="badge bg-primary"><?php echo $row['quantity']; ?></span></td>
                
                <?php if($_SESSION['role'] == 'approver'): ?>
                <td>
                    <a href="history.php?edit_order=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                    <a href="history.php?del_order=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('ลบ?');">ลบ</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>