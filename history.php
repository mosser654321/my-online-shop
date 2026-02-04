<?php
include 'db.php';

// 1. เช็คสิทธิ์
if (!isset($_COOKIE['user_id'])) {
    header("Location: index.php");
    exit();
}

$my_role = $_COOKIE['role'];
$my_name = $_COOKIE['name'];

// --- 🔥 ส่วนที่เพิ่ม: ระบบลบประวัติ (เฉพาะ Approver/Admin) ---
if (isset($_GET['delete_id'])) {
    // เช็คสิทธิ์ก่อนลบ เพื่อความปลอดภัย
    if ($my_role == 'approver' || $my_role == 'admin') {
        $id = $_GET['delete_id'];
        $conn->query("DELETE FROM orders WHERE id = $id");
        // รีโหลดหน้าเว็บเพื่อเคลียร์ค่า
        echo "<script>window.location='history.php';</script>";
    } else {
        echo "<script>alert('❌ คุณไม่มีสิทธิ์ลบรายการนี้');</script>";
    }
}
// -----------------------------------------------------------

// --- กำหนดลิงก์ย้อนกลับ ---
if ($my_role == 'user') {
    $back_link = 'order.php';     
} else {
    $back_link = 'dashboard.php'; 
}

// 2. ดึงข้อมูล
if ($my_role == 'user') {
    // User: ดูเฉพาะของตัวเอง
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_name = ? ORDER BY order_time DESC");
    $stmt->bind_param("s", $my_name);
} else {
    // Approver/Admin: ดูรายการที่ทำไปแล้วทั้งหมด
    $stmt = $conn->prepare("SELECT * FROM orders WHERE status != 'pending' ORDER BY order_time DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติรายการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
            <h2 class="text-secondary m-0">📜 ประวัติรายการ</h2>
        </div>
        <a href="<?php echo $back_link; ?>" class="btn btn-outline-secondary">
            ⬅️ กลับหน้าหลัก
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>วันที่/เวลา</th>
                        <th>ผู้สั่ง</th> 
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <?php if($my_role != 'user') echo "<th>จัดการ</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-muted small">
                                <?php echo date('d/m/Y H:i', strtotime($row['order_time'])); ?>
                            </td>
                            <td class="fw-bold text-primary">
                                <?php echo !empty($row['user_name']) ? $row['user_name'] : "-"; ?>
                            </td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['quantity']; ?> แก้ว</td>
                            <td>
                                <?php 
                                    if($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark">⏳ รออนุมัติ</span>';
                                    elseif($row['status'] == 'approved') echo '<span class="badge bg-success">✅ อนุมัติแล้ว</span>';
                                    else echo '<span class="badge bg-danger">❌ ปฏิเสธ</span>';
                                ?>
                            </td>
                            
                            <?php if($my_role != 'user'): ?>
                            <td>
                                <a href="history.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('⚠️ ยืนยันลบประวัติรายการนี้ถาวร?');">
                                   🗑️ ลบ
                                </a>
                            </td>
                            <?php endif; ?>
                            
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo ($my_role != 'user') ? '6' : '5'; ?>" class="py-5 text-muted">ยังไม่มีประวัติรายการ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>