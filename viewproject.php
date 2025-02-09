<?php
session_start();
require_once "./nav/navbar_boss.php";
require_once "./footer/footer.php";


$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

if (isset($_GET['id'])) {
    $id_pro = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch project data based on id_pro
        $projectStmt = $conn->prepare("SELECT * FROM project WHERE id_project = :id_project");
        $projectStmt->bindParam(':id_project', $id_pro);
        $projectStmt->execute();
        $project = $projectStmt->fetch(PDO::FETCH_ASSOC);

        if ($project) {
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

        .commenttext {
            width: 100%;
            height: 150px;
            box-sizing: border-box;
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
    </style>

</head>
<body>
    <div class="content">
        <h2><?php echo htmlspecialchars($project['name_project']); ?></h2>
        <p><b>หลักการและเหตุผล: </b><?php echo htmlspecialchars($project['reason_and_reason']); ?></p>
        <p><b>วัตถุประสงค์: </b><?php echo htmlspecialchars($project['objective']); ?></p>
        <p><b>กลุ่มเป้าหมาย: </b><?php echo htmlspecialchars($project['Target_group']); ?></p>
        <p><b>ระยะเวลาดำเนินการ: </b><?php echo htmlspecialchars($project['Processing_time']); ?></p>
        <p><b>งบประมาณ: </b><?php echo htmlspecialchars($project['Budget']); ?></p>
        <p><b>การประเมินผล: </b><?php echo htmlspecialchars($project['Evaluation']); ?></p>
        <p><b>สถานะโครงการ: </b><?php echo htmlspecialchars($project['status_pro']); ?></p>
    </div>

    <?php
        $fileExtension = pathinfo($project['file_name'], PATHINFO_EXTENSION);
    ?>

    <div class="content">
        <p><b>เอกสารเพิ่มเติม: </b></p>
        <?php if (!empty($project['file_name']) && !empty($project['file_path'])): ?>
            <?php 
                $fileExtension = pathinfo($project['file_name'], PATHINFO_EXTENSION);
            ?>
            <?php if ($fileExtension === 'pdf'): ?>
                <!-- เปิดไฟล์ PDF ในแท็บใหม่ -->
                <p><a href="https://yanreehospital.com/file/<?php echo htmlspecialchars($project['file_name']); ?>" target="_blank">
                    <?php echo htmlspecialchars($project['file_name']); ?>
                </a></p>
            <?php else: ?>
                <!-- ดาวน์โหลดไฟล์อื่น ๆ -->
                <p><a href="https://yanreehospital.com/file/<?php echo htmlspecialchars($project['file_name']); ?>" download>
                    <?php echo htmlspecialchars($project['file_name']); ?>
                </a></p>
            <?php endif; ?>
        <?php else: ?>
            <p>ไม่มี</p>
        <?php endif; ?>
    </div>

    <div class="content">
        <form id="commentForm" method="POST">
            <label for="comment">แสดงความคิดเห็น:</label><br>
            <textarea id="commenttext" name="comment" class="commenttext" placeholder="ไม่มี"></textarea><br><br>

            <input type="checkbox" id="approve" name="status" value="อนุมัติ" onclick="checkOnly(this)">
            <label for="approve">อนุมัติ</label><br>
            <input type="checkbox" id="disapprove" name="status" value="ไม่อนุมัติ" onclick="checkOnly(this)">
            <label for="disapprove">ไม่อนุมัติ</label><br><br>

            <div class="form-buttons">
                <button type="submit" name="submit">ตกลง</button>
                <button type="button" onclick="window.location.href='request.php'">ย้อนกลับ</button>
            </div>
        </form>
    </div>

    <script>
        function checkOnly(checkbox) {
            var checkboxes = document.getElementsByName('status');
            checkboxes.forEach((item) => {
                if (item !== checkbox) item.checked = false;
            });
        }

        document.getElementById('commentForm').addEventListener('submit', function (e) {
            var checkboxes = document.getElementsByName('status');
            var isChecked = false;
            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    isChecked = true;
                }
            });

            if (!isChecked) {
                alert('กรุณาเลือกสถานะอนุมัติหรือไม่อนุมัติ');
                e.preventDefault(); // ป้องกันการส่งฟอร์ม
            }
        });
    </script>

</body>
</html>
<?php
        } else {
            echo "Project not found.";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $comment = empty($_POST['comment']) ? "ไม่มี" : $_POST['comment'];
            $status = $_POST['status'];
            $id_us = $_SESSION['id_us']; // ID ของผู้ใช้งานที่กำลังเข้าสู่ระบบ
        
            try {
                // ดึงข้อมูลผู้ใช้
                $userStmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
                $userStmt->bindParam(':id_us', $id_us);
                $userStmt->execute();
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
                if ($user) {
                    $rank_boss_pro = $user['rank'];
                    $prefix_boss_pro = $user['prefix'];
                    $name_boss_pro = $user['name'] . ' ' . $user['last_name'];
        
                    // Update the project status and add the comment
                    $updateStmt = $conn->prepare("UPDATE project SET 
                        status_pro = :status, 
                        comments = :comment, 
                        id_boss_pro = :id_us,
                        prefix_boss_pro = :prefix_boss_pro,
                        name_boss_pro = :name_boss_pro,
                        rank_boss_pro = :rank_boss_pro,
                        status_updated_at = CURRENT_TIMESTAMP
                        WHERE id_project = :id_project"
                    );
        
                    // Bind parameters
                    $updateStmt->bindParam(':status', $status);
                    $updateStmt->bindParam(':comment', $comment);
                    $updateStmt->bindParam(':id_us', $id_us);
                    $updateStmt->bindParam(':prefix_boss_pro', $prefix_boss_pro);
                    $updateStmt->bindParam(':name_boss_pro', $name_boss_pro);
                    $updateStmt->bindParam(':rank_boss_pro', $rank_boss_pro);
                    $updateStmt->bindParam(':id_project', $id_pro);
        
                    // Execute the update query
                    $updateStmt->execute();
                    echo "<script>alert('ข้อมูลถูกอัปเดตเรียบร้อยแล้ว'); window.location.href='request.php';</script>";
                } else {
                    echo "User not found.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No project ID specified.";
}
?>
