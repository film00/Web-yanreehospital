<?php
session_start();
require_once "./nav/navbar.php";
require_once "./footer/footer.php";

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


if(isset($_SESSION['id_us'])){
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch user data
        $userStmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $userStmt->bindParam(':id_us', $id_us);
        $userStmt->execute();
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        // Define pagination variables for projects
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10; // You can adjust this value
        $offset = ($page - 1) * $perPage;

        // Count total projects for pagination without restricting to a specific user
        $countSql = "SELECT COUNT(*) AS total FROM project";
        $countStmt = $conn->prepare($countSql);
        $countStmt->execute();
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalProjects = $countResult['total'];
        $totalPagesForProjects = ceil($totalProjects / $perPage);

        // Fetch project data with pagination
        // Fetch project data with pagination for all users
        $projectStmt = $conn->prepare("SELECT * FROM project LIMIT :perPage OFFSET :offset");
        $projectStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $projectStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $projectStmt->execute();
        $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all use_car data without pagination
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

    } catch(PDOException $e) {
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
        /* เพิ่มสีพื้นหลังให้กับหัวตาราง */
        #projectTable thead th {
            background-color: #1E90FF;
            color: white;
            border: 1px solid #ddd; /* เพิ่มเส้นรอบหัวตาราง */
        }

        /* เพิ่มเส้นรอบตารางทั้งหมด */
        #projectTable {
            border-collapse: collapse;
            border: 1px solid #000; /* เส้นรอบตาราง */
            width: 100%; /* ให้ตารางขยายเต็มความกว้างของ container */
        }

        #projectTable th, #projectTable td {
            border: 1px solid #000; /* เส้นรอบเซลล์ */
            padding: 8px; /* เพิ่มระยะห่างภายในเซลล์ */
        }

        #projectTable tr:nth-child(even) {
            background-color: #f2f2f2; /* สีเทาอ่อน */
        }

        #projectTable tr:nth-child(odd) {
            background-color: #ffffff; /* สีขาว */
        }

        #projectTable tr:hover {
            background-color: #e0e0e0; /* สีเทาอ่อนเมื่อชี้เมาส์ */
        }

        body {
            font-family: "Prompt", sans-serif;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
            background-image: url('./image/background.JPG'); /* กำหนดพื้นหลังเป็นรูปภาพ */
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
            margin-bottom: 3rem;
        }

        h2 {
            font-family: "Prompt", sans-serif;
        }

        .prompt-bold {
            font-family: "Prompt", sans-serif;
            font-weight: 700;
            font-style: normal;
        } 

        content {
            padding: 20px;
            font-family: "Prompt", sans-serif;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: right;
        }

        .us {
            font-size: 30px;
            margin-top: 20px;
            text-align: center;
        }

        .grid {
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        table {
            width: 100%;
            border-collapse: collapse;
            
        }

        td{
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            white-space: nowrap;
           
        }
        th {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            background-color: #191970;
            color: white;
            white-space: nowrap;
           
        }

        th:last-child,
        td:last-child {
            border-right: none;
        }


        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            
            padding: 20px;
            border: 1px solid #888;
            width: 35%;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            text-align: center;
        }

        p.modal-text {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p.small-text {
            font-size: 16px;
            margin-top: 10px;
        }
        
        p.smalless-text {
            font-size: 12px;
            margin-top: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        #projectDetails {
            white-space: pre-wrap;
        }

        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 10px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
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

        .add-member-button {
            margin-left: auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-align: center;
            display: inline-block;
            margin-left: 0px;
        }

        .add-member-button:hover {
            background-color: #45a049;
        }

        .search-container {
            float: left;
            margin-right: auto;
        }

        input[type="text"] {
            padding: 5px;
            border-radius: 5px;
        }

        .action-link {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            
        }
       

        .action-text {
            position: absolute;
            bottom: -20px;
            transform: translateX(-50%);
            background-color: white;
            color: black;
            font-size: 12px;
            padding: 2px 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, bottom 0.3s;
            border-radius: 10px;
        }

        .action-link:hover .action-text {
            opacity: 1;
            bottom: 35px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .confirm-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-button:hover {
            background-color: #45a049;
        }

        .cancel-button {
            background-color: #f44336; /* สีแดง */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-button:hover {
            background-color: #d32f2f; /* สีแดงเข้มเมื่อเอาเมาส์ไปชี้ */
        }

        th {
            background-color: #191970;
            color: white;
           
        }


        th.width-9 {
            width: 10%; 
        }
                
        .container {
            display: flex; /* ใช้ Flexbox สำหรับจัดตำแหน่งลิงก์ */
            justify-content: space-between; /* จัดตำแหน่งให้มีพื้นที่ระหว่างลิงก์เท่ากัน */
            align-items: center; /* จัดตำแหน่งกลางแนวตั้ง */
            width: 100%; /* ให้คอนเทนเนอร์มีความกว้างเต็มที่ */
        }

        table {
            width: 100%; /* ให้ตารางใช้ความกว้างทั้งหมดของคอนเทนเนอร์ */
            border-collapse: collapse; /* รวมขอบของเซลล์ให้ไม่ทับซ้อน */
        }
        /* ทำให้เนื้อหาหลักขยายเต็มหน้าจอ */
        .content {
           
            padding: 20px;
            max-width: 100%;
            overflow-x: auto; /* การเลื่อนแนวนอนเมื่อเนื้อหากว้างกว่า */
            box-sizing: border-box;
        }

        nav {
            width: 100%;
        }
        /* สำหรับอุปกรณ์มือถือ */
        @media (max-width: 768px) {
            .navbar {
                background-color: #ffffff; /* สีพื้นหลังสำหรับ navbar ในมือถือ */
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* เงาสำหรับ navbar */
            }
        }

        /* กำหนดความกว้างของเนื้อหา */
        .content-wrapper {
            max-width: 100%; /* จำกัดความกว้างของเนื้อหาตามขนาดของ viewport */
            overflow-x: auto; /* เลื่อนแนวนอนเมื่อเนื้อหากว้างกว่า */
            background-color: #ffffff;
        }
        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 10px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }
        
    </style>
</head>
<body>
    <content>

        <div style="text-align:center;">
            <h1 class="h1">ประวัติ</h1><h2 class="h2">การทำโครงการ</h2>
        </div>
    <div <div class="content-wrapper">
    <div class="content">
    
            <table id="projectTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>หัวข้อคำร้องขอโครงการ</th>
                        <th>ชื่อ-สกุล</th>
                        <th>วันยื่นเรื่อง</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['name_project']); ?></td>
                            <td><?php echo htmlspecialchars($project['name_us']); ?></td>
                            <td><?php echo htmlspecialchars($project['time']); ?></td>
                            <td><?php echo htmlspecialchars($project['status_pro']); ?></td>
                        </tr>
                            <?php endforeach; ?>
                </tbody>
            </table> 
            <?php if (empty($projects)): ?>
                <p style="text-align: center; margin-top: 20px;">ไม่มีข้อมูล</p>
            <?php endif; ?>
            <div class="pagination"></div>
        </div>
    </content>  

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#projectTable').DataTable({
                "order": [[2, "desc"]], // จัดเรียงตามคอลัมน์วันที่ (คอลัมน์ที่ 3) จากใหม่สุดไปเก่าสุด
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Thai.json"
                }
            }); 
        });
    </script>
</body>
</html>

