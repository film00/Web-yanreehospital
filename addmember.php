<?php
ob_start(); // เริ่มต้น output buffering

session_start(); // ใช้งาน session

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";

require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";

// ปิด buffering และส่งข้อมูลไปยังเบราว์เซอร์
 
// ตรวจสอบว่ามีค่า id_us ใน session หรือไม่
if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM users WHERE id_us = :id_us";

    // ใช้การเชื่อมต่อฐานข้อมูลที่ตั้งค่าเป็น UTF-8
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ทำการ prepare และ execute คำสั่ง SQL
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_us', $id_us, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch ข้อมูลผู้ใช้
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // หากไม่พบข้อมูลผู้ใช้ ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
        header("Location: index.php");
        exit();
    }
} else {
    // หากไม่มี id_us ใน session ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
    header("Location: index.php");
    exit();
}

ob_end_flush();
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: ;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            padding-bottom: 4em;
        }
        content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-collapse: collapse;
        }

        form {
            max-width: 65%;
            width: 100%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .password-toggle {
            position: relative;
        }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }

        button {
            background-color: #84dcc6;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="button"] {
            background-color: #d9534f;
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

       

        .label-container-2 {
            display: flex;  
            gap: 20%;  
        }

        .form-container {
            display: flex;  
            align-items: center;  
            gap: 15;  
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-row label {
            width: 120px;
            text-align: right;
            margin-right: 10px;
        }

        .form-row .input-group {
            display: flex;
            align-items: center;
            flex: 1;
        }
        
        #username {
            width: 100%;  
            height: 5%;  
        }

        #password {
            width: 100%;
            height: 5%;
        }

        .password-toggle {
            display: flex;
            align-items: center;
        }

        .toggle-btn {
            cursor: pointer;
            margin-left: 8px;
        }

        .toggle-btn svg {
            vertical-align: middle;
        }

        .centered-image {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .centered-image img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .file-input {
            display: none;กก
        }

        .edit-btn {
            margin-top: 10px;
        }
        

        .responsive-form {
            max-width: 100%;
            margin: auto;
            padding: 20px;
        }

        .centered-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .label-container {
            margin-bottom: 15px;
        }

        .label-container label, .label-container input, .label-container select {
            display: block;
            width: 100%;
        }

        .password-toggle {
            display: flex;
            align-items: center;
        }

        .password-toggle input {
            flex-grow: 1;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }




    </style>

</head> 
<body>
     
    <div style="text-align:center;">
        <h1 class="h1">เพิ่ม</h1><h2 class="h2">สมาชิก</h2>
    </div>
 
    <div style="overflow-x:auto;">
    <content>
        <form action="process_addmember.php" method="post" enctype="multipart/form-data">
            <div class="centered-image">
                <img src="./image/user.png" alt="User Image" id="userImage" width="150" height="150">
                <input type="file" id="fileInput" class="file-input" name="userImage" accept="image/*" onchange="previewImage(event)">
                <button type="button" class="edit-btn" onclick="document.getElementById('fileInput').click()">เลือกรูป</button>
            </div>
            
            <div class="label-container">
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" required>
                <span id="username-availability"></span> 
            </div>

             <div class="label-container">
                <label for="password">รหัสผ่าน:</label>
                <div class="password-toggle">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-btn" onclick="togglePassword('password')">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                    </span>
                </div>
            </div>
          
            <div class="label-container">
                <label for="prefix">คำนำหน้าชื่อ:</label>
                <select id="prefix" name="prefix">
                    <option value="นาย">นาย</option>
                    <option value="นาง">นาง</option>
                    <option value="นางสาว">นางสาว</option>
                </select>
            </div>
            <div class="label-container">
                <label for="name">ชื่อ:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="label-container">
                <label for="last_name">นามสกุล:</label>
                <input type="text" id="last_name" name="last_name" required>
            

            <label for="rank">ตำแหน่ง:</label>
            <input type="text" id="rank" name="rank" required>

            <label for="phone_number">หมายเลขโทรศัพท์:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="email">อีเมล:</label>
            <input type="email" id="email" name="email" required>

            <label for="status">สถานะผู้ใช้:</label>
            <select id="status" name="status">
                <option value="ผู้ดูแลระบบ">ผู้ดูแลระบบ</option>
                <option value="หัวหน้า">หัวหน้า</option>
                <option value="เจ้าหน้าที่ทั่วไป">เจ้าหน้าที่ทั่วไป</option>
            </select>

            <button type="submit" id="submit-btn">ยืนยัน</button>

            <button type="button" onclick="cancel()">ยกเลิก</button>
        </form>
    </content>
    </div>

    <script>
        function togglePassword(id) { 
            const passwordInput = document.getElementById(id);
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5a12.229 12.229 0 0 1-2.098 2.745l-1.439-1.439a4.5 4.5 0 0 0-6.168-6.168m1.394 1.394a2.5 2.5 0 0 1 3.288 3.288l-3.288-3.288m-1.707 1.708 3.288 3.288a2.5 2.5 0 0 1-3.288-3.288Z"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>';
            }
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('userImage');
                output.src = reader.result;
                output.style.width = '150px';
                output.style.height = '150px';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function cancel() {
            window.location.href = "datausers.php"; // Redirect to another page on cancel
        }


        document.getElementById('username').addEventListener('input', function() {
            var username = this.value;
            var availabilityElement = document.getElementById('username-availability');
            var submitButton = document.getElementById('submit-btn');

            if (username === '') {
                availabilityElement.textContent = ''; // ถ้ายังไม่ได้พิมพ์อะไร ให้ข้อความว่างเปล่า
                submitButton.disabled = false; // ปล่อยให้กดส่งได้
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_username.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log(xhr.status); // ดูสถานะการตอบกลับ
                    console.log(xhr.responseText); // ดูข้อความตอบกลับ
                    if (xhr.status === 200) {
                        if (xhr.responseText.includes('ชื่อผู้ใช้นี้มีอยู่แล้ว')) {
                            availabilityElement.textContent = xhr.responseText;
                            availabilityElement.style.color = 'red';  // เปลี่ยนเป็นสีแดง
                            submitButton.disabled = true; // ปิดการใช้งานปุ่มส่ง
                        } else {
                            availabilityElement.textContent = xhr.responseText;
                            availabilityElement.style.color = 'green';  // เปลี่ยนเป็นสีเขียว
                            submitButton.disabled = false; // ปล่อยให้กดส่งได้
                        }
                    } else {
                        console.error('Failed to check username, status: ' + xhr.status);
                    }
                }
            };
            xhr.send('username=' + encodeURIComponent(username));
        });



    </script>

    
</body>
</html>
