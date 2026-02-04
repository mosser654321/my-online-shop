<?php
include 'db.php';

// 1. เช็คสิทธิ์ (Approver/Admin)
if (!isset($_COOKIE['user_id'])) { header("Location: index.php"); exit(); }
if ($_COOKIE['role'] != 'approver' && $_COOKIE['role'] != 'admin') { 
    echo "<script>alert('❌ ไม่มีสิทธิ์เข้าถึง'); window.location='index.php';</script>";
    exit(); 
}

// 2. เพิ่มสินค้า (Logic ใหม่: รวม Emoji + ชื่อ)
if (isset($_POST['add_product'])) {
    $emoji = trim($_POST['product_emoji']); // รับอีโมจิ
    $name = trim($_POST['product_name']);   // รับชื่อเมนู
    
    // รวมร่าง: "☕" + " " + "มอคค่า" = "☕ มอคค่า"
    $full_name = $emoji . " " . $name; 

    if (!empty($full_name)) {
        $stmt = $conn->prepare("INSERT INTO products (name) VALUES (?)");
        $stmt->bind_param("s", $full_name);
        $stmt->execute();
        echo "<script>alert('✅ เพิ่มเมนูเรียบร้อย'); window.location='manage_products.php';</script>";
    }
}

// 3. ลบสินค้า
if (isset($_GET['del'])) {
    $conn->query("DELETE FROM products WHERE id = " . $_GET['del']);
    header("Location: manage_products.php");
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
            <h2 class="text-secondary m-0">จัดการสินค้า</h2>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            ⬅️ กลับ Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">➕ เพิ่มสินค้าใหม่</div>
                <div class="card-body">
                    <form method="post" class="row g-2 align-items-end">
                        
                        <div class="col-3">
                            <label class="form-label small text-muted">Emoji</label>
                            <input type="text" name="product_emoji" class="form-control text-center fs-4" placeholder="" required>
                        </div>

                        <div class="col-7">
                            <label class="form-label small text-muted">ชื่อสินค้า</label>
                            <input type="text" name="product_name" class="form-control" placeholder="" required>
                        </div>

                        <div class="col-2">
                            <button type="submit" name="add_product" class="btn btn-success w-100">บันทึก</button>
                        </div>
                    </form>
                    <div class="form-text mt-2">* (กด window - . เพื่อจะเอาEmoji) ระบบจะนำ Emoji และชื่อไปรวมกันให้อัตโนมัติ</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">รายการสินค้าทั้งหมด</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if ($products->num_rows > 0): ?>
                            <?php while($p = $products->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="fs-5 fw-bold text-dark"><?php echo $p['name']; ?></span>
                                
                                <a href="manage_products.php?del=<?php echo $p['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('ยืนยันลบเมนูนี้?');">
                                   🗑️ ลบ
                                </a>
                            </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted py-5">ยังไม่มีสินค้าในระบบ</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</body>
</html>