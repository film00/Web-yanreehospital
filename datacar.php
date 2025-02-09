<?php
session_start();
ob_start(); // เริ่มการ buffer output
require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";
 
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


// ตรวจสอบว่ามีค่า id_us ใน session หรือไม่
if (isset($_SESSION['id_us'])) { 
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ตรวจสอบว่ามีคำขอลบหรือไม่
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car_id'])) {
            $deleteCarId = $_POST['delete_car_id'];

            // ดึงข้อมูลรถที่จะลบ
            $stmt = $conn->prepare("SELECT picture_name FROM car WHERE id_car = :id_car");
            $stmt->bindParam(':id_car', $deleteCarId, PDO::PARAM_INT);
            $stmt->execute();
            $car = $stmt->fetch(PDO::FETCH_ASSOC);

            // ลบรูปภาพถ้ามี
            if (!empty($car['picture_name'])) {
                $filePath = './imagecar/' . $car['picture_name'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // ลบรถจากฐานข้อมูล
            $deleteStmt = $conn->prepare("DELETE FROM car WHERE id_car = :id_car");
            $deleteStmt->bindParam(':id_car', $deleteCarId, PDO::PARAM_INT);
            $deleteStmt->execute();
        }

        // ดึงข้อมูลรถยนต์ทั้งหมด
        $carStmt = $conn->prepare("SELECT * FROM car");
        $carStmt->execute();
        $cars = $carStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();

}

$conn = null;
ob_end_flush(); // ปล่อย buffer output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: "Prompt", sans-serif;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
            background-image: url('../background.jpg'); 
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
            margin-bottom: 3rem;
        }

        h2 {
            font-family: "Prompt", sans-serif;
        }

        .prompt-bold {
            font-family: "Prompt", sans-serif;
            font-weight: 700;
            font-style: normal;
        } 

        content {
            padding: 20px;
            font-family: "Prompt", sans-serif;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: right;
        }

        .us {
            font-size: 30px;
            margin-top: 20px;
            text-align: center;
        }

        .grid {
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        table {
            width: 100%;
            border-collapse: collapse;
            
        }

        td{
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            white-space: nowrap;
           
        }
        th {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            background-color: #191970;
            color: white;
            white-space: nowrap;
           
        }

        th:last-child,
        td:last-child {
            border-right: none;
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
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 35%;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            text-align: center;
        }

        p.modal-text {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p.small-text {
            font-size: 16px;
            margin-top: 10px;
        }
        
        p.smalless-text {
            font-size: 12px;
            margin-top: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        #projectDetails {
            white-space: pre-wrap;
        }

        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 10px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tr:hover {
            background-color: #e0e0e0; 
        }

        .add-member-button {
            margin-left: auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-align: center;
            display: inline-block;
            margin-left: 0px;
        }

        .add-member-button:hover {
            background-color: #45a049;
        }

        .search-container {
            float: left;
            margin-right: auto;
        }

        input[type="text"] {
            padding: 5px;
            border-radius: 5px;
        }

        .action-link {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            
        }
       

        .action-text {
            position: absolute;
            bottom: -20px;
            transform: translateX(-50%);
            background-color: white;
            color: black;
            font-size: 12px;
            padding: 2px 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, bottom 0.3s;
            border-radius: 10px;
        }

        .action-link:hover .action-text {
            opacity: 1;
            bottom: 35px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .confirm-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-button:hover {
            background-color: #45a049;
        }

        .cancel-button {
            background-color: #f44336; /* สีแดง */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-button:hover {
            background-color: #d32f2f; /* สีแดงเข้มเมื่อเอาเมาส์ไปชี้ */
        }

        th {
            background-color: #191970;
            color: white;
           
        }


        th.width-9 {
            width: 10%; 
        }
                
        .container {
            display: flex; /* ใช้ Flexbox สำหรับจัดตำแหน่งลิงก์ */
            justify-content: space-between; /* จัดตำแหน่งให้มีพื้นที่ระหว่างลิงก์เท่ากัน */
            align-items: center; /* จัดตำแหน่งกลางแนวตั้ง */
            width: 100%; /* ให้คอนเทนเนอร์มีความกว้างเต็มที่ */
        }

        table {
            width: 100%; /* ให้ตารางใช้ความกว้างทั้งหมดของคอนเทนเนอร์ */
            border-collapse: collapse; /* รวมขอบของเซลล์ให้ไม่ทับซ้อน */
        }
        .action-container {
            display: flex;
            justify-content: center; /* จัดให้อยู่ตรงกลางในแนวนอน */
            align-items: center; /* จัดให้อยู่ตรงกลางในแนวตั้ง */
            gap: 20px; /* ระยะห่างระหว่างปุ่มแก้ไขกับลบ */
        }

        .action-link {
            display: flex;
            align-items: center; /* จัดไอคอนและข้อความให้อยู่ตรงกลางในแนวตั้ง */
            text-decoration: none; /* ลบขีดเส้นใต้ */
        }

        .action-link svg {
            margin-right: 5px; /* ระยะห่างระหว่างไอคอนกับข้อความ */
        }

                
    </style>
    
</head>
<body> 
    <script src="script.js"></script>
    <content>
        <div style="text-align:center;">
            <h1 class="h1">ข้อมูลและ</h1><h2 class="h2">การจัดการรถ</h2>
        </div>
        <div>
            <a class="add-member-button" href="addcar.php">เพิ่มข้อมูลรถ</a>
        </div>
        <div style="overflow-x:auto;">
        <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">รูป</th>
                    <th style="width: auto">ลักษณะรถ</th>
                    <th style="width: auto">ทะเบียนรถ</th>                   
                    <th style="width: auto">แก้ไข</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car) : ?>
                <tr>
                    <td>
                        <div class="profile-picture">
                            <?php if (!empty($car['picture_name'])): ?>
                                <img src="<?php echo './imagecar/' . $car['picture_name']; ?>" alt="Profile Picture">
                            <?php else: ?>
                                <img src="./image/car.jpg" alt="Default Profile Picture">
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo $car['logo']; ?></td>
                    <td><?php echo $car['number']; ?></td>
                    
                    <td>
                    <div class="action-container">
                        <a href="javascript:void(0);" onclick="handleButtonClick(<?php echo $car['id_car']; ?>, 'edit')" class="action-link">
                            <i class="bi bi-tools"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16" style="color: black;">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21 4.5 11.5l2.29-.439 6.823-6.823z"></path>
                                <path fill-rule="evenodd" d="M1 13.5V16h2.5L14.379 5.621l-2-2L1 13.5zm1-.5H1v1h1v-1z"></path>
                            </svg>
                            <span class="action-text">แก้ไข</span>
                        </a>

                        <a href="javascript:void(0);" onclick="handleButtonClick(<?php echo $car['id_car']; ?>, 'delete')" class="action-link">
                            <i class="bi bi-trash3-fill"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#000" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3v1h-1v10.5c0 .5-.5 1-1 1h-8c-.5 0-1-.5-1-1v-10.5h-1v-1h3v-1h4zM4 3v10h8v-10h-8zm4-2v1h-2v-1h2zm4 1v1h-3v-1h3zm-8 0v1h-3v-1h3zm8 2h-8v8h8v-8z"></path>
                            </svg>
                            <span class="action-text">ลบ</span>
                        </a>
                    </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </content>

    <!-- Delete confirmation modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>ยืนยันการลบ</h2>
            <p>คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?</p>
            <form id="deleteForm" method="POST">
                <input type="hidden" name="delete_car_id" id="deleteCarId">
                <button type="submit" class="confirm-button">ยืนยัน</button>
                <button type="button" class="cancel-button" onclick="closeModal()">ยกเลิก</button>
            </form>
        </div>
    </div>

    <script>
       function handleButtonClick(carId, action) {
    if (action === 'edit') {
        window.location.href = 'edit_car.php?id_car=' + carId;
    } else if (action === 'delete') {
        document.getElementById('deleteCarId').value = carId;
        document.getElementById('deleteModal').style.display = 'block';
    }
}


        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
</body>
</html>
