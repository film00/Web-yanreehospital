<?php
session_start();
require_once "./nav/navbar_addmin.php"; // ท่านอาจจะต้องแก้ไข path ถ้ามันไม่ถูกต้อง
require_once "./footer/footer.php"; // เดี๋ยวผมจะเรียกใช้ footer ให้ในที่เดียว

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


if(isset($_SESSION['id_us'])){
    $id_us = $_SESSION['id_us'];
} else {
    // หากไม่มี id_us ใน session ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
    header("Location: login.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- เปลี่ยนเป็นเวอร์ชัน 3.6.0 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            
        }

        content {
            padding: 20px;
        }

        .hidden {
            display: none;
        }

        .head {
            display: flex;
            align-items: center;
            justify-content: center; /* เพิ่มบรรทัดนี้เพื่อกึ่งกลางในแนวนอน */
            font-weight: bold;
            font-size: 18px;
        }

        .head label {
            margin-right: 10px;
        }

         /* ปรับแต่งปุ่ม Submit All Forms */
        .submit-btn {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        /* ปรับแต่งลิงก์ดาวน์โหลดฟอร์ม */
        .download-form-link {
            font-size: 25px;
            color: #007bff;
            text-decoration: none;
            padding: 8px 12px;
            margin: 5px 0;
            display: inline-block;
            cursor: pointer;
        }

        /* ปรับแต่ง checkbox labels */
        .checkbox-label {
            font-size: 25px;
            margin-right: 15px;
            cursor: pointer;
        }

        /* ปรับแต่ง iframes */
        .iframe-form {
            border: 1px solid #ddd;
            margin-top: 10px;
        }


    </style>

</head>
<body>

<div id="formContainer" style="margin-left: 20mm;">
    <div style="margin: 20px;">
        <h2>ดาวน์โหลดฟอร์มโครงการ</h2>
        <a href="from_pro.docx" download class="download-form-link">ดาวน์โหลดฟอร์ม</a>

        <h2>ฟอร์มโครงการ</h2>
        <label class="checkbox-label"><input type="checkbox" id="loadFormCheckbox"> กรอกแบบฟอร์ม</label>
        <iframe id="projectForm" class="hidden iframe-form" src="./create_project.php" width="100%" height="800px"></iframe>
        <label class="checkbox-label"><input type="checkbox" id="uploadCheckbox"> อัพโหลดเอกสารและเอกสารเพิ่มเติม</label>
        <iframe id="uploadForm" class="hidden iframe-form" src="./upload_file.php" width="100%" height="150px"></iframe>
        
        <h2>ฟอร์มไปราชการ</h2>
        <label class="checkbox-label"><input type="checkbox" id="loadFormCheckboxCar"> กรอกแบบฟอร์มใช้รถ</label>
        <iframe id="carForm" class="hidden iframe-form" src="./create_projectcar.php" width="100%" height="800px"></iframe>

        <br>
        <button type="button" onclick="submitAllForms()" class="submit-btn">Submit All Forms</button>
    </div>
</div>

<script>
    document.getElementById('loadFormCheckbox').addEventListener('change', function() {
        document.getElementById('projectForm').classList.toggle('hidden', !this.checked);
    });

    document.getElementById('uploadCheckbox').addEventListener('change', function() {
        document.getElementById('uploadForm').classList.toggle('hidden', !this.checked);
    });

    document.getElementById('loadFormCheckboxCar').addEventListener('change', function() {
        document.getElementById('carForm').classList.toggle('hidden', !this.checked);
    });

    function submitAllForms() {
        var isProjectFormChecked = document.getElementById('loadFormCheckbox').checked;
        var isUploadFormChecked = document.getElementById('uploadCheckbox').checked;
        var isCarFormChecked = document.getElementById('loadFormCheckboxCar').checked;

        if (isProjectFormChecked) {
            submitForm(document.getElementById('projectForm').contentWindow.document.forms[0]);
        }
        if (isUploadFormChecked) {
            submitForm(document.getElementById('uploadForm').contentWindow.document.forms[0]);
        }
        if (isCarFormChecked) {
            submitForm(document.getElementById('carForm').contentWindow.document.forms[0]);
        }
    }

    function submitForm(form) {
        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.text();
        }).then(data => {
            console.log(data);
            alert("Form submitted successfully");
        }).catch(error => {
            console.error('Error:', error);
        });
    }
</script>

</body>
</html>
