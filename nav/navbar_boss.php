<?php 
// ตรวจสอบว่าเซสชันได้เริ่มต้นแล้วหรือไม่
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// เชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "yanreeho_yanree_db";
    $password = "B@4N+209rhMfoT";
    $dbname = "yanreeho_yanree_db";

try {

    //การแจ้งเตือน
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch notification counts
    $carNotificationStmt = $conn->prepare("SELECT COUNT(*) AS totalCarNotifications FROM use_car WHERE status_car = 'รออนุมัติ'");
    $carNotificationStmt->execute();
    $totalCarNotifications = $carNotificationStmt->fetch(PDO::FETCH_ASSOC)['totalCarNotifications'];

    $projectNotificationStmt = $conn->prepare("SELECT COUNT(*) AS totalProjectNotifications FROM project WHERE status_pro = 'รออนุมัติ'");
    $projectNotificationStmt->execute();
    $totalProjectNotifications = $projectNotificationStmt->fetch(PDO::FETCH_ASSOC)['totalProjectNotifications'];

    // Calculate total notifications
    $totalNotifications = $totalCarNotifications + $totalProjectNotifications;

    $summarizeCount = isset($_SESSION['summarize_count']) ? $_SESSION['summarize_count'] : 0;

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./nav/nav.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dropdown-content, .dropdown-content .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 10; /* เพิ่ม z-index ให้มากกว่าค่าเดิม เพื่อให้เมนูอยู่ด้านบน */
            top: 100%; /* เลื่อน dropdown ให้อยู่ด้านล่างของปุ่ม */
            left: 0;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-content {
            left: 100%; /* เลื่อนเมนูย่อยไปทางขวาของ dropdown หลัก */
            top: 0;
        }

        .notification-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }

        .notification-active a {
            font-weight: bold;
        }

    </style>
</head>
<body>
<div class="topnav">
    <div class="top">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="time" viewBox="0 0 16 16">
            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
        </svg>
        <div class="t1">8:30 a.m. - 4:30 p.m.</div>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
        </svg>
        <div class="t2">055 576 108</div>
    </div>
</div> 

    <!-- Navbar for desktop screens -->
    <nav class="navbar d-none d-lg-block">
        <ul class="navbar-container">
            <li><a href="boss.php" class="btn-5"><img src="image/ชื่อโรงพยาบาล.jpg" alt="โรงพยาบาล"></a></li>
                <ul class="menu">    
                    <li class="menu-item"><a href="boss.php">หน้าหลัก</a></li>   
                    <li class="menu-item <?php echo $totalNotifications > 0 ? 'notification-active' : ''; ?>">
                    <a href="request.php" onclick="markAsRead()">
                        คำร้อง
                        <svg id="notification-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                        <span class="notification-count"><?php echo $totalNotifications; ?></span>
                        </svg>
                    </a>
                </li>
                <li class="menu-item <?php echo $summarizeCount > 0 ? 'notification-active' : ''; ?>">
                    <a href="Project_Summary.php" onclick="markAsRead()">
                        สรุปโครงการ
                        <svg id="notification-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                        <span class="notification-count"><?php echo $summarizeCount; ?></span>
                        </svg>
                    </a>
                </li>
                <!-- เมนูอื่น ๆ -->
                <li class="menu-item"><a href="work_schedule.php">ตารางขอโครงการ</a></li>
                <li class="menu-item"><a href="car_schedule.php">ตารางขอใช้รถ</a></li>
                <li class="menu-item"><a href="pro_statisticsboss.php">สถิติโครงการ</a></li>
                <li class="menu-item right" style="margin-right: 1em;"><a href="index.php">ออกจากระบบ</a></li>
            </ul>
        </ul>
    </nav>

    <!-- Dropdown menu for mobile and tablet screens -->
    <div class="dropdown d-lg-none">
        <a class="btn dropbtn" href="#" onclick="toggleDropdown(event)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
            </svg> 
        </a>
        <div class="dropdown-content">
            <a href="boss.php">หน้าหลัก</a>
            <div class="dropdown-submenu">
            <a href="#" class="submenu-link" onclick="toggleSubmenu(event)">คำร้อง</a>
                <div class="dropdown-content">
                    <a href="mobile_requestpro.php">คำร้องขอโครงการ</a>
                    <a href="mobile_requestcar.php">คำร้องขอไปราชการ</a>
                </div>
            </div>     
            <a href="work_schedule.php">ตารางขอไปราชการ</a>
            <a href="car_schedule.php">ตารางขอใช้รถ</a>
            <a href="pro_statisticsboss.php">สถิติโครงการ</a>
            <a href="index.php">ออกจากระบบ</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function markAsRead() {
            $.post("update_notifications.php", function(response) {
                if (response.status === "success") {
                    $("#notification-icon").removeClass("notification-active");
                    $(".notification-count").text("0");
                } else {
                    console.error("Error updating notifications: " + response.message);
                }
            }, "json");
        }
    </script>

<script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdownContent = event.target.closest('.dropdown').querySelector('.dropdown-content');
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        }

        function toggleSubmenu(event) {
            event.preventDefault();
            event.stopPropagation();
            const submenuContent = event.target.nextElementSibling;
            submenuContent.style.display = submenuContent.style.display === 'block' ? 'none' : 'block';
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn') && !event.target.matches('.submenu-link')) {
                const dropdowns = document.querySelectorAll('.dropdown .dropdown-content');
                dropdowns.forEach(function(dropdown) {
                    dropdown.style.display = 'none';
                });
            }
        }
    </script>

</body>
</html>
