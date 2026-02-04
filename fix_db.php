<?php
include 'db.php';

echo "<h2>🔧 กำลังซ่อมฐานข้อมูล...</h2>";

// คำสั่งขยายช่อง status ให้รับตัวหนังสือได้ยาวๆ (50 ตัวอักษร)
$sql = "ALTER TABLE orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'";

if ($conn->query($sql) === TRUE) {
    echo "<h1 style='color:green'>✅ แก้ไขสำเร็จ!</h1>";
    echo "<p>ขยายช่อง status เป็น VARCHAR(50) เรียบร้อยแล้ว</p>";
    echo "<a href='dashboard.php'>กลับไปหน้า Dashboard</a>";
} else {
    echo "<h1 style='color:red'>❌ เกิดข้อผิดพลาด</h1>";
    echo "Error: " . $conn->error;
}
?>