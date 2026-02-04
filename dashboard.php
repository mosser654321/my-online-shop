<?php
include 'db.php';

// 1. เช็คสิทธิ์
if (!isset($_COOKIE['user_id'])) { header("Location: index.php"); exit(); }
if ($_COOKIE['role'] != 'approver' && $_COOKIE['role'] != 'admin') { header("Location: index.php"); exit(); }

// กดอนุมัติ/ปฏิเสธ
if (isset($_GET['action']) && isset($_GET['id'])) {
    $status = ($_GET['action'] == 'approve') ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $_GET['id']);
    if ($stmt->execute()) header("Location: dashboard.php");
}

// ดึงรายการรออนุมัติ
$result = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY order_time DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ผู้อนุมัติ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <meta http-equiv="refresh" content="30">
    
</head>
<body class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
            <div>
                <h2 class="text-primary m-0">📋 Dashboard</h2>
                <small class="text-muted" style="font-size: 0.8rem;">
                    🔄 อัปเดตล่าสุด: <?php echo date('H:i:s'); ?> (รีเฟรชทุก 30 วิ)
                </small>
            </div>
        </div>
        <div class="text-end">
            <span class="small text-muted">สวัสดีคุณ <?php echo $_COOKIE['name']; ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">🚪 ออก</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <a href="manage_products.php" class="btn btn-primary w-100 py-3 shadow-sm">
                จัดการเมนูสินค้า (เพิ่ม/ลบ)
            </a>
        </div>
        <div class="col-md-6">
            <a href="history.php" class="btn btn-secondary w-100 py-3 shadow-sm">
                🕒 ดูประวัติการอนุมัติย้อนหลัง
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white text-secondary fw-bold">
            ⏳ รายการที่รอการอนุมัติ
        </div>
        <div class="card-body p-0">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">เวลา</th>
                            <th>ผู้สั่ง</th>
                            <th>สินค้า</th>
                            <th>จำนวน</th>
                            <th class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?php echo date('H:i', strtotime($row['order_time'])); ?></td>
                            <td class="fw-bold text-primary"><?php echo $row['user_name']; ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo $row['product_name']; ?></span></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td class="text-end pe-4">
                                <a href="dashboard.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm rounded-pill px-3" onclick="return confirm('อนุมัติ?');">✅</a>
                                <a href="dashboard.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="return confirm('ปฏิเสธ?');">❌</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center p-5 text-muted"><h3>🎉</h3><p>เคลียร์หมดแล้ว ไม่มีรายการรออนุมัติ</p></div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
