<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

try {
    // เชื่อมต่อฐานข้อมูล
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบว่าเป็นการร้องขอแบบ POST หรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $logo = $_POST['logo'];
        $number = $_POST['number'];
        $status_car = 'ว่าง'; // ค่าคงที่สำหรับ status_car

        // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "imagecar/";
            $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));

            // สร้างชื่อไฟล์ใหม่โดยใช้ uniqid() และ time() ต่อท้าย
            $new_file_name = uniqid() . '_' . time() . '.' . $imageFileType;
            $target_file = $target_dir . $new_file_name;
            $uploadOk = 1;

            // ตรวจสอบว่าไฟล์เป็นรูปภาพจริงหรือไม่
            $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "ไฟล์ไม่ใช่รูปภาพ.";
                $uploadOk = 0;
            }

            // ตรวจสอบขนาดของไฟล์
            if ($_FILES["profile_picture"]["size"] > 5000000) {
                echo "ขออภัย, ไฟล์ของคุณใหญ่เกินไป.";
                $uploadOk = 0;
            }

            // อนุญาตเฉพาะบางประเภทไฟล์
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "ขออภัย, อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.";
                $uploadOk = 0;
            }

            // ตรวจสอบว่าไม่มีข้อผิดพลาดใดๆ และทำการอัปโหลดไฟล์
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $profile_picture = $target_file;
                    $picture_name = $new_file_name;
                } else {
                    echo "ขออภัย, มีข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.";
                }
            } else {
                // หากมีข้อผิดพลาดในการอัปโหลด
                $profile_picture = null;
                $picture_name = null;
            }
        } else {
            // กรณีไม่มีการอัปโหลดไฟล์
            $profile_picture = null;
            $picture_name = null;
        }

        // เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูล
        $stmt = $conn->prepare("INSERT INTO car (logo, number, status_car, profile_picture, picture_name) VALUES (:logo, :number, :status_car, :profile_picture, :picture_name)");
        $stmt->bindParam(':logo', $logo);
        $stmt->bindParam(':number', $number);
        $stmt->bindParam(':status_car', $status_car); // ใช้ตัวแปรที่กำหนดไว้
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':picture_name', $picture_name);

        // ดำเนินการคำสั่ง SQL
        $stmt->execute();

        // Redirect ไปยังหน้า datacar.php หลังจากเพิ่มข้อมูลสำเร็จ
        header("Location: datacar.php");
    } else {
        // Redirect กลับไปยัง addcar.php หากไม่ได้เป็นการร้องขอแบบ POST
        header("Location: addcar.php");
    }
} catch (PDOException $e) {
    // แสดงข้อความข้อผิดพลาดการเชื่อมต่อฐานข้อมูล
    echo "Connection failed: " . $e->getMessage();
}
?>
