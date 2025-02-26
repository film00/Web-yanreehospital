<?php
session_start();
require_once "./nav/navbar_boss.php";
require_once "./footer/footer.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

if (isset($_GET['id'])) {
    $id_usecar = $_GET['id'];
 
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch use_car data based on id_usecar
        $useCarStmt = $conn->prepare("SELECT * FROM use_car WHERE idusecar = :idusecar");
        $useCarStmt->bindParam(':idusecar', $id_usecar);
        $useCarStmt->execute();
        $useCar = $useCarStmt->fetch(PDO::FETCH_ASSOC);

        if ($useCar) {
            // Fetch user data based on id_us
            $userStmt = $conn->prepare("SELECT prefix, name, last_name FROM users WHERE id_us = :id_us");
            $userStmt->bindParam(':id_us', $useCar['id_us_car']);
            $userStmt->execute();
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            ?>
            <!DOCTYPE html>
            <html lang="th">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="styles.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

                <style>
                    body {
                        font-family: "Prompt", sans-serif;
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        background-image: url('../background.jpg'); 
                        background-size: cover;
                        background-repeat: no-repeat;
                        margin-bottom: 200px;
                    }

                    .content {
                        padding: 20px;
                        font-size: 20px;
                    }

                    .grid {
                        margin-bottom: 3rem;
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    }

                    .content {
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        margin: 20px auto;
                        max-width: 960px;
                    }

                    .modal {
                        display: none;
                        position: fixed;
                        z-index: 1;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0,0,0,0.4);
                    }

                    .modal-content {
                        background-color: #fefefe;
                        margin: 15% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        max-height: 70vh;
                        overflow-y: auto;
                    }

                    .form-buttons {
                        display: flex;
                        justify-content: center;
                        margin-top: 20px;
                        margin-bottom: 50px;
                    }

                    .form-buttons button {
                        margin: 0 10px;
                        padding: 10px 20px;
                        border: none;
                        cursor: pointer;
                        font-size: 16px;
                        border-radius: 4px;
                        transition: background-color 0.3s;
                    }

                    .form-buttons button[type="submit"] {
                        background-color: #1e90ff;
                        color: white;
                    }

                    .form-buttons button[type="submit"]:hover {
                        background-color: #191970;
                    }

                    .form-buttons button[type="button"] {
                        background-color: #FF3333;
                        color: white;
                    }

                    .form-buttons button[type="button"]:hover {
                        background-color: #FF0000;
                    }

                    .centered-content {
                        text-align: center;
                    }
                    .commenttext {
                        width: 100%; /* กำหนดความกว้าง */
                        height: 150px; /* กำหนดความสูง */
                    }

                </style>

            </head> 
            <body>
                <div class="content">
                    <div class="centered-content">
                        <p>ใบขออนุญาตเดินทางไปราชการ</p>
                        <p>หน่วยงาน โรงพยาบาลส่งเสริมสุขภาพตำบลย่านรี</p>
                    </div>
                    <p style="margin-left: 30px;">คำชี้แจงการเดินทางไปราชการ</p>
                    <p style="margin-left: 100px;">ข้าพเจ้า...<?php echo htmlspecialchars($user['prefix']); ?>...<?php echo htmlspecialchars($user['name']); ?>...<?php echo htmlspecialchars($user['last_name']); ?>... ตำแหน่ง ...<?php echo htmlspecialchars($useCar['rank']); ?>...</p>
                    <p style="margin-left: 30px;">ขอไปอนุมัติราชราชการ ...<?php echo htmlspecialchars($useCar['go_to']); ?>... ขอเบิกค่าใช้จ่ายจาก ...<?php echo htmlspecialchars($useCar['about_costs']); ?>...</p>
                    <p style="margin-left: 30px;">ไปทำโครงการวันที่: <?php echo htmlspecialchars($useCar['from_date']); ?> ถึงวันที่: <?php echo htmlspecialchars($useCar['up_date']); ?> รวมวันที่ไป: <?php echo htmlspecialchars($useCar['sum_date']); ?> วัน</p>
                    <p style="margin-left: 30px;">โดยยานพาหนะ: <?php echo htmlspecialchars($useCar['carrier']); ?> ทะเบียน: <?php echo htmlspecialchars($useCar['id_car']); ?> พนักงานขับรถ: <?php echo htmlspecialchars($useCar['driver']); ?></p>
                    <p style="margin-left: 30px;"><?php echo htmlspecialchars($useCar['gasoline_cost']); ?> <?php echo htmlspecialchars($useCar['rest']); ?></p>
                    <p style="margin-left: 30px;">เรื่อง/งานที่ไปราชการ: ...<?php echo htmlspecialchars($useCar['purposes']); ?>... สถานที่ไปราชการ: ...<?php echo htmlspecialchars($useCar['destinations']); ?>...</p>
                    <p style="margin-left: 30px;">ผู้ที่ไปด้วยกี่คน: ...<?php echo htmlspecialchars($useCar['sumfollower']); ?>...คน </p>
                    <p style="margin-left: 30px;">ผู้ที่ไปด้วย: <?php echo htmlspecialchars($useCar['follower1']); ?></p>
                </div>

                <div class="content">
                    <form id="statusForm" method="POST">
                     
                    <label for="comment">แสดงความคิดเห็น:</label><br>
                    <textarea id="commenttext" name="comment" class="commenttext" placeholder="ไม่มี"></textarea><br><br>
 
                        <input type="checkbox" id="approve" name="status_car" value="อนุมัติ" onclick="checkOnly(this)">
                        <label for="approve">อนุมัติ</label><br>
                        <input type="checkbox" id="disapprove" name="status_car" value="ไม่อนุมัติ" onclick="checkOnly(this)">
                        <label for="disapprove">ไม่อนุมัติ</label><br><br>

                        <div class="form-buttons">
                            <button type="submit" name="submit">ตกลง</button>
                            <button type="button" onclick="window.location.href='request.php'">ย้อนกลับ</button>
                        </div>
                    </form>
                </div>

                <script>
                    function checkOnly(checkbox) {
                        var checkboxes = document.getElementsByName('status_car');
                        checkboxes.forEach((item) => {
                            if (item !== checkbox) item.checked = false;
                        });
                    }
                </script>

            </body>
            </html>
            <?php
        } else {
            echo "Use car request not found.";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $comment = empty($_POST['comment']) ? "ไม่มี" : $_POST['comment'];
            $status_car = isset($_POST['status_car']) ? $_POST['status_car'] : '';
            $id_us = $_SESSION['id_us']; 
        
            try {
                $userConn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $userConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $userStmt = $userConn->prepare("SELECT * FROM users WHERE id_us = :id_us");
                $userStmt->bindParam(':id_us', $id_us);
                $userStmt->execute();
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
                if ($user) {
                    $rank_boss = $user['rank'];
                    $prefix_boss = $user['prefix'];
                    $name_boss = $user['name'] . ' ' . $user['last_name'];
        
                    if (!empty($status_car)) {
                        $updateStmt = $conn->prepare("UPDATE use_car SET 
                            status_car = :status_car, 
                            comments = :comment, 
                            id_boss_car = :id_us,
                            rank_boss = :rank_boss,
                            prefix_boss = :prefix_boss,
                            name_boss = :name_boss
                            WHERE idusecar = :idusecar"
                        );
                        $updateStmt->bindParam(':status_car', $status_car);
                        $updateStmt->bindParam(':comment', $comment);
                        $updateStmt->bindParam(':id_us', $id_us);
                        $updateStmt->bindParam(':rank_boss', $rank_boss);
                        $updateStmt->bindParam(':prefix_boss', $prefix_boss);
                        $updateStmt->bindParam(':name_boss', $name_boss);
                        $updateStmt->bindParam(':idusecar', $id_usecar);
                        $updateStmt->execute();
        
                        echo "<script>alert('อัพเดตสำเร็จ'); window.location.href='request.php';</script>";
                    } else {
                        echo "<script>alert('กรุณาเลือกสถานะ');</script>";
                    }
                } else {
                    echo "<script>alert('ไม่พบข้อมูลผู้ใช้');</script>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
