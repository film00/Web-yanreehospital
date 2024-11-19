<?php
    session_start();
   
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

            // Update count query for projects
            $countSql = "SELECT COUNT(*) AS total FROM project WHERE status_pro = 'รออนุมัติ'";
            $countStmt = $conn->prepare($countSql);
            $countStmt->execute();
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $totalProjects = $countResult['total'];
            $totalPagesForProjects = ceil($totalProjects / $perPage);

            // Fetch project data with pagination
            // Fetch project data with pagination for all users
            $projectStmt = $conn->prepare("SELECT * FROM project WHERE status_pro = 'รออนุมัติ' ORDER BY time DESC LIMIT :perPage OFFSET :offset");
            $projectStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
            $projectStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $projectStmt->execute();
            $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch all use_car data without pagination
            $pageUseCar = isset($_GET['pageUseCar']) ? (int)$_GET['pageUseCar'] : 1;
            $offsetUseCar = ($pageUseCar - 1) * $perPage;

            $useCarStmt = $conn->prepare("SELECT * FROM use_car WHERE status_car = 'รออนุมัติ' ORDER BY from_date DESC LIMIT :perPage OFFSET :offset");
            $useCarStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
            $useCarStmt->bindParam(':offset', $offsetUseCar, PDO::PARAM_INT);
            $useCarStmt->execute();
            $useCars = $useCarStmt->fetchAll(PDO::FETCH_ASSOC);

            // Update count query for use_car
            $countUseCarSql = "SELECT COUNT(*) AS total FROM use_car WHERE status_car = 'รออนุมัติ'";
            $countUseCarStmt = $conn->prepare($countUseCarSql);
            $countUseCarStmt->execute();
            $countUseCarResult = $countUseCarStmt->fetch(PDO::FETCH_ASSOC);
            $totalUseCars = $countUseCarResult['total'];
            $totalPagesForUseCar = ceil($totalUseCars / $perPage);



        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    } else {
        header("Location: index.php"); 
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Data</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 90%; /* กำหนดความกว้างของตาราง */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #1E90FF;
            color: white;
        }
        /* เปลี่ยนพื้นหลังของแถวคู่เป็นสีเทาอ่อน */
        td:nth-child(even) {
            background-color: #f2f2f2; /* สีเทาอ่อนสำหรับแถวคู่ */
        }
        /* แถวคี่มีพื้นหลังเป็นสีขาว */
        td:nth-child(odd) {
            background-color: #ffffff; /* สีขาวสำหรับแถวคี่ */
        }
        td:hover {
            background-color: #e0e0e0; /* สีเทาอ่อนเมื่อชี้เมาส์ */
        }
    </style>
</head>
<body>

<table id="projectTable">
    <thead>
        <tr>
            <th>ชื่อ-นามสกุล</th>
            <th>ตำแหน่ง</th>
            <th>รถที่ใช้</th>
            <th>วันที่เริ่มใช้</th>
            <th>วันที่สิ้นสุด</th>
            <th>สถานะ</th>
            <th>การอนุมัติ</th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($useCars) && is_array($useCars)) : ?>
        <?php foreach ($useCars as $useCar) : ?>
            <tr>
                <td><?php echo $useCar['name'] . ' '; ?><?php echo $useCar['last_name']; ?></td>
                <td><?php echo $useCar['rank']; ?></td>
                <td><?php echo $useCar['go_to']; ?></td>
                <td><?php echo $useCar['from_date']; ?></td>
                <td><?php echo $useCar['up_date']; ?></td>
                <td><?php echo $useCar['status_car']; ?></td>
                <td style="text-align: center; vertical-align: middle;">
                    <a href="viewusecar.php?id=<?php echo $useCar['idusecar']; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                            <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">ไม่มีรายการรถที่ใช้งาน</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#projectTable').DataTable({
            "language": {
                "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                "zeroRecords": "ไม่พบข้อมูล",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                "infoFiltered": "(กรองจาก _MAX_ รายการทั้งหมด)",
                "search": "ค้นหา:",
                "paginate": {
                    "first": "แรก",
                    "last": "สุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                }
            },
            "order": [] // ปิดการเรียงลำดับจาก DataTables
        });
    });
</script>

</body>
</html>
