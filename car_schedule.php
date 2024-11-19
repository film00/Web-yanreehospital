<?php
session_start();
require_once "./nav/navbar_boss.php";

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM car");
    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];
    $sql = "SELECT * FROM users WHERE id_us = :id_us";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_us', $id_us);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: index.php");
    exit();
}

try {
    // กำหนดสีที่ต้องการให้วนไปใช้
    $colorCycle = ['#333399', '#556B2F', '#A0522D', '#9932CC', '#DA70D6', '#663399', '#336600'];
    $colorIndex = 0; // ตัวแปรสำหรับติดตามตำแหน่งของสีใน array

    $stmt = $conn->prepare("SELECT name, last_name, carrier, id_car, from_date, up_date, status_car FROM use_car");
    $stmt->execute();
    $projectscar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = []; // สร้าง array สำหรับเก็บข้อมูล events

    foreach ($projectscar as $projectcar) {
        // ตรวจสอบสถานะโครงการ
        if ($projectcar['status_car'] !== 'อนุมัติ') {
            continue; // ข้ามไปยังรายการถัดไปหากสถานะไม่ใช่ "อนุมัติ"
        }

        // ตรวจสอบวันที่เริ่มต้นและวันที่สิ้นสุด
        $startDateObj = DateTime::createFromFormat('d/m/Y', $projectcar['from_date']);
        $endDateObj = DateTime::createFromFormat('d/m/Y', $projectcar['up_date']);

        if ($startDateObj && $endDateObj) {
            // แปลงวันที่เมื่อการแปลงสำเร็จ
            $startDate = $startDateObj->format('Y-m-d');
            $endDate = $endDateObj->format('Y-m-d');

            // เพิ่มวันถัดไปใน end date
            $endDatePlusOne = (new DateTime($endDate))->modify('+1 day')->format('Y-m-d');

            // ใช้สีตามลำดับใน array และวนรอบเมื่อถึงสีสุดท้าย
            $currentColor = $colorCycle[$colorIndex];
            $colorIndex = ($colorIndex + 1) % count($colorCycle); // เพิ่ม index และวนรอบเมื่อถึงสีสุดท้าย

            // เพิ่ม event ลงใน array
            $events[] = [
                'title' => $projectcar['carrier'] . ' ทะเบียน ' . $projectcar['id_car'] . ' - ' . $projectcar['name'] . ' ' . $projectcar['last_name'],
                'start' => $startDate,
                'end' => $endDatePlusOne,
                'backgroundColor' => $currentColor,
                'borderColor' => $currentColor
            ];
        } else {
            // ถ้าวันที่ไม่ถูกต้อง ข้ามรายการนี้ไป
            continue;
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">

    <!-- FullCalendar Localization -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/th.min.js"></script>

    <link href="https://unpkg.com/tippy.js@6/animations/scale.css" rel="stylesheet">
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
 
    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            margin-bottom: 100px; /* เพิ่ม margin-bottom เพื่อให้ footer ไม่ทับเนื้อหา */
        }

        footer {
            position: relative; /* เปลี่ยนจาก fixed เป็น relative */
            left: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
        }

        .h1, .h2 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }

        #calendar {
            position: relative;
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 10px;
            background-color: #FFFFCC;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<header>
    <?php require_once "./nav/navbar_boss.php"; ?>
</header>

<main>
    <div style="text-align:center;">
        <h1 class="h1">ตารางไป</h1><h2 class="h2">ราชการ</h2>
    </div>
    
    <div id="calendar"></div>
</main>

<footer>
    <?php require_once "./footer/footer.php"; ?>
</footer>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'th', // กำหนดให้ปฏิทินใช้ภาษาไทย
            events: <?php echo json_encode($events); ?>,
            buttonText: {
                today: 'วันนี้' // เปลี่ยน "today" เป็น "วันนี้"
            },
            eventMouseEnter: function(info) {
                // ใช้ Tippy.js ในการแสดง popup
                tippy(info.el, {
                    content: info.event.title, // แสดงชื่อของ event ใน popup
                    placement: 'top',          // ตำแหน่ง popup (บน)
                    animation: 'scale',        // กำหนด animation
                    theme: 'light',            // ใช้ธีม light
                });
            }
        });
        calendar.render();
    });
</script>
</body>
</html>
