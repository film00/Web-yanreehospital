<?php
session_start();
require_once "./nav/navbar.php";
require_once "./footer/footer.php";
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $userStmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $userStmt->bindParam(':id_us', $id_us);
        $userStmt->execute();
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10; // You can adjust this value
        $offset = ($page - 1) * $perPage;
        $countSql = "SELECT COUNT(*) AS total FROM project WHERE id_us_pro = :id_us";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bindParam(':id_us', $id_us);
        $countStmt->execute();
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalProjects = $countResult['total'];
        $totalPagesForProjects = ceil($totalProjects / $perPage);
        $projectStmt = $conn->prepare("SELECT name_project, Processing_time, status_pro FROM project WHERE id_us_pro = :id_us LIMIT :perPage OFFSET :offset");
        $projectStmt->bindParam(':id_us', $id_us);
        $projectStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $projectStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $projectStmt->execute();
        $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
        $pageUseCar = isset($_GET['pageUseCar']) ? (int)$_GET['pageUseCar'] : 1;
        $offsetUseCar = ($pageUseCar - 1) * $perPage;
        $useCarStmt = $conn->prepare("SELECT * FROM use_car LIMIT :perPage OFFSET :offset");
        $useCarStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $useCarStmt->bindParam(':offset', $offsetUseCar, PDO::PARAM_INT);
        $useCarStmt->execute();
        $useCars = $useCarStmt->fetchAll(PDO::FETCH_ASSOC);
        $countUseCarSql = "SELECT COUNT(*) AS total FROM use_car";
        $countUseCarStmt = $conn->prepare($countUseCarSql);
        $countUseCarStmt->execute();
        $countUseCarResult = $countUseCarStmt->fetch(PDO::FETCH_ASSOC);
        $totalUseCars = $countUseCarResult['total'];
        $totalPagesForUseCar = ceil($totalUseCars / $perPage);

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
   

    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover;
            height: auto;
            display: flex;
            flex-direction: column;
            padding-bottom: 4em;
        }

        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px 5px -20px;
            padding: 0px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .h1 {
            color: #000;
            font-size: 50px;
        }

        .h2, .h3 {
            color: #ffffff;
            font-size: 50px;
        }

        .h3 {
            font-size: 30px;
        }

        .content {
            padding: 20px;
            display: flex;
            flex: 1;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
        }

        .d-flex {
            display: flex;
            flex-direction: row; 
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }

        td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }

        th:last-child, td:last-child {
            border-right: none;
        }

        th {
            background-color: #1e90ff;
            color: white;
            font-family: "Prompt", sans-serif;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tr:hover {
            background-color: #e0e0e0; 
        }

        .pagination {
            text-align: center;
            padding: 20px 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #006400;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #004d00;
        }

        .pagination a:hover {
            background-color: #004d00;
        }

        /* สำหรับหน้าจอขนาดเล็กมือถือ*/
        @media (max-width: 767px) {
            .d-flex {
                flex-direction: column;
            }

            .sidebar-column {
                width: 7rem;
                margin-right: 0;
                margin-bottom: 30px;
            }

            .main-column {
                width: 5rem;
                margin-bottom: 30px;
            }

            .nav-link {
                font-size: 14px;
            }

            .h1, .h2, .h3 {
                font-size: 50px;
            }

            .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px 5px -20px;
            padding: 0px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        
        .sidebar-column {
                display: none;
            }

            .main-column {
                width: 100%;
            }

       
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .d-flex {
                flex-direction: row;
            }

            .sidebar-column {
                width: 10rem;
                margin-right: 20px;
                margin-bottom: 50px;
            }

            .main-column {
                width: calc(100% - 20rem);
                margin-bottom: 50px;

            }

            .sidebar-column {
                display: none;
            }

            .main-column {
                width: 100%;
            }
        }

        
    </style>
</head>

<body>
<div style="text-align:center;">
        <h1 class="h1">ประวัติ</h1><h2 class="h2">คำร้องขอ</h2>
    </div>
    
    <div class="d-flex flex-row flex-shrink-0">
        <!-- Sidebar column -->
        <div class="sidebar-column">
            <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
                <li class="border-3 border-primary rounded-3">
                    <a href="pro_history.php" class="nav-link py-3 border-bottom rounded-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-pass-fill" viewBox="0 0 16 16">
                            <path d="M10 0a2 2 0 1 1-4 0H3.5A1.5 1.5 0 0 0 2 1.5v13A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 12.5 0zM4.5 5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1m0 2h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1"/>
                        </svg><br>
                        <span>คำร้องขอโครงการ</span>
                    </a>
                </li> 
                <li class="border-3 border-primary rounded-3">
                    <a href="car_history.php" class="nav-link py-3 border-bottom rounded-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
                            <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                        </svg><br>
                            <span>คำร้องขออนุญาตไปราชการและขอใช้รถราชการ</span>
                    </a>
                </li>
            </ul>
        </div>
            <!-- Main content column -->
            <div class="main-column">
                <!-- Content will be loaded here -->
            </div>
        </div>
        <?php require_once "./footer/footer.php"; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
        <script>
        $(document).ready(function() {
            $('.main-column').load('pro_history.php');

            $('a[href="pro_history.php"]').click(function(e) {
                e.preventDefault();
                $('.main-column').load('pro_history.php');
            });

            $('a[href="car_history.php"]').click(function(e) {
                e.preventDefault();
                $('.main-column').load('car_history.php');
            });
        });

        $(document).ready(function() {
            
            $(window).resize(adjustLayout);
            
            adjustLayout();
        });

        </script>
</body>

</html>

//ไม้ได้ใช้
