<?php
session_start();
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการอัพโหลดไฟล์และมีการล็อกอิน
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us']; // รับ id_us จาก session
    $fileName = $_FILES["fileToUpload"]["name"];
    $fileTmpName = $_FILES["fileToUpload"]["tmp_name"];
    $file = file_get_contents($fileTmpName);

    // Prepare an insert statement to insert file with user ID
    $sql = "INSERT INTO file (file_name, file, id_us) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $null = NULL; // This is needed to bind the blob data
        $stmt->bind_param("sbi", $fileName, $null, $id_us);
        $stmt->send_long_data(1, $file);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $file_id = $conn->insert_id; // Get the ID of the uploaded file
            $_SESSION['file_id'] = $file_id; // Store file ID in session
            echo "File uploaded successfully. File ID is: " . $file_id;
        } else {
            echo "Failed to upload file.";
        }
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} 

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <style>
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .btn-upload {
            border: 2px solid #0066cc;
            color: white;
            background-color: #0096ff;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
        }
        .file-upload-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .btn-submit {
            display: none; /* เริ่มต้นซ่อนปุ่ม */
            border: none;
            color: white;
            background-color: #4CAF50;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            margin-left: 10px; /* ระยะห่างจากชื่อไฟล์ */
        }
        .file-info {
            display: inline-block; /* ให้อยู่ในบรรทัดเดียวกัน */
            margin-top: 8px; /* ปรับตามต้องการ */
        }
    </style>
</head>
<body>
    <h2>เอกสารเพิ่มเติม</h2>
    <form id="uploadForm" action="upload_file.php" method="post" enctype="multipart/form-data">
        <div class="file-upload-wrapper">
            <button type="button" class="btn-upload" onclick="document.getElementById('fileToUpload').click()">เลือกไฟล์</button>
            <input type="file" name="fileToUpload" id="fileToUpload" onchange="fileSelected();" hidden>
            <span class="file-info" id="fileName"></span>
        </div>
    </form>

    <script>
        function fileSelected() {
            var fileInput = document.getElementById('fileToUpload');
            var fileNameDisplay = document.getElementById('fileName');
            var submitBtn = document.getElementById('submitBtn');

            if(fileInput.files.length > 0) {
                var fileName = fileInput.files[0].name;
                fileNameDisplay.innerText = "ไฟล์ที่เลือก: " + fileName; // แสดงชื่อไฟล์
                submitBtn.style.display = 'inline-block'; // ปุ่มแสดงในบรรทัดเดียวกัน
            } else {
                fileNameDisplay.innerText = ''; // ไม่แสดงชื่อไฟล์ถ้าไม่มีการเลือกไฟล์
                submitBtn.style.display = 'none'; // ซ่อนปุ่มถ้าไม่มีไฟล์ที่เลือก
            }
        }
    </script>
</body>
</html>

