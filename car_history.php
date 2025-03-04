<?php
session_start();

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM use_car WHERE id_us_car = :id_us ORDER BY status_updated_at DESC");
        $stmt->bindParam(':id_us', $id_us, PDO::PARAM_INT); 
        $stmt->execute();
        $useCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    
    <style>
        #myTable thead {
            background-color: #1E90FF;
            color: white;
        }

        #myTable tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #myTable tbody tr:nth-child(odd) {
            background-color: #ffffff; 
        }
        
    </style>
</head>
<body>
    <content>
        <div class="car">
            <table id="myTable" border="1">
                <thead>
                    <tr>
                        <th>ชื่อ-นามสกุล</th>
                        <th>ตำแหน่ง</th>
                        <th>รถที่ใช้</th>
                        <th>วันที่เริ่มใช้</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>สถานะ</th>
                        <th>วันที่ยื่นเรื่อง</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($useCars as $useCar) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($useCar['name']) . ' ' . htmlspecialchars($useCar['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['rank']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['go_to']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['from_date']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['up_date']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['status_car']); ?></td>
                            <td><?php echo htmlspecialchars($useCar['status_updated_at']); ?></td> 
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination"></div>
        </div>
    </content>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "order": [[6, "desc"]], 
                "language": {
                    "decimal": "",
                    "emptyTable": "ไม่มีข้อมูลในตาราง",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                    "infoFiltered": "(กรองจาก _MAX_ รายการทั้งหมด)",
                    "lengthMenu": "แสดง _MENU_ รายการ",
                    "loadingRecords": "กำลังโหลด...",
                    "processing": "กำลังประมวลผล...",
                    "search": "ค้นหา:",
                    "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    },
                    "aria": {
                        "sortAscending": ": เปิดใช้งานการจัดเรียงจากน้อยไปมาก",
                        "sortDescending": ": เปิดใช้งานการจัดเรียงจากมากไปน้อย"
                    }
                }
            });
        });
    </script>
</body>
</html>
