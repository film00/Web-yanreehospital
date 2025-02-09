<?php
ob_start(); // เริ่มต้น output buffering

session_start(); // ใช้งาน session

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

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
    <link rel="stylesheet" type="text/css" href="nav.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">

    <style>
       body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('../background.jpg'); /* กำหนดพื้นหลังเป็นรูปภาพ */
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
        }

        content {
            padding: 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            margin-bottom: 100px;
        }

        .grid {
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .us {
            padding: 0px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.7);
            font-size: 1.5625em;
            border-radius: 15px;
        }

        .us img {
            margin-bottom: 10px;
        }

        .us table {
            width: 100%;
            text-align: left;
            margin-top: 20px;
        }

        .us .text {
            text-align: right;
            padding-right: 20px;
        }

        .us .data {
            text-align: left;
            margin-left: 50px;
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
            margin-top: 2em;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }

          @media (max-width: 768px) {
        .user-info-table .text {
            font-size: 16px; 
        }
        .user-info-table .data {
            font-size: 16px; 
        }

       
        }  

    </style>

</head>
<body>


    <div style="text-align:center;">

        <h1 class="h1">ข้อมูล</h1><h2 class="h2">ส่วนตัว</h2>
    </div>
    <content>
        
        <div class="us">
                <td>
                    <div class="profile-picture">
                        <?php if (!empty($user['picture_name'])): ?>
                            <img src="<?php echo './imageprofile/' . $user['picture_name']; ?>" alt="Profile Picture">
                        <?php else: ?>
                            <img src="./image/user.png" alt="Default Profile Picture">
                        <?php endif; ?>
                    </div>
                </td>
            <table class="user-info-table">
                <tr>
                    <td class="text" style="text-align: right;">ชื่อ :</td>
                    <td class="data" style="text-align: left;"><?php echo htmlspecialchars($user['prefix'] . ' ' . $user['name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td class="text" style="text-align: right;">ตำแหน่ง :</td>
                    <td class="data" style="text-align: left;"><?php echo htmlspecialchars($user['rank'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td class="text" style="text-align: right;">เบอร์โทรศัพท์ :</td>
                    <td class="data" style="text-align: left;"><?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td class="text" style="text-align: right;">อีเมล :</td>
                    <td class="data" style="text-align: left;"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr> 
                <tr>
                    <td class="text" style="text-align: right;">สถานะผู้ใช้ :</td>
                    <td class="data" style="text-align: left;">
                        <?php
                            if ($user['status'] === 'admin') {
                                echo 'ผู้ดูแลระบบ';
                            } else {
                                echo htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8');
                            }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </content>
</body>
</html>
