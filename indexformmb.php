<?php
    session_start();
    require_once "./nav/navbar.php";
    require_once "./footer/footer.php";
    
    $servername = "localhost";
    $username = "yanreeho_yanree_db";
    $password = "B@4N+209rhMfoT";
    $dbname = "yanreeho_yanree_db";
    

    if(isset($_SESSION['id_us'])){
        $id_us = $_SESSION['id_us'];
    } else {
        header("Location: login.php"); 
        exit();
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<style>
            body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('./image/background.JPG');
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh; /* เปลี่ยนจาก height: auto เป็น min-height: 100vh */
            display: flex;
            flex-direction: column;
        }
            .form-button {
                border: 1px solid #ccc;
                padding: 10px;
                width: 100%;
                text-align: left;
                display: block;
                margin-top: 10px;
                position: relative; /* Added for positioning */
            }
            .form-button svg {
                vertical-align: middle;
                margin-right: 10px; /* Adjust as needed */
            }
            .form-button .checkmark {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                display: none; /* Initially hide checkmark */
            }
            .form-button.active .checkmark {
                display: inline-block; /* Show checkmark when active */
            }
            .hidden {
                display: none;
            }
            .iframe-form {
                border: 1px solid #ddd;
                margin-top: 10px;
                width: 100%;
                height: 800px;
            }
    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Prompt", sans-serif;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
            background-image: url('./image/background.JPG'); /* กำหนดพื้นหลังเป็นรูปภาพ */
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
            margin-bottom: 3rem;
            }
            .form-button {
                border: 1px solid #ccc;
                padding: 10px;
                width: 100%;
                text-align: left;
                display: block;
                margin-top: 10px;
                position: relative; /* Added for positioning */
            }
            .form-button svg {
                vertical-align: middle;
                margin-right: 10px; /* Adjust as needed */
            }
            .form-button .checkmark {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                display: none; /* Initially hide checkmark */
            }
            .form-button.active .checkmark {
                display: inline-block; /* Show checkmark when active */
            }
            .hidden {
                display: none;
            }
            .iframe-form {
                border: 1px solid #ddd;
                margin-top: 10px;
                width: 100%;
                height: 800px;
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
        

    </style>
</head>
<body>
        <div style="text-align:center;">
            <h1 class="h1">แบบ</h1><h2 class="h2">ฟอร์ม</h2>
        </div>
    <button class="form-button active" id="projectFormLink">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-file-text-fill" viewBox="0 0 16 16">
            <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M5 4h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1m-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1m0 2h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1"/>
        </svg>
        <span>แบบฟอร์มโครงการ</span>
        <span class="checkmark">&#10003;</span> <!-- Checkmark -->
    </button> 
    <iframe id="projectForm" class="iframe-form hidden" src="./create_project.php"></iframe>

    <button class="form-button" id="governmentFormLink">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
            <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
        </svg>
        <span>แบบฟอร์มไปราชการ</span>
        <span class="checkmark">&#10003;</span> <!-- Checkmark -->
    </button>
    <iframe id="governmentForm" class="iframe-form hidden" src="./create_projectcar.php"></iframe>

    <script>
        $(document).ready(function() {
            $('#projectFormLink').click(function(e) {
                e.preventDefault();
                $('.form-button').removeClass('active'); // Remove active class from all buttons
                $(this).addClass('active'); // Add active class to clicked button
                $('#governmentForm').addClass('hidden'); // Hide government form iframe
                $('#projectForm').toggleClass('hidden'); // Toggle project form iframe
            });

            $('#governmentFormLink').click(function(e) {
                e.preventDefault();
                $('.form-button').removeClass('active'); // Remove active class from all buttons
                $(this).addClass('active'); // Add active class to clicked button
                $('#projectForm').addClass('hidden'); // Hide project form iframe
                $('#governmentForm').toggleClass('hidden'); // Toggle government form iframe
            });
        });
    </script>
</body>
</html>
