<?php 
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";

session_start();

$conn = mysqli_connect($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if (!$conn) {
    die("การเชื่อมต่อล้มเหลว: " . mysqli_connect_error());
}
 
$errors = array();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if (empty($username)) {
        $errors['username'] = "กรุณากรอก Username";
    }
    if (empty($password)) {
        $errors['password'] = "กรุณากรอก Password";
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $users = mysqli_fetch_assoc($result);

            // ตั้งค่า session variables
            $_SESSION['username'] = $username;
            $_SESSION['id_us'] = $users['id_us'];
            $_SESSION['status'] = $users['status'];
             
            switch ($users['status']) {
                case "ผู้ดูแลระบบ": 
                    header("Location: admin.php");
                    exit();
                case "หัวหน้า":
                    header("Location: boss.php");
                    exit();
                case "เจ้าหน้าที่ทั่วไป":
                    header("Location: users.php");
                    exit();
                default:
                    echo "Invalid user type";
            }
        } else {
            // ไม่พบผู้ใช้ ส่งกลับไปยังหน้า index พร้อมข้อความข้อผิดพลาด
            header("Location: index.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
            exit();
        }
    }
}

// แสดงข้อผิดพลาด ถ้ามี
if (count($errors) > 0) {
    foreach ($errors as $error) {
        echo htmlspecialchars($error) . "<br>";
    }
}

mysqli_close($conn);
?>
