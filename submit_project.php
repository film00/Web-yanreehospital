<?php
session_start();
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['id_us'])) {
        $id_us = $_SESSION['id_us']; // ID ผู้ใช้จาก session
        $name_project = $_POST['name_project'];
        $reason_and_reason = $_POST['reason_and_reason'];
        $objective = $_POST['objective'];
        $target_group = $_POST['target_group'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $budget = $_POST['budget'];
        $evaluation = $_POST['evaluation'];

        // ตรวจสอบไฟล์แนบ
        $file_name = null;
        $file_path = null;

        // Debug ข้อมูลที่ส่งเข้ามา
        var_dump($_POST);
        var_dump($_FILES);

        if (isset($_POST['project_file']) && $_POST['project_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_POST['project_file'];
            $file_name = basename($file['name']);
            $target_dir = "file/"; // เปลี่ยนเป็นพาธที่ถูกต้อง
            $target_file = $target_dir . $file_name;

            // ตรวจสอบประเภทไฟล์ (อนุญาตเฉพาะ .docx และ .pdf)
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if ($file_type !== "pdf" && $file_type !== "docx") {
                echo "เฉพาะไฟล์ .docx และ .pdf เท่านั้นที่ได้รับอนุญาต";
                exit();
            }

            // อัปโหลดไฟล์
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $file_path = $target_file;
                echo "ไฟล์อัปโหลดสำเร็จ: " . $file_path;
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
                exit();
            }
        } else {
            echo "ไม่มีไฟล์แนบหรือเกิดข้อผิดพลาดในการอัปโหลดไฟล์";
        }

        // SQL สำหรับบันทึกข้อมูลโครงการ
        $sql = "INSERT INTO project (id_us, name_project, reason_and_reason, objective, target_group, start_date, end_date, budget, evaluation, file_name, file_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssssss", $id_us, $name_project, $reason_and_reason, $objective, $target_group, $start_date, $end_date, $budget, $evaluation, $file_name, $file_path);

        // Debug SQL statement และการบันทึกข้อมูล
        if ($stmt->execute()) {
            echo "บันทึกข้อมูลโครงการสำเร็จ พร้อมไฟล์: " . $file_name;
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "ผู้ใช้ไม่ได้เข้าสู่ระบบ";
    }
}

$conn->close();
?>
