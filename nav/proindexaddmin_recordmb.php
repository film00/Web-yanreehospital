<?php
session_start();

require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";

$projects = isset($_SESSION['projects']) ? $_SESSION['projects'] : [];
?>

<!DOCTYPE html>
<html lang="th">
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
    </style>
</head>
<body>
    <content>
        <div class="container" id="projectFormLink">
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