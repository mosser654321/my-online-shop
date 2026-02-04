<?php
include 'db.php';

// --- 1. เช็คสิทธิ์ด้วย Cookie ---
if (!isset($_COOKIE['user_id'])) {
    header("Location: index.php");
    exit();
}
// ดึงค่าจาก Cookie มาใช้งาน
$user_id = $_COOKIE['user_id'];
$user_name = $_COOKIE['name']; // ชื่อที่จะไปโชว์ในใบสั่งซื้อ
// ----------------------------

if (isset($_POST['submit_order'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];

    // บันทึกออเดอร์โดยใช้ชื่อจริงจาก Cookie ($user_name)
    $stmt = $conn->prepare("INSERT INTO orders (user_name, product_name, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("ssi", $user_name, $product_name, $quantity);

    if ($stmt->execute()) {
        echo "<script>alert('✅ สั่งซื้อสำเร็จ!'); window.location='order.php';</script>";
    } else {
        echo "<script>alert('❌ เกิดข้อผิดพลาด');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head><title>สั่งซื้อสินค้า</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <h1>🛒 สวัสดีคุณ <?php echo $user_name; ?></h1>
    <hr>
    <form method="post">
        <div class="mb-3">
            <label>เลือกเมนู</label>
            <select name="product_name" class="form-control">
                <option value="ลาเต้">☕ ลาเต้</option>
                <option value="ชาเขียว">🍵 ชาเขียว</option>
                <option value="ข้าวผัด">🍛 ข้าวผัด</option>
            </select>
        </div>
        <div class="mb-3">
            <label>จำนวน</label>
            <input type="number" name="quantity" class="form-control" value="1" min="1">
        </div>
        <button type="submit" name="submit_order" class="btn btn-success w-100">ยืนยันการสั่งซื้อ</button>
    </form>
    <br>
    <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
</body>
</html>