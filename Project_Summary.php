<?php
session_start();

if (!isset($_SESSION['id_us'])) {
    echo "ไม่พบข้อมูลผู้ใช้งาน";
    exit;
}

$id_us = $_SESSION['id_us'];

require_once "./nav/navbar_boss.php";
require_once "./footer/footer.php";
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Table</title>
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
            background-image: url('../background.jpg'); 
            background-size: cover; 
        }

        .container {
            margin-bottom: 100px; 
            background-color: #CCFF99;
            padding: 20px; 
        }

        #projectTable {
            margin-top: 0; 
            margin-bottom: 0; 
        }

        .header-text {
            text-align: center;
            margin-top: 20px;
        }

        .h1, .h2 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
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
        
        td:nth-child(even) {
            background-color: #f2f2f2; 
        }
       
        td:nth-child(odd) {
            background-color: #ffffff; 
        }
        td:hover {
            background-color: #e0e0e0; 
        }
    </style>
</head>
<body>
    <div style="text-align:center;">
        <h1 class="h1" >สรุป</h1><h2 class="h2">โครงการ</h2>
    </div>
    <div class="container">
        <table id="projectTable" class="display" style="margin-top: 20px; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>ชื่อโครงการ</th>
                    <th>ชื่อผู้ร้องขอ</th>
                    <th>ตำแหน่งผู้ร้องขอ</th>
                    <th>สรุปโครงการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
            
                $servername = "localhost";
                $username = "yanreeho_yanree_db";
                $password = "B@4N+209rhMfoT";
                $dbname = "yanreeho_yanree_db";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT name_project, name_us, rank_us_pro, summarize_name, summarize_path 
                        FROM project 
                        WHERE status_pro = 'อนุมัติ'";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["name_project"] . "</td>";
                        echo "<td>" . $row["name_us"] . "</td>";
                        echo "<td>" . $row["rank_us_pro"] . "</td>";
                        
                        if (!empty($row['summarize_name']) && !empty($row['summarize_path'])) {
                            $fileExtension = pathinfo($row['summarize_name'], PATHINFO_EXTENSION);
                            if ($fileExtension === 'pdf') {
                                echo "<td><a href='https://yanreehospital.com/summarize/" . htmlspecialchars($row['summarize_name']) . "' target='_blank'>" . htmlspecialchars($row['summarize_name']) . "</a></td>";
                            } else {
                                echo "<td><a href='https://yanreehospital.com/summarize/" . htmlspecialchars($row['summarize_name']) . "' download>" . htmlspecialchars($row['summarize_name']) . "</a></td>";
                            }
                        } else {
                            echo "<td>รอสรุปโครงการ</td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
            "order": [] 
        });
    });
    </script>
</body>
</html>
