<?php
session_start();
require_once "./nav/navbar.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];
} else {
    header("Location: index.php"); 
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_projects = "
    SELECT 
        (SELECT COUNT(*) 
         FROM project 
         WHERE id_us_pro = ? 
         AND status_pro IN ('อนุมัติ', 'ไม่อนุมัติ') 
         AND status_updated_at IS NOT NULL) 
        - 
        (SELECT COUNT(*) 
         FROM project 
         WHERE id_us_pro = ? 
         AND status_pro = 'อนุมัติ' 
         AND summarize_name IS NOT NULL 
         AND summarize_name <> '') AS project_count";

$stmt_projects = $conn->prepare($sql_projects);
$stmt_projects->bind_param("ii", $id_us, $id_us); 
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();
$row_projects = $result_projects->fetch_assoc();
$project_count = max(0, $row_projects['project_count']); 
$stmt_projects->close();

$sql_cars = "
    SELECT COUNT(*) AS car_count 
    FROM use_car 
    WHERE id_us_car = ? 
    AND status_car IN ('อนุมัติ', 'ไม่อนุมัติ') 
    AND status_updated_at IS NOT NULL";
    
$stmt_cars = $conn->prepare($sql_cars);
$stmt_cars->bind_param("i", $id_us);
$stmt_cars->execute();
$result_cars = $stmt_cars->get_result();
$row_cars = $result_cars->fetch_assoc();
$car_count = $row_cars['car_count'];
$stmt_cars->close();

$notification_count = $project_count + $car_count;

$_SESSION['notification_count'] = $notification_count;

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

<style>
    body {
        font-family: "Prompt", sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background-image: url('../background.jpg'); 
        background-size: cover;
        background-repeat: no-repeat; 
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin-bottom: 100px;
    }
    .h1, .h2, .h3 {
        display: inline-block; 
        margin: 5px 5px -20px;
        padding: 0px;
    }

    .h1 {
        color: #000;
        font-size: 50px;
    }

    .h2 {
        color: #ffffff;
        font-size: 50px;
    }

    .h3 {
        color: #ffffff;
        font-size: 30px;
    }

    .content {
        padding: 20px;
        display: flex;
        flex: 1;
        background-color: rgba(255, 255, 255);
        border-radius: 8px;
    }

    .d-flex {
        display: flex;
        flex-direction: row; /* แนวตั้ง */
    }

    .sidebar-column {
        width: 12rem;
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-right: 20px;
        flex-shrink: 0;
        overflow-y: auto;
        margin-bottom: 70px;
    }

    .main-column {
        flex: 1; 
        padding: 20px;
        background-color: #CCFF99;
        border-radius: 8px;
        overflow-y: auto;
        height: auto;
        margin-bottom: 70px;
    }

    .nav-link {
        border: 1px solid #ccc; 
        border-radius: 8px; 
        padding: 10px; 
        display: block;
        text-decoration: none; 
        color: #000; 
        transition: all 0.3s ease; 
    }

    .nav-link:hover {
        background-color: #f0f0f0; 
    }
 
    .nav-link svg {
        margin-bottom: 5px; 
    }
    @media (max-width: 768px) {
    .sidebar-column {
        display: none;
    }
}
</style>

</head>

<body>
    <div style="text-align:center;">
        <h1 class="h1">สถานะและ</h1>
        <h2 class="h2">การตอบกลับ</h2>
    </div> 
    <div class="d-flex flex-row flex-shrink-0">
        <!-- Sidebar column -->
        <div class="sidebar-column">
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
                <li class="border-3 border-primary rounded-3">
                    <a href="Status_and_replies_pro.php" class="nav-link py-3 border-bottom rounded-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dashboard" data-bs-original-title="Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-pass-fill" viewBox="0 0 16 16">
                            <path d="M10 0a2 2 0 1 1-4 0H3.5A1.5 1.5 0 0 0 2 1.5v13A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 12.5 0zM4.5 5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1m0 2h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1"/>
                        </svg><br>
                        <span>โครงการ</span>
                        <span style="color: red;">(<?php echo $project_count; ?>)</span>
                    </a>
                </li> 

                <li class="border-3 border-primary rounded-3">
                    <a href="Status_and_replies_car.php" class="nav-link py-3 border-bottom rounded-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dashboard" data-bs-original-title="Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5-.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.45.276l-.956 1.913Z"/>
                        </svg><br>
                        <span>ขออนุญาตไปราชการ</span>
                        <span style="color: red;">(<?php echo $car_count; ?>)</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-column">
            <h3 class="h3">โปรดเลือกจากเมนูด้านซ้าย</h3>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.main-column').load('Status_and_replies_pro.php');
        $('a[href="Status_and_replies_pro.php"]').click(function(event) {
            event.preventDefault(); // Prevent the default action of the link
            // Load content from indexform.php into main-column
            $('.main-column').load($(this).attr('href'));
        });
        $('a[href="Status_and_replies_car.php"]').click(function(event) {
            event.preventDefault(); // Prevent the default action of the link
            // Load content from users_record.php into main-column
            $('.main-column').load($(this).attr('href'));
        });
    });
</script>
</body>
</html>
