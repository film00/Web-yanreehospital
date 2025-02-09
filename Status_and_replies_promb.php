<?php
session_start();
require_once "./nav/navbar.php";
require_once "./footer/footer.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

  
// ตรวจสอบว่ามีค่า id_us ใน session หรือไม่
if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // คำสั่ง SQL สำหรับดึงข้อมูลโครงการที่ id_us_pro ตรงกับ id_us ใน session
        $sql = "SELECT * FROM project WHERE id_us_pro = :id_us_pro";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us_pro', $id_us, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch ข้อมูลโครงการ
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    // หากไม่มี id_us ใน session ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
    header("Location: login.php");
    exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover; /* ทำให้รูปครอบคลุมพื้นที่ทั้งหมด */
            background-repeat: no-repeat; /* ไม่ให้รูปภาพซ้ำซ้อน */
            background-attachment: fixed; /* ทำให้พื้นหลังไม่เลื่อนตามหน้า */
            width: 100%;
            min-height: 100vh; /* กำหนดความสูงขั้นต่ำให้เต็มหน้าจอ */
        }


        content {
            padding: 20px;
        }

        .us {
            font-size: 30px;
            margin-top: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        th {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th { 
            background-color: #191970;
            color: #ffffff;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .status-pending {
            color: orange;
        }

        .status-approved {
            color: green;
        }

        .status-rejected {
            color: red;
        }

        .grid {
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .search-container {
            float: right;
        }

        input[type="text"] {
            padding: 5px;
            border-radius: 5px;
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

        .pagination a:hover {
            background-color: #004d00;
        }

        .center-text {
            text-align: center;
            font-size: 30px;
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
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-height: 70vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        #projectDetails {
            white-space: pre-wrap;
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
         /* กำหนดความกว้างของเนื้อหา */
         .content-wrapper {
            max-width: 100%; /* จำกัดความกว้างของเนื้อหาตามขนาดของ viewport */
            overflow-x: auto; /* เลื่อนแนวนอนเมื่อเนื้อหากว้างกว่า */
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div style="text-align:center;">
    <h1 class="h1">สถานะและ</h1><h2 class="h2">การตอบกลับ</h2>
    </div>
    <div <div class="content-wrapper">
    <content>
        <div style="overflow-x:auto;">
            <div class="table-container">
                <table id="myTable">
                    <thead>
                        <tr>
                            <th style="width: 30%">ชื่อโครงการ</th>
                            <th style="width: 20%">ระยะเวลาดำเนินการ</th>
                            <th style="width: 10%">สถานะโครงการ</th>
                            <th style="width: 20%">การตอบกลับ</th>
                            <th style="width: 10%">บันทึกไฟล์ PDF</th>
                            <th style="width: 10%">สรุปโครงการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['name_project']); ?></td>
                                <td><?php echo htmlspecialchars($project['Processing_time']); ?></td>
                                <td class="<?php
                                    if ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'status-pending';
                                    } elseif ($project['status_pro'] === 'อนุมัติ') {
                                        echo 'status-approved';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                        echo 'status-rejected';
                                    }
                                ?>"><?php echo htmlspecialchars($project['status_pro']); ?></td>
                                <td><?php
                                    if ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ' || $project['status_pro'] === 'อนุมัติ') {
                                        echo isset($project['Comments']) ? htmlspecialchars($project['Comments']) : 'ไม่มีความคิดเห็น';
                                    }
                                ?></td>
                                <td class="t1"><a href="generate_pdf.php?id_project=<?php echo htmlspecialchars($project['id_project']); ?>">ดาวน์โหลด PDF</a></td>
                                <td class="t1">
                                    <?php
                                    if ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                        echo 'โครงการไม่ถูกอนุมัติ';
                                    } elseif ($project['status_pro'] === 'อนุมัติ') {
                                        echo '<button>อัพโหลดไฟล์สรุปโครงการ</button>';
                                    }
                                    ?>
                                </td> 
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </content>
    </div>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>
</html>
