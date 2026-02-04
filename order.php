<?php
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit_order'])) {
    $name = $_SESSION['name'];
    $item = $_POST['product_name'];
    $qty  = $_POST['quantity'];
    
    $stmt = $conn->prepare("INSERT INTO orders (user_name, product_name, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $item, $qty);
    
    if($stmt->execute()){
        echo "<script>alert('✅ สั่งซื้อ [$item] จำนวน $qty ชิ้น เรียบร้อย!');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สั่งซื้อสินค้า</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .product-card { transition: transform 0.2s; cursor: pointer; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .emoji-icon { font-size: 3rem; margin-bottom: 10px; }
    </style>
</head>
<body class="container" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
        <div class="d-flex align-items-center">
            <img src="logo.png" width="40" class="me-3">
            <div>
                <h4 class="m-0">👋 สวัสดีคุณ <?php echo htmlspecialchars($_SESSION['name']); ?></h4>
                <small class="text-muted">Role: User</small>
            </div>
        </div>
        <a href="index.php" class="btn btn-outline-danger btn-sm">🚪 ออกจากระบบ</a>
    </div>

    <div class="mb-4 text-center">
        <a href="history.php" class="btn btn-info text-white">📜 ดูประวัติที่อนุมัติแล้ว</a>
    </div>

    <hr>
    <p class="text-center text-muted">เลือกเมนูและระบุจำนวนที่ต้องการ</p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        
        <?php
        $p_res = $conn->query("SELECT * FROM products");
        
        if($p_res->num_rows > 0):
            while($p_row = $p_res->fetch_assoc()):
        ?>
        <div class="col">
            <div class="card h-100 p-3 text-center product-card border-0 shadow-sm">
                <div class="emoji-icon"><?php echo $p_row['icon']; ?></div>
                <h5><?php echo $p_row['name']; ?></h5>
                
                <form method="post">
                    <input type="hidden" name="product_name" value="<?php echo $p_row['name']; ?>">
                    <input type="number" name="quantity" value="1" min="1" class="form-control mb-2 text-center">
                    <button type="submit" name="submit_order" class="btn btn-success w-100">🛒 สั่งซื้อ</button>
                </form>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center text-muted mt-5">
                <h3>🚫 ยังไม่มีรายการสินค้า</h3>
                <p>โปรดแจ้ง Admin ให้เพิ่มรายการสินค้า</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>