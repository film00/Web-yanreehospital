<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $user_id = $_POST['user_id'];

    $servername = "**********";
    $username = "**********";
    $password = "**********";
    $dbname = "**********";
    

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // อัปเดตสถานะของผู้ใช้ในฐานข้อมูล
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $user_id);

    if ($stmt->execute()) {
        echo "อัปเดตสถานะสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>
