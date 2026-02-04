<?php
// ลบ Cookie ทั้งหมด
setcookie("user_id", "", time() - 3600, "/");
setcookie("role", "", time() - 3600, "/");
setcookie("name", "", time() - 3600, "/");

// ดีดกลับไปหน้า Login
header("Location: index.php");
exit();
?>