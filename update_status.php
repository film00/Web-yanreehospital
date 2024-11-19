<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $user_id = $_POST['user_id'];

    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $servername = "localhost";
    $username = "yanreeho_yanree_db";
    $password = "B@4N+209rhMfoT";
    $dbname = "yanreeho_yanree_db";
    

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
