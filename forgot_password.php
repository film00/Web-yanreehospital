<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">
    <title>กู้คืนรหัสผ่าน</title>
    
    <style> 
        body {
            font-family: 'Sriracha', cursive;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 350px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        input[type="email"] {
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 20px;
            color: red;
        }
    </style>
      
</head> 
<body>
    <div class="container">
        <h2>กู้คืนรหัสผ่าน</h2>
        <form action="process_forgot_password.php" method="post">
            <label for="email">กรุณากรอกอีเมลของคุณ:</label>
            <input type="email" id="email" name="email" placeholder="example@example.com" required>
            <button type="submit">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
        </form>

        <!-- แสดงข้อความแจ้งเตือน (ถ้ามี) -->
        <?php if (isset($_GET['message'])): ?>
            <div class="message"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
