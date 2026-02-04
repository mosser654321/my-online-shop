<?php
include 'db.php';

// --- 1. เช็คสิทธิ์ (ใช้ Cookie) ---
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- 2. ส่วนบันทึกชื่อเล่น (แก้ไขเฉพาะชื่อเล่น) ---
if (isset($_POST['save_name'])) {
    $uid = $_POST['user_id'];
    $rname = $_POST['real_name'];
    $stmt = $conn->prepare("UPDATE users SET real_name = ? WHERE id = ?");
    $stmt->bind_param("si", $rname, $uid);
    $stmt->execute();
    echo "<script>alert('✅ บันทึกชื่อเรียบร้อย'); window.location='users.php';</script>";
}

// --- 3. ส่วนเพิ่ม User ใหม่ ---
if (isset($_POST['add_user'])) {
    $u = $_POST['new_u'];
    $p = $_POST['new_p']; // *ข้อแนะนำ: ระบบจริงควรเข้ารหัส md5 หรือ password_hash ก่อนเก็บ*
    $r = $_POST['new_r'];
    $rn = $_POST['new_rn'];

    $check = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check->bind_param("s", $u);
    $check->execute();
    if($check->get_result()->num_rows > 0){
        echo "<script>alert('❌ Username นี้มีอยู่แล้ว');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, real_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $u, $p, $r, $rn);
        if($stmt->execute()){
            echo "<script>alert('✅ เพิ่มผู้ใช้สำเร็จ'); window.location='users.php';</script>";
        }
    }
}

// --- 4. ส่วนลบ User ---
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE id = $del_id");
    echo "<script>alert('ลบเรียบร้อย'); window.location='users.php';</script>";
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้งาน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger">👮 จัดการผู้ใช้งาน (Admin)</h2>
        <a href="logout.php" class="btn btn-outline-danger">🚪 ออกจากระบบ</a>
    </div>

    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-header bg-primary text-white">➕ เพิ่มผู้ใช้งานใหม่</div>
        <div class="card-body">
            <form method="post" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted">Username</label>
                    <input type="text" name="new_u" class="form-control" placeholder="ตั้งชื่อไอดี" required>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">ชื่อที่แสดง</label>
                    <input type="text" name="new_rn" class="form-control" placeholder="เช่น พี่สมชาย">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Password</label>
                    <input type="text" name="new_p" class="form-control" placeholder="ตั้งรหัสผ่าน" required>
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">ตำแหน่ง</label>
                    <select name="new_r" class="form-select">
                        <option value="user">User</option>
                        <option value="approver">Approver</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-1"><button type="submit" name="add_user" class="btn btn-success w-100">เพิ่ม</button></div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm"><div class="card-body">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>ชื่อที่แสดง</th>
                    <th>Role</th>
                    <th style="width: 200px;">Password (ซ่อน)</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="fw-bold"><?php echo $row['username']; ?></td>
                    
                    <td>
                        <form method="post" class="d-flex gap-1 justify-content-center">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="real_name" class="form-control form-control-sm" value="<?php echo $row['real_name']; ?>" style="max-width: 150px;">
                            <button type="submit" name="save_name" class="btn btn-sm btn-primary" title="บันทึกชื่อ">💾</button>
                        </form>
                    </td>
                    
                    <td><span class="badge bg-secondary"><?php echo $row['role']; ?></span></td>
                    
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="password" 
                                   value="<?php echo $row['password']; ?>" 
                                   class="form-control text-center" 
                                   id="pass_<?php echo $row['id']; ?>" 
                                   readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass(<?php echo $row['id']; ?>)">
                                👁️
                            </button>
                        </div>
                    </td>

                    <td>
                        <?php if($row['role']!='admin'): ?>
                            <a href="users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันที่จะลบ user นี้?');">🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>

    <script>
        function togglePass(id) {
            var x = document.getElementById("pass_" + id);
            if (x.type === "password") {
                x.type = "text"; // เปลี่ยนเป็นเห็นตัวหนังสือ
            } else {
                x.type = "password"; // เปลี่ยนเป็นจุดๆ
            }
        }
    </script>
</body>
</html>