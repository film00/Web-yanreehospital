<?php
ob_start(); // เริ่มต้น output buffering
session_start(); // ใช้งาน session

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";
 
require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php"; 
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
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover; 
        }

        content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            max-width: 600px;
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

        .edit-btn {
            margin-top: 10px;
            background-color: #84dcc6;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .file-input {
            display: none;
        }

        #preview {
            
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .centered-image {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }
        body {
            margin: 0;
            padding-bottom: 4em; /* Adjust this value to match the height of your footer */
        }

        .footer {
            position: fixed;
            background-color: #191970;
            color: white;
            text-align: center;
            width: 100%;
            padding: 1em;
            bottom: 0;
            z-index: 1; /* Ensure the footer stays above other content */
        }
    </style>

</head>
<body>

    <div style="text-align:center;">
        <h1 class="h1">เพิ่ม</h1><h2 class="h2">ข้อมูลรถ</h2>
    </div>

    <script src="script.js"></script>
    <content>
        <form action="process_addcar.php" method="post" enctype="multipart/form-data">
            <div class="centered-image">
                <img id="preview" src="./image/car.jpg" alt="Preview">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="file-input" onchange="previewImage(event)">
                <button type="button" class="edit-btn" onclick="document.getElementById('profile_picture').click()">เลือกรูป</button>
            </div>

            <label for="logo">ลักษณะรถ(รุ่น,สี):</label>
            <input type="text" id="logo" name="logo" required>

            <label for="number">ทะเบียนรถ:</label>
            <input type="text" id="number" name="number" required pattern="[ก-ฮ]{1,2}[0-9]{1,4}" placeholder="เช่น กก1234 หรือ ก1234" oninput="clearMessages()" onblur="checkDuplicate()" title="กรุณากรอกเลขทะเบียนรถ เช่น กก1234 หรือ ก1234">
            <span id="duplicateWarning" style="color: red; display: none;">ทะเบียนรถนี้มีอยู่แล้ว</span>
            <span id="successMessage" style="color: green; display: none;">ทะเบียนรถนี้สามารถกรอกได้</span>
           

            <button type="submit" id="submitButton" disabled>ยืนยัน</button>
            <button type="button" onclick="cancel()">ยกเลิก</button>
        </form>
    </content>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.width = '150px';
                output.style.height = '150px';
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function cancel() {
            window.location.href = "datacar.php";
        }

        function clearMessages() {
            document.getElementById('duplicateWarning').style.display = 'none';
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('submitButton').disabled = true;
        }

        function checkDuplicate() {
            const carNumber = document.getElementById('number').value;
            const carPattern = /^[ก-ฮ]{1,2}[0-9]{1,4}$/; // รูปแบบการตรวจสอบ

            if (carNumber !== '') {
                if (!carPattern.test(carNumber)) {
                    document.getElementById('duplicateWarning').style.display = 'none';
                    document.getElementById('successMessage').style.display = 'none';
                    document.getElementById('submitButton').disabled = true;
                    alert('กรุณากรอกเลขทะเบียนรถให้ถูกต้องตามรูปแบบ');
                } else {
                    fetch('check_duplicate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ number: carNumber }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('duplicateWarning').style.display = 'inline';
                            document.getElementById('successMessage').style.display = 'none';
                            document.getElementById('submitButton').disabled = true;
                        } else {
                            document.getElementById('duplicateWarning').style.display = 'none';
                            document.getElementById('successMessage').style.display = 'inline';
                            document.getElementById('submitButton').disabled = false;
                        }
                    });
                }
            } else {
                document.getElementById('submitButton').disabled = true;
            }
        }

    </script>

</body>
</html>
