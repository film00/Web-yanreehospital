<?php
session_start();
require_once "./nav/navbar_boss.php";
require_once "./footer/footer.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

// ตรวจสอบ session
if (!isset($_SESSION['id_us'])) {
    header("Location: index.php");
    exit();
}

$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

try {
    // สร้างการเชื่อมต่อฐานข้อมูล
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ดึงข้อมูลจำนวนโครงการตามสถานะและโครงการที่มี summarize_name
    $sql = "
        SELECT 
            SUM(CASE WHEN p.status_pro = 'รออนุมัติ' AND YEAR(p.time) = :year THEN 1 ELSE 0 END) AS waiting_count,
            SUM(CASE WHEN p.status_pro = 'ไม่อนุมัติ' AND YEAR(p.time) = :year THEN 1 ELSE 0 END) AS rejected_count,
            SUM(CASE WHEN p.status_pro = 'อนุมัติ' AND YEAR(p.time) = :year THEN 1 ELSE 0 END) AS approved_count,
            SUM(CASE WHEN p.status_pro = 'อนุมัติ' AND p.summarize_name IS NOT NULL AND YEAR(p.time) = :year THEN 1 ELSE 0 END) AS approved_with_summary_count
        FROM project p
        WHERE YEAR(p.time) = :year
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
    $stmt->execute();
    $projectData = $stmt->fetch(PDO::FETCH_ASSOC);

    // กำหนดค่าเริ่มต้นถ้าหากไม่มีข้อมูล
    if (!$projectData) {
        $projectData = [
            'waiting_count' => 0,
            'rejected_count' => 0,
            'approved_count' => 0,
            'approved_with_summary_count' => 0
        ];
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> <!-- เพิ่มไลบรารี ApexCharts -->

    <style>
        body {
            font-family: "Prompt", sans-serif;
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover;
        }

        .chart-container {
            max-width: 800px;
            margin: 50px auto;
            margin-top: 50px;
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .h1, .h2 {
            display: inline-block;
            margin: 5px;
            font-size: 50px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }

        button {
            display: none;
        }
    </style>
</head>
<body>
    <div style="text-align:center;">
        <h1 class="h1">สถิติ</h1><h2 class="h2">โครงการ</h2>
    </div>

    <!-- ฟิลเตอร์เลือกปี -->
    <form method="GET" style="text-align:center; margin-bottom: 20px;">
        <label for="year">เลือกปี พ.ศ.:</label>
        <select name="year" id="year" onchange="this.form.submit()">
            <?php
            $currentYearAD = date('Y'); // ปี ค.ศ.
            $currentYearBE = $currentYearAD + 543; // ปี พ.ศ.
            for ($i = $currentYearAD; $i >= 2020; $i--) { // แสดงปีตั้งแต่ 2020 จนถึงปีปัจจุบัน
                $yearBE = $i + 543; // แปลงเป็นปี พ.ศ.
                echo "<option value='$i'" . ($i == $selectedYear ? ' selected' : '') . ">$yearBE</option>";
            }
            ?>
        </select>
    </form>

    <div class="chart-container" id="chart" style="margin-bottom: 100px;"></div> <!-- เพิ่ม ID ให้ chart -->

    <script>
        // ข้อมูลโครงการรวม
        const waitingCount = <?= json_encode((int)$projectData['waiting_count']) ?>;
        const rejectedCount = <?= json_encode((int)$projectData['rejected_count']) ?>;
        const approvedCount = <?= json_encode((int)$projectData['approved_count']) ?>;
        const approvedWithSummaryCount = <?= json_encode((int)$projectData['approved_with_summary_count']) ?>;

        var colors = ['#FFCC00', '#FF6666', '#66FF66', '#3399FF'];

        var options = {
        series: [{
            name: "จำนวน",  // เปลี่ยนเป็น "จำนวน"
            data: [waitingCount, rejectedCount, approvedCount, approvedWithSummaryCount]
        }],
        chart: {
            height: 350,
            type: 'bar',
            events: {
                click: function(chart, w, e) {
                    // ถ้าต้องการจัดการเมื่อมีการคลิกในกราฟ
                }
            }
        },
        colors: colors,
        plotOptions: {
            bar: {
                columnWidth: '45%',
                distributed: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: [
                'รออนุมัติ', 'ไม่อนุมัติ', 'อนุมัติ', 'สรุปโครงการ'
            ],
            labels: {
                style: {
                    colors: colors,
                    fontSize: '12px'
                }
            }
        },
        // ปุ่มดาวน์โหลด
        toolbar: {
            tools: {
                download: 'ดาวน์โหลด',  // เปลี่ยนเป็น "ดาวน์โหลด"
                svg: 'ดาวน์โหลด SVG',   // เปลี่ยนเป็น "ดาวน์โหลด SVG"
                png: 'ดาวน์โหลด PNG',   // เปลี่ยนเป็น "ดาวน์โหลด PNG"
                csv: 'ดาวน์โหลด CSV'    // เปลี่ยนเป็น "ดาวน์โหลด CSV"
            }
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>


</body>
</html>
