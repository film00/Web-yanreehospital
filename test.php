<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบอัปโหลดไฟล์</title>
</head>
<body>
    <h2>ทดสอบการอัปโหลดไฟล์</h2>
    
    <!-- แบบฟอร์มสำหรับอัปโหลดไฟล์ -->
    <form action="" method="post" enctype="multipart/form-data">
        เลือกไฟล์เพื่ออัปโหลด:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="อัปโหลดไฟล์" name="submit">
    </form>

    <?php
    // ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
    if (isset($_POST["submit"])) {
        $target_dir = "file/"; // โฟลเดอร์ที่ต้องการบันทึกไฟล์
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบว่าเป็นไฟล์ภาพหรือไม่
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "ไฟล์เป็นภาพ - " . $check["mime"] . ".<br>";
            $uploadOk = 1;
        } else {
            echo "ไฟล์นี้ไม่ใช่ภาพ.<br>";
            $uploadOk = 0;
        }

        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            echo "ไฟล์มีขนาดใหญ่เกินไป.<br>";
            $uploadOk = 0;
        }

        // อนุญาตเฉพาะไฟล์รูปภาพบางประเภท (JPG, PNG, JPEG, GIF)
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.<br>";
            $uploadOk = 0;
        }

        // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
        if ($uploadOk == 0) {
            echo "ไม่สามารถอัปโหลดไฟล์ได้.<br>";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "ไฟล์ " . basename($_FILES["fileToUpload"]["name"]) . " ถูกอัปโหลดเรียบร้อยแล้ว.<br>";
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.<br>";
            }
        }
    }
    ?>
</body>
</html>
