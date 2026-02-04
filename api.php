<?php
include 'db.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM orders WHERE status='pending' ORDER BY id DESC";
$result = $conn->query($sql);

$data = array();
if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
echo json_encode($data);
?>