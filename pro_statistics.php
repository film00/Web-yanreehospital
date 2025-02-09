<?php
    session_start();
    require_once "./nav/navbar_addmin.php";
    require_once "./footer/footer.php";

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

    $conn = null;
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
    <link rel="stylesheet" href="styles.css">
    <title>Navbar with Dropdown</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
            background-image: url('../background.jpg'); 
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
            background-repeat: no-repeat; /* ไม่ให้รูปภาพซ้ำซ้อน */
        }

        content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .password-toggle {
            position: relative;
        }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }

        button {
            background-color: #84dcc6;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="button"] {
            background-color: #d9534f;
        }

    </style>
</head>
<body>

    <script src="script.js"></script>
    <head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        #chart-container {
            max-width: 800px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div id="chart-container">
        <canvas id="myChart"></canvas>
    </div>
    <script>
        // ตัวอย่างข้อมูลสถิติโครงการ
        const projectData = {
            labels: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤษจิกายน','ธันวาคม'],
            datasets: [{
                label: 'Completed Projects',
                data: [5, 8, 12, 7, 10],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        // กำหนดค่าแผนภูมิ
        const projectConfig = {
            type: 'bar',
            data: projectData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // สร้างแผนภูมิ
        const projectCtx = document.getElementById('myChart').getContext('2d');
        const myProjectChart = new Chart(projectCtx, projectConfig);
    </script>

</body>
</html>

