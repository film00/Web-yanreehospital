<?php
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $dsn = 'mysql:host=localhost;dbname=yanreeho_yanree_db;charset=utf8'; 
    $username = 'yanreeho_yanree_db';  
    $password = 'B@4N+209rhMfoT';      

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    
    $sql = "SELECT username, password FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // ดึงข้อมูล username และ password
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $user['username'];
        $password = $user['password'];

        // เตรียมข้อความในอีเมล
        $to = $email;
        $subject = "ข้อมูลการเข้าสู่ระบบของคุณ";
        $message = "ชื่อผู้ใช้ของคุณ: $username\nรหัสผ่านของคุณ: $password";
        $headers = "From: no-reply@yourdomain.com";

        // ส่งอีเมล
        if (mail($to, $subject, $message, $headers)) {
            header("Location: forgot_password.php?message=ส่งข้อมูลไปยังอีเมลของคุณเรียบร้อยแล้ว");
        } else {
            header("Location: forgot_password.php?message=เกิดข้อผิดพลาดในการส่งอีเมล");
        }
    } else {
        // ถ้าไม่พบอีเมลในฐานข้อมูล
        header("Location: forgot_password.php?message=ไม่พบอีเมลนี้ในระบบ");
    }
} else {
    // ถ้าผู้ใช้เข้าถึงไฟล์นี้โดยไม่ใช่วิธี POST
    header("Location: forgot_password.php");
    exit();
}
