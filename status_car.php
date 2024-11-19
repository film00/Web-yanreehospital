<?php
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// ตั้งค่าเขตเวลาเป็นประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// สร้างตัวแปรสำหรับเก็บวันที่ปัจจุบัน
$current_date = date('Y-m-d'); // รูปแบบ Y-m-d สำหรับเปรียบเทียบ

// แสดงวันที่ปัจจุบัน 
echo "วันที่ปัจจุบัน: " . $current_date . "<br>";

// ตรวจสอบว่า $current_date ตรงกับ from_date หรือ up_date และมี status_car เป็น 'อนุมัติ'
$query = "SELECT id_car, from_date, up_date FROM use_car WHERE (from_date = :current_date OR up_date > DATE_ADD(:current_date, INTERVAL 1 DAY)) AND status_car = 'อนุมัติ'";
$stmt = $pdo->prepare($query);
$stmt->execute(['current_date' => $current_date]);

if ($stmt->rowCount() > 0) {
    // ดึง id_car, from_date และ up_date ที่ตรงกับวันที่ปัจจุบันและมี status_car เป็น 'อนุมัติ'
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        $id_car = $row['id_car'];
        $from_date = $row['from_date'];
        $up_date = $row['up_date'];

        // แปลงวันที่ from_date และ up_date ให้เป็นรูปแบบ Y-m-d
        $from_date_formatted = DateTime::createFromFormat('d/m/Y', $from_date)->format('Y-m-d');
        $up_date_formatted = DateTime::createFromFormat('d/m/Y', $up_date)->format('Y-m-d');

        // แสดง from_date และ up_date
        echo "จากวันที่: " . $from_date . "<br>";
        echo "อัพเดทถึงวันที่: " . $up_date . "<br>";

        // อัปเดต status_car ในตาราง car โดยใช้ number
        if ($from_date_formatted == $current_date) {
            // ถ้า from_date ตรงกับวันที่ปัจจุบัน
            $updateQuery = "UPDATE car SET status_car = 'ไม่ว่าง' WHERE number = :id_car";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(['id_car' => $id_car]);
            echo "สถานะรถยนต์ (number: $id_car) ถูกอัพเดทเป็น 'ไม่ว่าง'<br>";
        } elseif ($up_date_formatted > date('Y-m-d', strtotime($current_date . ' +1 day'))) {
            // ถ้า up_date มากกว่า 1 วันจากวันที่ปัจจุบัน
            $updateQuery = "UPDATE car SET status_car = 'ว่าง' WHERE number = :id_car";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(['id_car' => $id_car]);
            echo "สถานะรถยนต์ (number: $id_car) ถูกอัพเดทเป็น 'ว่าง'<br>";
        }
    }
} else {
    echo "ไม่มีการใช้งานในวันที่ปัจจุบัน";
}
?>
