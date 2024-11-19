<?php
$email = 'user@example.com'; // อีเมลผู้รับ
$subject = 'รีเซ็ตรหัสผ่านของคุณ'; // หัวเรื่องอีเมล

// สร้างลิงก์รีเซ็ตรหัสผ่าน
$resetToken = 'abc123'; // โทเค็นที่ใช้รีเซ็ตรหัสผ่าน (ควรสร้างแบบสุ่มและปลอดภัย)
$resetLink = "https://example.com/reset_password.php?token=$resetToken"; 

// ข้อความในอีเมล
$message = "
<html> 
<head>
    <title>รีเซ็ตรหัสผ่าน</title>
</head>
<body>
    <p>คลิกลิงก์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ:</p>
    <a href='$resetLink'>$resetLink</a>
</body>
</html>
";

// การตั้งค่า header ของอีเมล
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: Your Name <phatthatasudaphumjun@gmail.com>' . "\r\n"; // เปลี่ยนเป็นอีเมลจริงของคุณ

// ส่งอีเมล
if(mail($email, $subject, $message, $headers)) {
    echo 'อีเมลถูกส่งสำเร็จ';
} else {
    echo 'ไม่สามารถส่งอีเมลได้';
}
?>
