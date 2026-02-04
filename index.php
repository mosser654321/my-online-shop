<?php
include 'db.php';

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // --- ส่วนสำคัญ: สร้าง Cookie เพื่อจำการล็อกอิน (แทน Session) ---
        setcookie("user_id", $row['id'], time() + (86400 * 30), "/"); // อยู่ได้ 30 วัน
        setcookie("role", strtolower(trim($row['role'])), time() + (86400 * 30), "/");
        
        // จำชื่อจริง (ถ้ามี) หรือชื่อเล่น
        $name_show = !empty($row['real_name']) ? $row['real_name'] : $row['username'];
        setcookie("name", $name_show, time() + (86400 * 30), "/");

        // --- ส่วนลิงก์ไปหน้าต่างๆ (ชื่อไฟล์ต้องเป๊ะ) ---
        $role = strtolower(trim($row['role']));
        if ($role == 'admin') {
            header("Location: users.php"); // ไปหน้า Admin
        } elseif ($role == 'user') {
            header("Location: order.php"); // ไปหน้าสั่งของ
        } else {
            header("Location: dashboard.php"); // ไปหน้าผู้อนุมัติ
        }
        exit();
    } else {
        echo "<script>alert('❌ ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .login-box { width: 380px; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; }
        .logo-img { width: 120px; height: auto; margin-bottom: 20px; object-fit: contain; }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="logo.png" alt="Logo" class="logo-img">
        <h3 class="mb-4 fw-bold text-secondary">เข้าสู่ระบบ</h3>
        <form method="post">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <label>ชื่อผู้ใช้งาน</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <label>รหัสผ่าน</label>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fs-5 rounded-pill shadow-sm">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>