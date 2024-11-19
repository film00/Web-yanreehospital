<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['id_us'])) {
    header("Location: ../index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yanree_db";

if (isset($_GET['id'])) {
    $id_car = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ลบข้อมูลรถจากฐานข้อมูล
        $sql = "DELETE FROM car WHERE id_car = :id_car";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_car', $id_car, PDO::PARAM_INT);
        $stmt->execute();

        // ลบรูปภาพรถ (ถ้ามี)
        $sql = "SELECT picture_name FROM car WHERE id_car = :id_car";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_car', $id_car, PDO::PARAM_INT);
        $stmt->execute();
        $car = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($car && !empty($car['picture_name'])) {
            $picturePath = '../imagecar/' . $car['picture_name'];
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }

        header("Location: ../car_management.php");
        exit();
    } catch (PDOException $e) {
        echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    }
} else {
    header("Location: ../car_management.php");
    exit();
}

$conn = null;
?>