<?php
$host     = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";  // (1) Host ที่คุณก๊อปมาจากปุ่ม Connect
$port     = 4000;
$user     = "gQ3TyFqadCPKx27.root";           // (2) User ที่คุณก๊อปมา
$password = "sgMiu2NyuKK1MajT";              // (3) รหัสผ่านที่คุณสร้างไว้
$dbname   = "test";                // (4) สำคัญ! ต้องใช้ชื่อ test ตามในรูปครับ

// การเชื่อมต่อสำหรับ TiDB Cloud
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("เชื่อมต่อไม่สำเร็จ: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Bangkok');
session_start();
?>