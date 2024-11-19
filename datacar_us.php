<?php
    session_start();
    require_once "./nav/navbar.php";
    require_once "./footer/footer.php";

    $servername = "localhost";
    $username = "yanreeho_yanree_db";
    $password = "B@4N+209rhMfoT";
    $dbname = "yanreeho_yanree_db";
    

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $stmt = $conn->prepare("SELECT * FROM car");
        $stmt->execute();
    
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    // ตรวจสอบว่ามีค่า id_us ใน session หรือไม่
    if(isset($_SESSION['id_us'])){
        $id_us = $_SESSION['id_us'];

        // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $sql = "SELECT * FROM users WHERE id_us = :id_us";

        // ตรงนี้ควรเป็นการใช้ connection ที่คุณได้สร้างไว้
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ทำการ prepare และ execute คำสั่ง SQL
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us', $id_us);
        $stmt->execute();

        // Fetch ข้อมูลผู้ใช้
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    } else {
        // หากไม่มี id_us ใน session ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
        header("Location: login.php"); 
        exit();
    }
?>

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
        font-family: "Prompt", sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background-image: url('../background.jpg'); 
        background-size: cover; /* ทำให้รูปครอบคลุมพื้นที่ทั้งหมด */
        background-repeat: no-repeat; /* ไม่ให้รูปภาพซ้ำซ้อน */
        background-attachment: fixed; /* ทำให้พื้นหลังไม่เลื่อนตามหน้า */
        width: 100%;
        min-height: 100vh; /* กำหนดความสูงขั้นต่ำให้เต็มหน้าจอ */
    }


        content {
            padding: 20px;
        }

        .us{
            font-size: 30px;
            margin-top: 20px;
            text-align: center;

        }

        /* อันนี้คือปรับขนาดของหน้าเว็ปไม่ให้เคลื่อน */
        .grid {
            margin-bottom: 3rem; /* กำหนดขอบล่างของคอนเทนเนอร์กริดไปที่ 3 เร็ม */   
            display: grid; /* ระบุว่าคอนเทนเนอร์ควรเป็นคอนเทนเนอร์กริด */
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* กำหนดคอลัมน์ของกริด */
        }

        .car {
            margin-bottom: 3rem; /* กำหนดขอบล่างของคอนเทนเนอร์กริดไปที่ 3 เร็ม */   
            display: grid; /* ระบุว่าคอนเทนเนอร์ควรเป็นคอนเทนเนอร์กริด */
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* กำหนดคอลัมน์ของกริด */
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 50px;
            margin-bottom: 100px; /* เพิ่ม margin-bottom เพื่อให้มีพื้นที่ระหว่างการ์ดและ Footer */
            width: 150px;
            height: 230px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .card img {
            max-width: 80%; /* กำหนดขนาดรูปภาพให้เล็กลง */
            height: auto;
        }
        .card h3 {
            margin-top: 10px;
            font-size: 16px;
        }

        .card p {
            color: #666;
        }

        .car {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 3 columns with equal width */
            gap: 20px; /* Gap between grid items */
        }

        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;

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
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
        }
        <style>
  /* Default (for screens larger than 1024px) */
  .car {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* 4 columns with equal width */
      gap: 20px; /* Gap between grid items */
  }

  /* For screens between 769px and 1024px */
  @media (min-width: 769px) and (max-width: 1024px) {
      .car {
          grid-template-columns: repeat(3, 1fr); /* 3 columns with equal width */
      }
       

  } 

  @media (max-width: 768px) {
        .user-info-table .text {
            font-size: 16px; 
        }
        .user-info-table .data {
            font-size: 16px; 
        }

       
        } 

  /* For screens 768px and smaller */
  @media (max-width: 768px) {
      .car {
          grid-template-columns: repeat(2, 1fr); /* 2 columns with equal width */
      }
  }

  html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: auto; /* ทำให้สามารถเลื่อนเนื้อหาตามแนวนอนได้ */

        }

        .container {
            max-width: 100%; /* ทำให้ container มีความกว้างสูงสุดที่ 100% ของความกว้างหน้าจอ */
            overflow-x: auto; /* เพิ่ม scroll bar แนวนอนถ้าจำเป็น */
        }

        .navbar {
            width: 100%; /* ทำให้ navbar ครอบคลุมความกว้างทั้งหมด */
            box-sizing: border-box; /* ทำให้ padding และ border รวมอยู่ในความกว้าง */
        }
      
</style>

   
    
</head>
<body>

    <content>
    <div class="container">
    <div style="text-align:center;">
            <h1 class="h1" >รายการ</h1><h2 class="h2" >รถ</h2>
    </div>
    <div class="car">
        <?php foreach ($cars as $car) : ?>
            <div class="card">
                <div class="profile-picture" >
                    <?php if (!empty($car['picture_name'])): ?>
                        <img src="<?php echo './imagecar/' . $car['picture_name']; ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="./image/car.jpg" alt="Default Profile Picture">
                    <?php endif; ?>
                </div>
                <h3><?php echo htmlspecialchars($car['number']); ?></h3>
                <h3><?php echo htmlspecialchars($car['logo']); ?></h3>
                <p><?php echo htmlspecialchars($car['status_car']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</content>

 
</body>
</html>


