<?php
session_start();
require_once "./nav/navbar_boss.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

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
    $colorCycle = ['#333399', '#556B2F', '#A0522D', '#9932CC', '#DA70D6', '#663399', '#336600'];
    $colorIndex = 0; 

    $stmt = $conn->prepare("SELECT name_project, name_us, Processing_time, status_pro FROM project");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($projects as $project) {
        if ($project['status_pro'] !== 'อนุมัติ') {
            continue; 
        }
        $processingTime = explode(" ถึง ", $project['Processing_time']);
        if (count($processingTime) == 2) {
            $startDate = DateTime::createFromFormat('d/m/Y', trim($processingTime[0]))->format('Y-m-d');
            $endDate = DateTime::createFromFormat('d/m/Y', trim($processingTime[1]))->format('Y-m-d');
            $endDatePlusOne = (new DateTime($endDate))->modify('+1 day')->format('Y-m-d');
            $currentColor = $colorCycle[$colorIndex];
            $colorIndex = ($colorIndex + 1) % count($colorCycle); 
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
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
            margin-bottom: 60px; 
        }

        footer {
            position: fixed; 
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000; 
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
        <h1 class="h1">ตารางดำเนิน</h1><h2 class="h2">โครงการ</h2>
    </div>
    
    <div id="calendar"></div>
</main>

<footer>
    <?php require_once "./footer/footer.php"; ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'th', 
            events: <?php echo json_encode($events); ?>, 
            buttonText: {
                today: 'วันนี้' 
            },
            eventMouseEnter: function(info) {  
                tippy(info.el, {
                    content: info.event.title, 
                    placement: 'top',          
                    animation: 'scale',       
                    theme: 'light',            
                });
            }
        });
        calendar.render();
    });
</script>
</body>
</html>
