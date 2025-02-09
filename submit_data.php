<?php
session_start();
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


date_default_timezone_set('Asia/Bangkok');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['id_us'])) {
        $id_us = $_SESSION['id_us'];
        
        $stmt = $conn->prepare("SELECT prefix, name, last_name, rank FROM users WHERE id_us = :id_us");
        $stmt->bindValue(':id_us', $id_us, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("ไม่พบข้อมูลผู้ใช้.");
        }

        $projectData = json_decode($_POST['projectData'], true) ?? [];
        //$carData = json_decode($_POST['carData'], true) ?? [];

        if (isset($_FILES['project_file'])) {
            $file = $_FILES['project_file'];
            $maxSize = 50 * 1024 * 1024; // 50MB
            
            if ($file['size'] > $maxSize) {
                echo 'ไฟล์มีขนาดใหญ่เกินกว่า 50MB';
            } else {
                // ตั้งค่าชื่อไฟล์และเส้นทางในการจัดเก็บไฟล์
                $file_name = basename($file['name']);
                $upload_dir = 'file/'; // เส้นทางการจัดเก็บไฟล์
                $file_path = $upload_dir . $file_name;
        
                // จัดการการอัปโหลดไฟล์
                move_uploaded_file($file['tmp_name'], $file_path);
            }
        } else { 
            $file_path = null;
            $file_name = null;
        }
        

        $stmt = $conn->prepare("INSERT INTO project (id_us_pro, name_us, rank_us_pro, name_project, reason_and_reason, objective, Target_group, Processing_time, Budget, Evaluation, file_path, file_name, status_pro, time) 
            VALUES (:id_us_pro, :name_us, :rank_us_pro, :name_project, :reason_and_reason, :objective, :target_group, :processing_time, :budget, :evaluation, :file_path, :file_name, :status_pro, :time)");
            
        $stmt->bindValue(':id_us_pro', $id_us, PDO::PARAM_INT);
        $stmt->bindValue(':name_us', $user['prefix'] . ' ' . $user['name'] . ' ' . $user['last_name'], PDO::PARAM_STR);
        $stmt->bindValue(':rank_us_pro', $user['rank'], PDO::PARAM_STR); // เพิ่มการ bind ค่า rank_us_pro
        $stmt->bindValue(':name_project', $projectData['name_project'], PDO::PARAM_STR);
        $stmt->bindValue(':reason_and_reason', $projectData['reason_and_reason'], PDO::PARAM_STR);
        $stmt->bindValue(':objective', $projectData['objective'], PDO::PARAM_STR);
        $stmt->bindValue(':target_group', $projectData['target_group'], PDO::PARAM_STR);
        $stmt->bindValue(':processing_time', $projectData['start_date'] . ' ถึง ' . $projectData['end_date'], PDO::PARAM_STR);
        $stmt->bindValue(':budget', $projectData['budget'], PDO::PARAM_STR);
        $stmt->bindValue(':evaluation', $projectData['evaluation'], PDO::PARAM_STR);
        $stmt->bindValue(':file_path', $file_path, PDO::PARAM_STR);
        $stmt->bindValue(':file_name', $file_name, PDO::PARAM_STR);
        $stmt->bindValue(':status_pro', 'รออนุมัติ', PDO::PARAM_STR);
        $stmt->bindValue(':time', date('Y-m-d H:i:s'), PDO::PARAM_STR);

        $stmt->execute();
        $projectId = $conn->lastInsertId();


        echo "บันทึกข้อมูลสำเร็จ.";
    }
} catch (PDOException $e) {

} catch (Exception $e) {

}
?>
