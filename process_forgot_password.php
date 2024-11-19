<?php
$servername = "localhost";
$username = "yanreeho_yanree_db"; // เปลี่ยนข้อมูลผู้ใช้ให้ถูกต้อง
$password = "B@4N+209rhMfoT";     // ใช้รหัสผ่านที่ถูกต้อง
$dbname = "yanreeho_yanree_db";   // ชื่อฐานข้อมูลที่ถูกต้อง

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เชื่อมต่อฐานข้อมูล
    $dsn = 'mysql:host=localhost;dbname=yanreeho_yanree_db;charset=utf8'; // ใช้ข้อมูลฐานข้อมูลที่ถูกต้อง
    $username = 'yanreeho_yanree_db';  // เปลี่ยนเป็นผู้ใช้ที่ถูกต้อง
    $password = 'B@4N+209rhMfoT';      // เปลี่ยนเป็นรหัสผ่านที่ถูกต้อง

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    // รับค่าที่ผู้ใช้กรอก
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // ตรวจสอบอีเมลจากฐานข้อมูล
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
