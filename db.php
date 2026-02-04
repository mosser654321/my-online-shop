<?php
date_default_timezone_set('Asia/Bangkok');

$servername = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; 
$username = "gQ3TyFqadCPKx27.root"; // รหัสเดิมของคุณ
$password = "sgMiu2NyuKK1MajT";      // รหัสเดิมของคุณ
$dbname = "test"; 
$port = 4000; 

// เชื่อมต่อ (แบบรองรับ SSL)
$conn = mysqli_init();
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

if (!$conn->real_connect($servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connect Error: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");

// ✅ สั่ง Database ให้ใช้เวลาไทย
$conn->query("SET time_zone = '+07:00'");
?>