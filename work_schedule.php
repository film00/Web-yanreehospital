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

    $stmt = $conn->prepare("SELECT name_project, name_us, Processing_time, status_pro FROM project");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($projects as $project) {
        // ตรวจสอบสถานะโครงการ
        if ($project['status_pro'] !== 'อนุมัติ') {
            continue; // ข้ามไปยังรายการถัดไปหากสถานะไม่ใช่ "อนุมัติ"
        }

        // แปลงข้อมูล Processing_time เป็นวันที่เริ่มต้นและวันที่สิ้นสุด
        $processingTime = explode(" ถึง ", $project['Processing_time']);
        if (count($processingTime) == 2) {
            $startDate = DateTime::createFromFormat('d/m/Y', trim($processingTime[0]))->format('Y-m-d');
            $endDate = DateTime::createFromFormat('d/m/Y', trim($processingTime[1]))->format('Y-m-d');

            // เพิ่มวันถัดไปใน end date
            $endDatePlusOne = (new DateTime($endDate))->modify('+1 day')->format('Y-m-d');

            // ใช้สีตามลำดับใน array และวนรอบเมื่อถึงสีสุดท้าย
            $currentColor = $colorCycle[$colorIndex];
            $colorIndex = ($colorIndex + 1) % count($colorCycle); // เพิ่ม index และวนรอบเมื่อถึงสีสุดท้าย

            $events[] = [
                'title' => $project['name_project'] . ' - ' . $project['name_us'],
                'start' => $startDate,
                'end' => $endDatePlusOne,
                'backgroundColor' => $currentColor,
                'borderColor' => $currentColor
            ];
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
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

    <!-- Tippy.js CSS และ JS -->
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
            margin-bottom: 60px; /* เพิ่มระยะห่างที่ด้านล่างให้ฟุตเตอร์ไม่ทับเนื้อหา */
        }

        footer {
            position: fixed; /* ใช้ fixed เพื่อให้ฟุตเตอร์อยู่บนสุด */
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* ให้ฟุตเตอร์มีตำแหน่งสูงกว่าเนื้อหา */
            background-color: #f8f9fa; /* สีพื้นหลังเพื่อความชัดเจน */
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
            position: relative; /* ใช้ relative เพื่อให้มั่นใจว่า z-index ทำงานได้ */          
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 10px;
            background-color: #FFFFCC; /* สีครีม */
            border-radius: 8px; /* ทำให้มุมของปฏิทินมีความโค้ง */
        }
    </style>
</head>
<body>

<header>
    <?php require_once "./nav/navbar_boss.php"; ?>
</header>

<main>
    <div style="text-align:center;">
        <h1 class="h1">ตารางดำเนิน</h1><h2 class="h2">โครงการ</h2>
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
            events: <?php echo json_encode($events); ?>, // ต้องใส่จุลภาคตรงนี้
            buttonText: {
                today: 'วันนี้' // เปลี่ยน "today" เป็น "วันนี้"
            },
            eventMouseEnter: function(info) { // เพิ่ม eventMouseEnter ในการตั้งค่า
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
