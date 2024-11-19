<?php
session_start();

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";
 

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// ตรวจสอบว่าเซสชันมี ID ของผู้ใช้หรือไม่
if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];
    
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT prefix, name, last_name, rank FROM users WHERE id_us = :id_us");
    $stmt->bindValue(':id_us', $id_us, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "ไม่พบข้อมูลผู้ใช้";
        exit();
    }

    // รับค่าจากฟอร์ม
    $carData = [
        'go' => $_POST['go'] ?? '',
        'about' => $_POST['about'] ?? '',
        'startDate' => $_POST['startDate'] ?? '',
        'endDate' => $_POST['endDate'] ?? '',
        'totalDays' => $_POST['totalDays'] ?? '',
        'approval' => $_POST['approval'] ?? [],
        'fuel' => $_POST['fuel'] ?? '',
        'overnightStay' => $_POST['overnightStay'] ?? '',
        'purpose' => $_POST['purpose'] ?? '',
        'destination' => $_POST['destination'] ?? '',
        'vehicleRegistration' => $_POST['number'] ?? '',
        'custom_number' => $_POST['custom_number'] ?? '',
        'driver' => $_POST['driver'] ?? '',
        'custom_driver' => $_POST['custom_driver'] ?? '',
        'travelerCount' => $_POST['travelerCount'] ?? '',
        'travelers' => $_POST['travelers'] ?? ''
    ];

    // ปรับค่า vehicleRegistration และ driver ตามค่าที่ผู้ใช้ป้อน
    $vehicleRegistration = $carData['vehicleRegistration'] === 'other' ? $carData['custom_number'] : $carData['vehicleRegistration'];
    $driver = $carData['driver'] === 'other' ? $carData['custom_driver'] : $carData['driver'];

    // แปลง array ของ approval เป็น string
    $approval = isset($carData['approval']) ? implode(", ", $carData['approval']) : '';

    // ปรับค่า gasoline_cost และ rest
    $gasoline_cost = $carData['fuel'] === 'fuel' ? 'ขอเบิกค่าน้ำมันเชื้อเพลิง' : 'ไม่ขอเบิกค่าน้ำมันเชื้อเพลิง';
    $rest = $carData['overnightStay'] === 'stayOvernight' ? 'พักค้างคืน' : 'ไม่พักค้างคืน';

    // เตรียมคำสั่ง SQL
    $sql = "
        INSERT INTO use_car 
        (id_us_car, name, last_name, rank, status_car, go_to, about_costs, from_date, up_date, sum_date, carrier, id_car, driver, gasoline_cost, rest, purposes, destinations, id_project, sumfollower, follower1, comments) 
        VALUES 
        (:id_us_car, :name, :last_name, :rank, :status_car, :go_to, :about_costs, :from_date, :up_date, :sum_date, :carrier, :id_car, :driver, :gasoline_cost, :rest, :purpose, :destination, :id_project, :sumfollower, :follower1, :comments)
    ";

    try {
        $stmt = $conn->prepare($sql);
        
        // ผูกค่ากับพารามิเตอร์
        $stmt->bindValue(':id_us_car', $id_us, PDO::PARAM_INT);
        $stmt->bindValue(':name', $user['name'], PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $user['last_name'], PDO::PARAM_STR);
        $stmt->bindValue(':rank', $user['rank'], PDO::PARAM_STR);
        $stmt->bindValue(':status_car', 'รออนุมัติ', PDO::PARAM_STR);
        $stmt->bindValue(':go_to', $carData['go'], PDO::PARAM_STR);
        $stmt->bindValue(':about_costs', $carData['about'], PDO::PARAM_STR);
        $stmt->bindValue(':from_date', $carData['startDate'], PDO::PARAM_STR);
        $stmt->bindValue(':up_date', $carData['endDate'], PDO::PARAM_STR);
        $stmt->bindValue(':sum_date', $carData['totalDays'], PDO::PARAM_INT);
        $stmt->bindValue(':carrier', $approval, PDO::PARAM_STR); // ใช้ค่าที่แปลงแล้ว
        $stmt->bindValue(':id_car', $vehicleRegistration, PDO::PARAM_STR);
        $stmt->bindValue(':driver', $driver, PDO::PARAM_STR);
        $stmt->bindValue(':gasoline_cost', $gasoline_cost, PDO::PARAM_STR);
        $stmt->bindValue(':rest', $rest, PDO::PARAM_STR);
        $stmt->bindValue(':purpose', $carData['purpose'], PDO::PARAM_STR);
        $stmt->bindValue(':destination', $carData['destination'], PDO::PARAM_STR);
        $stmt->bindValue(':id_project', $_POST['id_project'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':sumfollower', $carData['travelerCount'], PDO::PARAM_INT);
        $stmt->bindValue(':follower1', $carData['travelers'], PDO::PARAM_STR);
        $stmt->bindValue(':comments', 'ไม่มี', PDO::PARAM_STR);

        // ดำเนินการคำสั่ง SQL
        $stmt->execute();
        
        
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    echo "ไม่พบข้อมูลเซสชันของผู้ใช้";
}
?>
