<?php
include 'db.php';

if (!isset($_COOKIE['user_id'])) { header("Location: index.php"); exit(); }
$user_name = $_COOKIE['name'];

if (isset($_POST['submit_order'])) {
    $product = $_POST['product_name'];
    $qty = $_POST['quantity'];
    $stmt = $conn->prepare("INSERT INTO orders (user_name, product_name, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("ssi", $user_name, $product, $qty);
    if ($stmt->execute()) {
        echo "<script>alert('✅ สั่งซื้อสำเร็จ! กรุณารออนุมัติ'); window.location='order.php';</script>";
    }
}

// ดึงรายชื่อสินค้าจาก Database (เพื่อให้ตรงกับหน้า Dashboard)
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
            <h2 class="text-primary m-0">🛒 สั่งซื้อสินค้า</h2>
        </div>
        <div class="text-end">
            <span class="small text-muted">ผู้สั่ง: </span>
            <span class="fw-bold"><?php echo $user_name; ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">🚪 ออก</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    📝 แบบฟอร์มสั่งซื้อ
                </div>
                <div class="card-body p-4">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกสินค้า</label>
                            <select name="product_name" class="form-select" required>
                                <option value="" disabled selected>-- กรุณาเลือก --</option>
                                
                                <?php if($products->num_rows > 0): ?>
                                    <?php while($p = $products->fetch_assoc()): ?>
                                        <option value="<?php echo $p['name']; ?>">
                                             <?php echo $p['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="ลาเต้">☕ ลาเต้</option>
                                    <option value="ชาเขียว">🍵 ชาเขียว</option>
                                <?php endif; ?>
                                
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">จำนวน</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        
                        <button type="submit" name="submit_order" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm">
                            ✅ ยืนยันการสั่งซื้อ
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="history.php" class="btn btn-outline-info w-100">
                        📜 ดูประวัติการสั่งซื้อของฉัน
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>