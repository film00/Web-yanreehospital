<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-color: #84dcc6;
            color: white;
            text-align: center;
            padding: 10px;
        }

        nav {
            background-color: #006400;
            color: white;
            padding: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        content {
            padding: 20px;
        }

        footer {
            background-color: #ffa69d;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <header>
        <h1 style="font-family: 'Kanit', sans-serif;">เว็บแอปจัดการโครงการ</h1>
    </header>

    <nav>
        <a href="users.php">หน้าหลัก</a>
        <a href="#profile">กรอกแบบฟอร์ม</a>
        <a href="#settings">โครงการ</a>
        <a href="#settings">ประวัติการทำโครงการ</a>
        <a href="index.php">ออกจากระบบ</a>
    </nav>

    <content>
        <!-- ส่วนนี้ใส่เนื้อหาหน้า users ตามต้องการ -->
        <h2>Welcome, [Username]!</h2>
        <p>This is your user dashboard content.</p>
    </content>

    <footer>
        &copy; 2023 User Dashboard
    </footer>

</body>
</html>


