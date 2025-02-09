<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";



try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // รับค่า username จากการส่งข้อมูลผ่าน POST
    $user = $_POST['username'];

    // ตรวจสอบว่ามีชื่อผู้ใช้นี้อยู่ในฐานข้อมูลหรือไม่
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $user);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "ชื่อผู้ใช้นี้มีอยู่แล้วกรุณาตั้งชื่อผู้ใช้งานใหม่";
    } else {
        echo "ชื่อผู้ใช้นี้สามารถใช้ได้";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
