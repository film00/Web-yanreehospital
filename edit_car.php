<?php
session_start();
ob_start();
require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


try {
    // เชื่อมต่อฐานข้อมูล
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_car = $_POST['id_car'];
        $logo = $_POST['logo'];
        $number = $_POST['number'];
        $status_car = $_POST['status_car'];

        // ตรวจสอบการอัพโหลดรูปภาพใหม่
        if (!empty($_FILES['profile_picture']['name'])) {
            // ลบรูปภาพเก่า (ถ้ามี)
            $stmt_select_picture = $conn->prepare("SELECT picture_name FROM car WHERE id_car = :id_car");
            $stmt_select_picture->bindParam(':id_car', $id_car);
            $stmt_select_picture->execute();
            $old_picture = $stmt_select_picture->fetch(PDO::FETCH_ASSOC);

            if (!empty($old_picture['picture_name'])) {
                unlink('./imagecar/' . $old_picture['picture_name']);
            }

            // อัพโหลดรูปภาพใหม่
            $target_dir = "./imagecar/";
            $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
            move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
            $picture_name = basename($_FILES["profile_picture"]["name"]);

            // อัพเดทข้อมูลรถพร้อมรูปภาพใหม่
            $stmt = $conn->prepare("UPDATE car SET logo = :logo, number = :number, status_car = :status_car, picture_name = :picture_name WHERE id_car = :id_car");
            $stmt->bindParam(':logo', $logo);
            $stmt->bindParam(':number', $number);
            $stmt->bindParam(':status_car', $status_car);
            $stmt->bindParam(':picture_name', $picture_name);
            $stmt->bindParam(':id_car', $id_car);
            $stmt->execute();
        } else {
            // อัพเดทข้อมูลรถโดยไม่เปลี่ยนรูปภาพ
            $stmt = $conn->prepare("UPDATE car SET logo = :logo, number = :number, status_car = :status_car WHERE id_car = :id_car");
            $stmt->bindParam(':logo', $logo);
            $stmt->bindParam(':number', $number);
            $stmt->bindParam(':status_car', $status_car);
            $stmt->bindParam(':id_car', $id_car);
            $stmt->execute();
        }

        // เปลี่ยนเส้นทางหลังจากการอัพเดทข้อมูล
        header("Location: datacar.php");
        exit();
    } else {
        // ดึงข้อมูลรถที่ต้องการแก้ไข
        $id_car = $_GET['id_car'];
        $stmt = $conn->prepare("SELECT * FROM car WHERE id_car = :id_car");
        $stmt->bindParam(':id_car', $id_car);
        $stmt->execute();

        $car = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบการเข้าถึงข้อมูลผู้ใช้
        if (!isset($_SESSION['id_us'])) {
            header("Location: index.php");
            exit();
        }

        // ดึงข้อมูลผู้ใช้จาก session id_us
        $id_us = $_SESSION['id_us'];
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $stmt_user->bindParam(':id_us', $id_us, PDO::PARAM_INT);
        $stmt_user->execute();
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
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
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center; /* จัดตำแหน่งพื้นหลังตรงกลาง */
            min-height: 100vh; /* ความสูงขั้นต่ำของ body ให้เท่ากับหน้าจอ */
            background-attachment: fixed; /* ให้พื้นหลังติดอยู่ */
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

        .profile-picture {
            width: 150px;
            height: 150px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            margin-bottom: 15px;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
      


        .form-group {
            display: flex;
            justify-content: center; /* จัดกึ่งกลางแนวนอน */
            align-items: center; /* จัดกึ่งกลางแนวตั้ง */
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
            align-items: center; 
        }

        /* สไตล์สำหรับปุ่มประเภทอื่นๆ */
        button[type="button"] {
            background-color: #d9534f;
        }

    </style>

</head>
<body>

    <div style="text-align:center;">
        <h1 class="h1">แก้ไข</h1><h2 class="h2">ข้อมูลรถ</h2>
    </div>

    <content>
        <form action="edit_car.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_car" value="<?php echo $car['id_car']; ?>">

            <div class="profile-picture">
                <?php if (!empty($car['picture_name'])): ?>
                    <img id="profileImg" src="<?php echo './imagecar/' . $car['picture_name']; ?>" alt="รูปภาพรถ">
                <?php else: ?>
                    <img id="profileImg" src="./image/car.jpg" alt="รูปภาพรถ">
                <?php endif; ?>
            </div>
             
            <div class="form-group centered">
                <input type="file" id="profile_picture" name="profile_picture" onchange="previewImage(event)" style="display: none;">
                <label for="profile_picture" class="file-input-button">เลือกไฟล์</label>
            </div>

            <label for="logo">ลักษณะรถ:</label>
            <input type="text" id="logo" name="logo" value="<?php echo $car['logo']; ?>" required>

            <label for="number">ทะเบียนรถ:</label>
            <input type="text" id="number" name="number" value="<?php echo $car['number']; ?>" required>

 
            <button type="submit">บันทึก</button>
            <button type="button" onclick="goBack()">ย้อนกลับ</button>

        </form>
    </content>

    <script>
        function goBack() {
            window.history.back();
        }

        function previewImage(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("profileImg");
                preview.src = src;
                preview.style.display = "block";
            }
        }
    </script>
</body>
</html>
