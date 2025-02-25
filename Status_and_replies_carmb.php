<?php
session_start();
require_once "./nav/navbar.php";
require_once "./footer/footer.php";
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


$notification = '';

if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];
 
    try {  
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM use_car WHERE id_us_car = :id_us_car";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us_car', $id_us, PDO::PARAM_INT);
        $stmt->execute();
        $use_car_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-attachment: fixed; 
            width: 100%;
            min-height: 100vh; 
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
          .content-wrapper {
            max-width: 100%; 
            overflow-x: auto; 
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
                            <th style="width: 15%">เรื่อง/งานที่ไปราชการ</th>
                            <th style="width: 15%">โดยยานพาหนะ</th>
                            <th style="width: 15%">ทะเบียน</th>
                            <th style="width: 15%">สถานะการขออนุญาติ</th>
                            <th style="width: 15%">ขอเบิกค่าใช้จ่ายจาก</th>
                            <th style="width: 10%">บันทึกไฟล์ PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($use_car_requests as $request) : ?> 
                            <tr>
                                <td><?php echo htmlspecialchars($request['purposes']); ?></td>
                                <td><?php echo htmlspecialchars($request['carrier']); ?></td>
                                <td><?php echo htmlspecialchars($request['id_car']); ?></td>
                                <td class="<?php
                                    if ($request['status_car'] === 'รออนุมัติ') {
                                        echo 'status-pending';
                                    } elseif ($request['status_car'] === 'อนุมัติ') {
                                        echo 'status-approved';
                                    } elseif ($request['status_car'] === 'ไม่อนุมัติ') {
                                        echo 'status-rejected';
                                    }
                                ?>"><?php echo htmlspecialchars($request['status_car']); ?></td>
                                <td><?php echo htmlspecialchars($request['about_costs']); ?></td>
                                <td class="t1"><a href="generate_car_pdf.php?idusecar=<?php echo htmlspecialchars($request['idusecar']); ?>">ดาวน์โหลด PDF</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </content>
    </div>
    <?php if ($notification) : ?>
        <script>
            alert('<?php echo htmlspecialchars($notification); ?>');
        </script>
    <?php endif; ?>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();

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
            setInterval(checkForUpdates, 30000);
        });
    </script>
</body>
</html>
