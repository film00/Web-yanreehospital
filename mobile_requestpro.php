<?php 
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "yanreeho_yanree_db"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "B@4N+209rhMfoT"; // รหัสผ่านฐานข้อมูล
$dbname = "yanreeho_yanree_db"; // ชื่อฐานข้อมูล
require_once "./nav/navbar_boss.php";
require_once "./footer/footer.php";
// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง project โดยเรียงลำดับจากใหม่ไปเก่า และเลือกเฉพาะสถานะ "รออนุมัติ"
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
        WHERE status_pro = 'รออนุมัติ' 
        ORDER BY time DESC"; // เรียงลำดับข้อมูลจากใหม่ไปเก่า

$result = $conn->query($sql);
$projects = $result->fetch_all(MYSQLI_ASSOC);
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
            <th>ตำแหน่ง</th>
            <th>วันยื่นเรื่อง</th>
            <th>สถานะ</th>
            <th>การอนุมัติ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projects as $project) : ?>
            <tr>
                <td><?php echo htmlspecialchars($project['Project Name']); ?></td>
                <td><?php echo htmlspecialchars($project['Requester Name']); ?></td>
                <td><?php echo htmlspecialchars($project['Requester Rank']); ?></td>
                <td><?php echo htmlspecialchars($project['Submission Time']); ?></td>
                <td><?php echo htmlspecialchars($project['Status']); ?></td>
                <td style="text-align: center; vertical-align: middle;">
                    <a href="viewproject.php?id=<?php echo $project['id_project']; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                            <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </a>
                </td>
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
