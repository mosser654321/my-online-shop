<?php
include 'db.php';

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // ใช้ Prepared Statement เพื่อความปลอดภัย
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['role'] = strtolower(trim($row['role']));
        $_SESSION['user_id'] = $row['id'];
        
        // เช็คว่ามีชื่อจริง (real_name) ไหม? ถ้ามีให้ใช้ชื่อจริง ถ้าไม่มีให้ใช้ username
        if (!empty($row['real_name'])) {
            $_SESSION['name'] = $row['real_name'];
        } else {
            $_SESSION['name'] = $row['username'];
        }

        // แยกย้ายไปตามบทบาท
        if ($_SESSION['role'] == 'admin') {
            header("Location: users.php");
        } elseif ($_SESSION['role'] == 'user') {
            header("Location: order.php");
        } else {
            header("Location: dashboard.php");
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
    <title>เข้าสู่ระบบ</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sarabun', sans-serif; /* แนะนำให้ใช้ฟอนต์อ่านง่าย */
        }
        .login-box {
            width: 380px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo-img {
            width: 120px; /* ปรับขนาดโลโก้ตรงนี้ */
            height: auto;
            margin-bottom: 20px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="logo.png" alt="Logo วิทยาลัย" class="logo-img">
        
        <h3 class="mb-4 fw-bold text-secondary">เข้าสู่ระบบ</h3>
        
        <form method="post">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" required>
                <label for="floatingInput">ชื่อผู้ใช้งาน (Username)</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">รหัสผ่าน (Password)</label>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fs-5 rounded-pill shadow-sm">เข้าสู่ระบบ</button>
        </form>
    </div>