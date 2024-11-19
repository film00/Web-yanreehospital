<?php
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


// Token สำหรับ Line Notify
$lineNotifyToken = 'rsstHhNyIowZ0aj3aYQNOUp4u8xXhpfgRTJ3cjjAk8J';

// เชื่อมต่อกับ MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงไอดีล่าสุดจากตาราง project
$sqlLatestProject = "SELECT MAX(id_project) AS latest_id FROM project";
$resultLatestProject = $conn->query($sqlLatestProject);
$latestProjectId = 0;

if ($resultLatestProject->num_rows > 0) {
    $row = $resultLatestProject->fetch_assoc();
    $latestProjectId = $row['latest_id'];
    echo "Latest Project ID: " . $latestProjectId . "<br>"; // ตรวจสอบค่าของ latestProjectId
} else {
    die("ไม่พบข้อมูลโปรเจกต์");
}

// ตรวจสอบคำร้องใหม่ในตาราง use_car ที่เชื่อมโยงกับไอดีล่าสุดของโปรเจกต์
$sqlNewRequests = "SELECT * FROM use_car WHERE id_project = $latestProjectId";
$resultNewRequests = $conn->query($sqlNewRequests);

if ($resultNewRequests->num_rows > 0) {
    $messageNewRequests = "มีคำร้องใหม่เข้ามาในโปรเจกต์ ID: " . $latestProjectId;
    $responseNewRequests = sendLineNotify($lineNotifyToken, $messageNewRequests);
    echo "New Requests Notification Response: " . $responseNewRequests;
} else {
    echo "ไม่มีคำร้องใหม่เข้ามา<br>";
}

// ปิดการเชื่อมต่อ MySQL
$conn->close();

// ฟังก์ชันสำหรับส่งข้อความไปยัง Line Notify
function sendLineNotify($token, $message) {
    $url = 'https://notify-api.line.me/api/notify';
    $headers = array(
        'Authorization: Bearer ' . $token,
    );
    $data = array(
        'message' => $message,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
        return "Curl Error: " . $error;
    }

    return "Response: " . $response . " HTTP Code: " . $httpCode;
}
?>
