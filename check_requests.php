<?php
// check_requests.php
include 'requestpro.php';

$sql = "SELECT COUNT(*) as new_requests FROM requests WHERE status = 'new'";
$stmt = $pdo->query($sql);
$row = $stmt->fetch();
echo json_encode($row['new_requests']);
?>