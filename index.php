<?php
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// ตั้งค่าเขตเวลาเป็นประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// สร้างตัวแปรสำหรับเก็บวันที่ปัจจุบัน
$current_date = date('d/m/Y'); // รูปแบบ Y-m-d สำหรับเปรียบเทียบ


// ตรวจสอบว่า $current_date ตรงกับ from_date หรือ up_date และมี status_car เป็น 'อนุมัติ'
$query = "SELECT id_car, from_date, up_date FROM use_car WHERE (from_date = :current_date OR up_date > DATE_ADD(:current_date, INTERVAL 1 DAY)) AND status_car = 'อนุมัติ'";
$stmt = $pdo->prepare($query);
$stmt->execute(['current_date' => $current_date]);

if ($stmt->rowCount() > 0) {
    // ดึง id_car, from_date และ up_date ที่ตรงกับวันที่ปัจจุบันและมี status_car เป็น 'อนุมัติ'
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        $id_car = $row['id_car'];
        $from_date = $row['from_date'];
        $up_date = $row['up_date'];

        // แปลงวันที่ from_date และ up_date ให้เป็นรูปแบบ Y-m-d
        $from_date_formatted = DateTime::createFromFormat('d/m/Y', $from_date)->format('d/m/Y');
        $up_date_formatted = DateTime::createFromFormat('d/m/Y', $up_date)->format('d/m/Y');

        // อัปเดต status_car ในตาราง car โดยใช้ number
        if ($from_date_formatted == $current_date) {
            // ถ้า from_date ตรงกับวันที่ปัจจุบัน
            $updateQuery = "UPDATE car SET status_car = 'ไม่ว่าง' WHERE number = :id_car";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(['id_car' => $id_car]);
        } elseif ($up_date_formatted > date('d/m/Y', strtotime($current_date . ' +1 day'))) {
            // ถ้า up_date มากกว่า 1 วันจากวันที่ปัจจุบัน
            $updateQuery = "UPDATE car SET status_car = 'ว่าง' WHERE number = :id_car";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(['id_car' => $id_car]);
        }
    }
} else {

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sriracha', cursive;
            text-align: center;
            background-color: #f0f0f0; 
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 300px; 
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        input {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        button, input {
            outline: none;
        }

        .forgot-password {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .form-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #FF9999;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .popup button {
            background-color: white;
            color: #f44336;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        .popup button:hover {
            background-color: #ddd;
        }
    </style>
</head>
 
<body>
    <div class="container">
        <img src="hospital.jpg" alt="" width="100" height="100">
        <h1>โรงพยาบาลส่งเสริมสุขภาพส่วนตำบลย่านรี</h1> 
        <form action="connect.php" method="post">
            <label for="username" style="margin-left: -205px">ชื่อผู้ใช้:</label>
            <input type="text" id="username" name="username" required>

            <div class="form-group">
                <label for="password" style="margin-left: 25px">รหัสผ่าน:</label>
                <a href="forgot_password.php" class="forgot-password">ลืมรหัสผ่าน?</a> 
            </div>
            
            <input type="password" id="password" name="password" required><br>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>

    <!-- ป็อปอัพแจ้งเตือน -->
    <div id="popup" class="popup">
        <p id="popup-message">ข้อความแจ้งเตือน</p>
        <button onclick="closePopup()">ตกลง</button>
    </div>

    <script>
        // แสดงป็อปอัพถ้ามีข้อผิดพลาด
        function showPopup(message) {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popup-message');
            popupMessage.textContent = message;
            popup.style.display = 'block';
        }

        // ปิดป็อปอัพ
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }

        // ตรวจสอบว่ามีข้อผิดพลาดหรือไม่
        <?php if (isset($_GET['error'])): ?>
            showPopup('<?php echo htmlspecialchars($_GET['error']); ?>');
        <?php endif; ?>
    </script>
</body>
</html>
