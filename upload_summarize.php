<?php
session_start();

require_once "./nav/navbar.php";
require_once "./footer/footer.php";

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_project = $_POST['id_project'];
            $file_name = $_FILES['summarize_file']['name'];
            $file_tmp = $_FILES['summarize_file']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $target_dir = "summarize/";
            $target_file = $target_dir . basename($file_name);
            
            // ตรวจสอบประเภทไฟล์
            $allowed_ext = ['pdf', 'docx'];
            if (in_array($file_ext, $allowed_ext)) {
                // อัปโหลดไฟล์ไปยังโฟลเดอร์ summarize
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $sql = "UPDATE project SET summarize_name = :summarize_name, summarize_path = :summarize_path WHERE id_project = :id_project";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':summarize_name', $file_name);
                    $stmt->bindParam(':summarize_path', $target_file);
                    $stmt->bindParam(':id_project', $id_project);
                    $stmt->execute();
                    echo "<div class='alert alert-success'>อัปโหลดไฟล์สำเร็จ!</div>";
                } else {
                    echo "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์.</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>ไฟล์ที่อัปโหลดต้องเป็นไฟล์ .pdf หรือ .docx เท่านั้น.</div>";
            }
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Connection failed: " . $e->getMessage() . "</div>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปโหลดสรุปโครงการ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Prompt", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('./image/background.JPG');
            background-size: cover;
            margin-bottom: 200px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-upload"></i> อัปโหลดสรุปโครงการ</h3>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_project" value="<?php echo htmlspecialchars($_GET['id_project']); ?>">
                    <div class="form-group">
                        <label for="summarize_file"><i class="fas fa-file-upload"></i> เลือกไฟล์สรุป (ไฟล์ .pdf หรือ .docx เท่านั้น)</label>
                        <input type="file" class="form-control-file" name="summarize_file" id="summarize_file" accept=".pdf,.docx" required>
                        <a type="button" id="clearSummarizeFile" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16" style="color: black; margin-top: 5px;">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-cloud-upload-alt"></i> อัปโหลด</button>
                    <a href="Status_and_replies.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        document.getElementById('summarize_file').addEventListener('change', function() {
            var file = this.files[0];
            var fileSize = file.size; // ขนาดของไฟล์ในหน่วย bytes
            var maxFileSize = 50 * 1024 * 1024; // 50 MB ในหน่วย bytes

            // ตรวจสอบขนาดไฟล์
            else if (fileSize > maxFileSize) {
                // แสดงข้อความแจ้งเตือนเกี่ยวกับขนาดไฟล์
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: 'ขนาดไฟล์ต้องไม่เกิน 50 MB',
                    confirmButtonText: 'ตกลง'
                });

                // เคลียร์ค่าที่เลือกไว้ใน input file
                this.value = '';
                document.getElementById('clearSummarizeFile').style.display = 'none';
            } 
            else {
                // แสดงปุ่มเคลียร์ไฟล์
                document.getElementById('clearSummarizeFile').style.display = 'inline-block';
            }
        });

        document.getElementById('clearSummarizeFile').addEventListener('click', function() {
            document.getElementById('summarize_file').value = ''; // ล้างไฟล์ที่เลือก
            this.style.display = 'none';
        });
    </script>

</body>
</html>
