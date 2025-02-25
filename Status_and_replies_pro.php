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
        $sql = "SELECT * FROM project WHERE id_us_pro = :id_us_pro";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us_pro', $id_us, PDO::PARAM_INT);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
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
                            <th style="width: 30%">ชื่อโครงการ</th>
                            <th style="width: 15%">ระยะเวลา<br>ดำเนินการ</th>
                            <th style="width: 10%">สถานะโครงการ</th>
                            <th style="width: 20%">การตอบกลับ</th>
                            <th style="width: 10%">บันทึกไฟล์ PDF</th>
                            <th style="width: 10%">สรุปโครงการ</th>
                            <th style="width: 10% height: 10px ";>สถานะสรุปโครงการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($projects as $project) : ?>
                            <tr>
                                <td style="max-height: 50px; overflow: auto"><?php echo htmlspecialchars($project['name_project']); ?></td>
                                <td style="max-height: 50px; overflow: auto"><?php echo htmlspecialchars($project['Processing_time']); ?></td>
                                <td class="<?php
                                    if ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'status-pending';
                                    } elseif ($project['status_pro'] === 'อนุมัติ') {
                                        echo 'status-approved';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                        echo 'status-rejected';
                                    }
                                ?>" style="max-height: 50px; overflow: auto"><?php echo htmlspecialchars($project['status_pro']); ?>
                                </td>
                                <td style="max-height: 50px; overflow: auto">
                                    <?php
                                    if ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ' || $project['status_pro'] === 'อนุมัติ') {
                                        $comments = !empty($project['comments']) ? htmlspecialchars($project['comments']) : 'ไม่มีความคิดเห็น';    
                                        $maxLength = 20;
                                        
                                        if (strlen($comments) > $maxLength) {    
                                            $shortComments = substr($comments, 0, $maxLength) . '...';
                                            echo $shortComments;
                                            echo ' <a href="#" onclick="showFullComments(\'' . addslashes($comments) . '\')">ดูทั้งหมด</a>';
                                        } else {
                                            echo $comments;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="t1" style="max-height: 50px; overflow: auto">
                                    <?php
                                    if ($project['status_pro'] === 'อนุมัติ') {
                                        $link = "generate_pdf.php?id_project=" . htmlspecialchars($project['id_project']);
                                        echo '<a href="' . $link . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                                                    <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                                                    <path d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                                                </svg>
                                            </a>';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                        echo 'โครงการไม่ถูกอนุมัติ';
                                    } elseif ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    }
                                    ?>
                                </td>

                                <td class="t1" style="max-height: 50px; overflow: auto">
                                    <?php
                                    if ($project['status_pro'] === 'อนุมัติ') {
                                        echo '<a href="upload_summarize.php?id_project=' . htmlspecialchars($project['id_project']) . '" style="border: none; background: none; cursor: pointer;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                                                <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708z"/>
                                            </svg>
                                        </a>';
                                    } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                        echo 'โครงการไม่ถูกอนุมัติ';
                                    } elseif ($project['status_pro'] === 'รออนุมัติ') {
                                        echo 'รออนุมัติ';
                                    }
                                    ?>
                                </td>
                                <td style="max-height: 50px; overflow: auto">
                                    <?php
                                        if ($project['status_pro'] === 'อนุมัติ') {
                                            if (!empty($project['summarize_name']) && !empty($project['summarize_path'])) {
                                                $fileExtension = pathinfo($project['summarize_name'], PATHINFO_EXTENSION);
                                                if ($fileExtension === 'pdf') {                            
                                                    echo "<a href='https://yanreehospital.com/summarize/" . htmlspecialchars($project['summarize_name']) . "' target='_blank'>" . htmlspecialchars($project['summarize_name']) . "</a>";
                                                } else {              
                                                    echo "<a href='https://yanreehospital.com/summarize/" . htmlspecialchars($project['summarize_name']) . "' download>" . htmlspecialchars($project['summarize_name']) . "</a>";
                                                }
                                            } else {                               
                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-x-octagon" viewBox="0 0 16 16" color="red">
                                                        <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"/>
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                    </svg>';
                                            }
                                        } elseif ($project['status_pro'] === 'ไม่อนุมัติ') {
                                            echo 'โครงการไม่ถูกอนุมัติ';
                                        } elseif ($project['status_pro'] === 'รออนุมัติ') {
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
    <div id="commentsPopup" style="display:none; position:fixed; left:50%; top:50%; transform:translate(-50%, -50%); background:white; padding:20px; border:1px solid #ccc;">
        <div id="popupContent"></div>
        <button onclick="closePopup()" style="background-color:red; color:white; padding:10px; border:none; cursor:pointer;">ปิด</button>
    </div>

    <script>
        function showFullComments(comments) {
            document.getElementById('popupContent').innerHTML = comments;
            document.getElementById('commentsPopup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('commentsPopup').style.display = 'none';
        }

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
                "order": [] 
            });
        }); 

    </script>
</body>
</html>
