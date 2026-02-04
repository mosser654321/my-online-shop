<?php
include 'db.php';

// ถ้าเป็น User ธรรมดา ให้ไล่ออกไป
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user') {
    header("Location: index.php");
    exit();
}

// [จุดที่แก้ไข] ถ้า Admin เผลอเข้ามาหน้านี้ ให้ดีดไปหน้า users.php ทันที
if ($_SESSION['role'] == 'admin') {
    header("Location: users.php");
    exit();
}

// ฟังก์ชันอนุมัติ (Approver)
if (isset($_GET['approve_id']) && $_SESSION['role'] == 'approver') {
    $id = (int)$_GET['approve_id'];
    $conn->query("UPDATE orders SET status='approved' WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard (Approver)</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    
    <div class="container bg-white p-4 rounded shadow">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center">
                <img src="logo.png" width="50" class="me-3">
                <div>
                    <h2 class="m-0">📊 Dashboard</h2>
                    <small class="text-muted">สำหรับฝ่ายอนุมัติ (Approver)</small>
                </div>
            </div>
            <a href="index.php" class="btn btn-outline-danger">🚪 ออกจากระบบ</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-2">
                <a href="history.php" class="btn btn-info text-white w-100 h-100 d-flex align-items-center justify-content-center">
                    <span class="fs-4 me-2">📜</span> ดูประวัติ/แก้ไขออเดอร์
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="manage_products.php" class="btn btn-warning w-100 h-100 d-flex align-items-center justify-content-center">
                    <span class="fs-4 me-2">📦</span> จัดการสินค้า (เพิ่ม/ลบ)
                </a>
            </div>
        </div>

        <h4 class="mb-3 text-primary">🛒 รายการรออนุมัติ (Real-time)</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>เวลา</th>
                        <th>ผู้สั่ง</th>
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody id="liveData">
                    <tr><td colspan="6" class="text-center">กำลังโหลดข้อมูล...</td></tr>
                </tbody>
            </table>
        </div>

        <script>
            function fetchData() {
                fetch('api.php')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if(data.length === 0) {
                        html = '<tr><td colspan="6" class="text-center text-muted p-4">✅ ไม่มีรายการค้างอนุมัติ</td></tr>';
                    } else {
                        data.forEach(order => {
                            html += `
                                <tr>
                                    <td>${order.order_time}</td>
                                    <td>${order.user_name}</td>
                                    <td class="fw-bold">${order.product_name}</td>
                                    <td><span class="badge bg-primary rounded-pill fs-6">${order.quantity}</span></td>
                                    <td class="text-warning fw-bold">${order.status}</td>
                                    <td>
                                        <a href="dashboard.php?approve_id=${order.id}" class="btn btn-success btn-sm">✔ อนุมัติ</a>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('liveData').innerHTML = html;
                });
            }
            setInterval(fetchData, 1000);
            fetchData();
        </script>
    </div>
</body>
</html>