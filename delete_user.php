<?php
session_start();
ob_start(); // เริ่มการ buffer output

require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php"; // ตรวจสอบเส้นทางไฟล์และปรับให้ถูกต้อง

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";
 
if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ตรวจสอบว่าเป็นคำขอลบ
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
            $deleteUserId = $_POST['delete_user_id'];

            // ดึงข้อมูลเส้นทางรูปโปรไฟล์ของผู้ใช้
            $profilePicStmt = $conn->prepare("SELECT profile_picture FROM users WHERE id_us = :id_us");
            $profilePicStmt->bindParam(':id_us', $deleteUserId, PDO::PARAM_INT);
            $profilePicStmt->execute();
            $userProfile = $profilePicStmt->fetch(PDO::FETCH_ASSOC);

            if ($userProfile && !empty($userProfile['profile_picture'])) {
                $profilePicturePath = $userProfile['profile_picture'];

                // ลบไฟล์รูปโปรไฟล์
                if (file_exists($profilePicturePath)) {
                    unlink($profilePicturePath);
                }
            }

            // ลบผู้ใช้จากฐานข้อมูล
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE id_us = :id_us");
            $deleteStmt->bindParam(':id_us', $deleteUserId, PDO::PARAM_INT);
            $deleteStmt->execute();

            header("Location: datausers.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}

ob_end_flush(); // ปล่อย buffer output
?>
