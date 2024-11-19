<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// เริ่ม buffer output
ob_start();

require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";

// ข้อมูลฐานข้อมูล
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";

if (isset($_GET['id'])) {
    $id_us = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ดึงข้อมูลผู้ใช้
        $stmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $stmt->bindParam(':id_us', $id_us, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: datausers.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // รับค่าจากฟอร์มและใช้ htmlspecialchars เพื่อป้องกัน XSS
            $name = htmlspecialchars($_POST['name']);
            $last_name = htmlspecialchars($_POST['last_name']);
            $phone_number = htmlspecialchars($_POST['phone_number']);
            $email = htmlspecialchars($_POST['email']);
            $rank = htmlspecialchars($_POST['rank']);
            $status = htmlspecialchars($_POST['status']);
            
            // ตั้งค่าชื่อไฟล์รูปภาพ
            $newFileName = null;

            // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                // เส้นทางไปยังโฟลเดอร์ที่เก็บรูปภาพ
                $uploadDir = './imageprofile/';
                
                // ตรวจสอบและลบรูปภาพเดิมถ้ามี
                if (!empty($user['profile_picture'])) {
                    $oldFilePath = $uploadDir . basename($user['profile_picture']);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // ลบรูปภาพเดิมออก
                    }
                }

                // ตั้งชื่อไฟล์ใหม่ตามเวลาเพื่อป้องกันชื่อซ้ำกัน
                $newFileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
                $targetFilePath = $uploadDir . $newFileName;

                // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ที่กำหนด
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                    // อัปเดตชื่อไฟล์รูปภาพในฐานข้อมูล
                    $stmt = $conn->prepare("UPDATE users SET picture_name = :picture_name, profile_picture = :profile_picture WHERE id_us = :id_us");
                    $stmt->bindParam(':picture_name', $newFileName, PDO::PARAM_STR);
                    $stmt->bindParam(':profile_picture', $newFileName, PDO::PARAM_STR);
                    $stmt->bindParam(':id_us', $id_us, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            // อัพเดตข้อมูลผู้ใช้
            $updateStmt = $conn->prepare("UPDATE users SET name = :name, last_name = :last_name, phone_number = :phone_number, email = :email, rank = :rank, status = :status WHERE id_us = :id_us");
            $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
            $updateStmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $updateStmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
            $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $updateStmt->bindParam(':rank', $rank, PDO::PARAM_STR);
            $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_us', $id_us, PDO::PARAM_INT);
            $updateStmt->execute();

            // เปลี่ยนเส้นทางไปยังหน้า datausers.php
            header("Location: datausers.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    header("Location: datausers.php");
    exit();
}

$conn = null;

// ปล่อย buffer output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Edit User</title>

    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
            background-image: url('../background.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            margin-bottom: 100px;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: auto; /* Center the container */
        }

        form {
            width: 100%;
            padding: 20px;
            justify-content: center;
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
            margin-bottom: 20px;
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

        .btn-custom {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            border: none;
            margin-right: 10px;
        }

        .btn-save:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-back:hover {
            background-color: #0069d9;
        }

        .h1, .h2 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 50px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        .form-group.centered {
            text-align: center;
        }

        .form-group.centered input {
            margin-left: auto;
            margin-right: auto;
        }
       /* ซ่อน input ไฟล์ */
        #profile_picture {
            display: none;
        }

        /* สไตล์สำหรับปุ่มเลือกไฟล์ */
        .file-input-button {
            background-color: #d9534f;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }

        /* สไตล์สำหรับปุ่มประเภทอื่นๆ */
        button[type="button"] {
            background-color: #d9534f;
        }

        
        button {
            background-color: #84dcc6;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button[type="button"] {
            background-color: #d9534f;
        }
                
    </style>

</head>
<body>
    <div style="text-align:center;">
        <h1 class="h1">แก้ไข</h1><h1 class="h2">ข้อมูลผู้ใช้2</h1>
    </div>
    <div class="container mt-5">
        <form action="edit_user.php?id=<?php echo $id_us; ?>" method="post" enctype="multipart/form-data">
            <div class="profile-picture">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img id="profileImg" src="<?php echo './imageprofile/' . $user['profile_picture']; ?>" alt="Profile Picture">
                <?php else: ?>
                    <img id="profileImg" src="./image/user.png" alt="Default Profile Picture">
                <?php endif; ?>
            </div>
            <div class="form-group centered">
                <label for="profile_picture">อัปโหลดรูปภาพโปรไฟล์</label>
                <input type="file" id="profile_picture" name="profile_picture" onchange="previewImage(event)">
                <label for="profile_picture" class="file-input-button">เลือกไฟล์</label>
            </div>
            <div class="form-group">
                <label for="name">ชื่อ:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">นามสกุล:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rank">ตำแหน่ง:</label>
                <input type="text" id="rank" name="rank" value="<?php echo htmlspecialchars($user['rank']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">เบอร์โทรศัพท์:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <label for="status">สถานะผู้ใช้:</label>
            <select id="status" name="status" <?php echo $user['status'] == 'ผู้ดูแลระบบ' ? 'disabled' : ''; ?>>
                <option value="ผู้ดูแลระบบ" <?php echo $user['status'] == 'ผู้ดูแลระบบ' ? 'selected' : ''; ?>>ผู้ดูแลระบบ</option>
                <option value="หัวหน้า" <?php echo $user['status'] == 'หัวหน้า' ? 'selected' : ''; ?>>หัวหน้า</option>
                <option value="เจ้าหน้าที่ทั่วไป" <?php echo $user['status'] == 'เจ้าหน้าที่ทั่วไป' ? 'selected' : ''; ?>>เจ้าหน้าที่ทั่วไป</option>
            </select>
            
            <div class="form-group centered">
                <button type="submit" class="btn-custom btn-save">บันทึก</button>
                <a href="datausers.php" class="btn-custom btn-back">ย้อนกลับ</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profileImg');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
