<?php
session_start(); // เปิดการใช้งาน session

// เชื่อมต่อกับฐานข้อมูล
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us']; // ดึงค่า id_us จาก session

    // คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง project โดยกรองข้อมูลเฉพาะ id_us_pro ที่ตรงกับ id_us
    $sql = "SELECT 
                id_project,
                name_project AS 'Project Name',
                name_us AS 'Requester Name',
                rank_us_pro AS 'Requester Rank',
                reason_and_reason AS 'Reason',
                objective AS 'Objective',
                Target_group AS 'Target Group',
                Processing_time AS 'Processing Time',
                Budget AS 'Budget',
                Evaluation AS 'Evaluation',
                status_pro AS 'Status',
                summarize_name AS 'Summary Name',
                summarize_path AS 'Summary Path',
                comments AS 'Comments',
                time AS 'Submission Time',
                update_time AS 'Update Time',
                file_path AS 'File Path',
                file_name AS 'File Name',
                prefix_boss_pro AS 'Boss Prefix',
                name_boss_pro AS 'Boss Name',
                rank_boss_pro AS 'Boss Rank',
                status_updated_at AS 'Status Updated At',
                summarize AS 'Summary'
            FROM project
            WHERE id_us_pro = ? -- เพิ่มเงื่อนไขนี้
            ORDER BY time DESC"; // เรียงลำดับข้อมูลจากใหม่ไปเก่า

    // เตรียม statement และ bind ค่า id_us
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_us);
    $stmt->execute();
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
} else {
    echo "ผู้ใช้ยังไม่ได้เข้าสู่ระบบ.";
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
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
            <th>หัวข้อคำร้องขอ</th>
            <th>ชื่อ-สกุล</th>
            <th>วันยื่นเรื่อง</th>
            <th>สถานะ</th> 
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projects as $project) : ?>
            <tr>
                <td><?php echo htmlspecialchars($project['Project Name']); ?></td>
                <td><?php echo htmlspecialchars($project['Requester Name']); ?></td>
                <td><?php echo htmlspecialchars($project['Submission Time']); ?></td>
                <td><?php echo htmlspecialchars($project['Status']); ?></td>
            </tr>
        <?php endforeach; ?>
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
