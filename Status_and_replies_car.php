<?php
session_start();

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


$notification = '';

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];
 
    try { 
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // คำสั่ง SQL สำหรับดึงข้อมูลการใช้รถที่ id_us_car ตรงกับ id_us ใน session
        $sql = "SELECT * FROM use_car WHERE id_us_car = :id_us_car";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us_car', $id_us, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch ข้อมูลการใช้รถ
        $use_car_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบการเปลี่ยนแปลงสถานะ
        $sqlCheck = "SELECT COUNT(*) as count FROM use_car WHERE id_us_car = :id_us_car AND status_car IN ('อนุมัติ', 'ไม่อนุมัติ') AND checked = 0";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':id_us_car', $id_us, PDO::PARAM_INT);
        $stmtCheck->execute();

        $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($resultCheck['count'] > 0) {
            $notification = 'มีการเปลี่ยนแปลงสถานะ ' . $resultCheck['count'] . ' รายการ';

            // อัพเดตข้อมูลว่าได้ทำการตรวจสอบแล้ว
            $updateSql = "UPDATE use_car SET checked = 1 WHERE id_us_car = :id_us_car AND status_car IN ('อนุมัติ', 'ไม่อนุมัติ')";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':id_us_car', $id_us, PDO::PARAM_INT);
            $updateStmt->execute();
        }

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
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
            background-size: cover;
            background-repeat: no-repeat;
            margin-bottom: 200px;
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
    </style>

</head> 
<body>
    <content>
        <div style="overflow-x:auto;">
            <div class="table-container">
                <table id="myTable">
                    <thead>
                        <tr>
                            <th style="width: 15%">เรื่อง/งานที่ไปราชการ</th>
                            <th style="width: 15%">โดยยานพาหนะ</th>
                            <th style="width: 10%">ทะเบียน</th>
                            <th style="width: 15%">ขอเบิกค่าใช้จ่ายจาก</th>
                            <th style="width: 15%">สถานะการขออนุญาติ</th>
                            <th style="width: 10%">การตอบกลับ</th>
                            <th style="width: 10%">บันทึกไฟล์ PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($use_car_requests as $request) : ?> 
                            <tr>
                                <td><?php echo htmlspecialchars($request['purposes']); ?></td>
                                <td><?php echo htmlspecialchars($request['carrier']); ?></td>
                                <td><?php echo htmlspecialchars($request['id_car']); ?></td>
                                <td><?php echo htmlspecialchars($request['about_costs']); ?></td>
                                <td class="<?php
                                    if ($request['status_car'] === 'รออนุมัติ') {
                                        echo 'status-pending';
                                    } elseif ($request['status_car'] === 'อนุมัติ') {
                                        echo 'status-approved';
                                    } elseif ($request['status_car'] === 'ไม่อนุมัติ') {
                                        echo 'status-rejected';
                                    }
                                ?>"><?php echo htmlspecialchars($request['status_car']); ?></td>
                                <td>
                                    <?php
                                    if ($request['status_car'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    } elseif ($request['status_car'] === 'อนุมัติ' || $request['status_car'] === 'ไม่อนุมัติ') {
                                        echo isset($request['comments']) ? htmlspecialchars($request['comments']) : 'ไม่มีความคิดเห็น';
                                    }
                                    ?>
                                </td>
                                <td class="t1" style="text-align: center; vertical-align: middle;">
                                    <?php
                                    if ($request['status_car'] === 'อนุมัติ') {
                                        // ใช้ echo ซ้อนกันแบบนี้ไม่ถูกต้อง ต้องเปลี่ยนเป็นการใช้ตัวแปรหรือการรวมสตริง
                                        $link = "generate_car_pdf.php?idusecar=" . htmlspecialchars($request['idusecar']);
                                        echo '<a href="' . $link . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                                                    <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                                                    <path d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                                                </svg>
                                            </a>';
                                    } elseif ($request['status_car'] === 'ไม่อนุมัติ') {
                                        echo 'ไม่ถูกอนุมัติ';
                                    } elseif ($request['status_car'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
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
    <?php if ($notification) : ?>
        <script>
            alert('<?php echo htmlspecialchars($notification); ?>');
        </script>
    <?php endif; ?>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
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
                "order": [] // ปิดการเรียงลำดับ
            });

            function checkForUpdates() {
                $.ajax({
                    url: 'check_updates.php',
                    method: 'GET',
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.changed > 0) {
                            alert('มีการเปลี่ยนแปลงสถานะ ' + data.changed + ' รายการ');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request failed:', status, error);
                    }
                });
            }

            // ตั้งเวลาให้เรียกตรวจสอบทุก 30 วินาที
            setInterval(checkForUpdates, 30000);
        });
    </script>
</body>
</html>