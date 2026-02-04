<?php
include 'db.php';

// [แก้ไข] เปลี่ยนสิทธิ์: เฉพาะ Approver เท่านั้นที่เข้าได้ (Admin เข้าไม่ได้)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'approver') {
    echo "<script>alert('❌ หน้านี้สำหรับฝ่าย Approval เท่านั้น'); window.location='dashboard.php';</script>";
    exit();
}

// 1. เพิ่มสินค้าใหม่
if (isset($_POST['add_product'])) {
    $n = $_POST['p_name'];
    $i = $_POST['p_icon']; // รับค่า Emoji
    if (empty($i)) $i = ''; // ถ้าไม่ใส่ให้เป็นค่าว่าง
    
    $stmt = $conn->prepare("INSERT INTO products (name, icon) VALUES (?, ?)");
    $stmt->bind_param("ss", $n, $i);
    
    if($stmt->execute()){
        echo "<script>alert('✅ เพิ่มสินค้าเรียบร้อย'); window.location='manage_products.php';</script>";
    }
}

// 2. ลบสินค้า
if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    $conn->query("DELETE FROM products WHERE id=$id");
    echo "<script>alert('🗑️ ลบสินค้าแล้ว'); window.location='manage_products.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
             <img src="logo.png" width="40" class="me-3">
             <h2 class="text-warning m-0">📦 จัดการรายการสินค้า (Approval Dept.)</h2>
        </div>
        <a href="dashboard.php" class="btn btn-secondary">⬅️ กลับ Dashboard</a>
    </div>

    <div class="card mb-4 border-warning shadow-sm">
        <div class="card-header bg-warning text-dark">➕ เพิ่มสินค้าใหม่</div>
        <div class="card-body">
            <form method="post" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="p_name" class="form-control" placeholder="ชื่อสินค้า (เช่น ลาเต้)" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="p_icon" class="form-control" placeholder="ใส่ Emoji (กด Windows + .)">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_product" class="btn btn-primary w-100">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 15%;">ไอคอน</th>
                        <th style="width: 65%;">ชื่อสินค้า</th>
                        <th style="width: 20%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM products");
                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td style="font-size: 1.5rem;"><?php echo $row['icon']; ?></td>
                        <td class="text-start"><?php echo $row['name']; ?></td>
                        <td>
                            <a href="manage_products.php?del_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบสินค้านี้จริงหรือไม่?');">ลบ</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" class="text-muted">ยังไม่มีสินค้า</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>