<?php
session_start();
if (!isset($_SESSION['notification_count'])) {
    $_SESSION['notification_count'] = 0; 
}
$notification_count = $_SESSION['notification_count'];
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
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
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

        .notification-count {
            background-color: red; 
            color: white; 
            border-radius: 50%; 
            padding: 0.2em 0.5em; 
            font-size: 0.8em; 
            position: relative; 
            display: inline-block; 
            margin-left: 0.5em; 
        }
    </style>
    <title>Dropdown Example</title>
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
            <li class="logo"><a href="users.php" class="btn-5"><img src="image/ชื่อโรงพยาบาล.jpg" alt="โรงพยาบาล"></a></li>
            <ul class="menu">
                <li class="menu-item"><a href="users.php">หน้าหลัก</a></li>
                <li class="menu-item"><a href="fome_us.php">แบบฟอร์ม</a></li>
                <li class="menu-item"><a href="datacar_us.php">สถานะการใช้รถ</a></li>
                <li class="menu-item <?php echo $notification_count > 0 ? 'notification-active' : ''; ?>">
                    <a href="Status_and_replies.php" onclick="markAsRead()">
                        สถานะและการตอบกลับ
                        <svg id="notification-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                        </svg>
                        <?php if ($notification_count > 0): ?>
                            <span class="notification-count"><?php echo $notification_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="menu-item right" style="margin-right: 1em;"><a href="index.php">ออกจากระบบ</a></li>
            </ul>
        </ul>    
    </nav>
     
    <!-- Dropdown menu for mobile and tablet screens -->
    <div class="dropdown d-lg-none">
        <a class="btn dropbtn" href="#" onclick="toggleDropdown(event)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
            เมนู
        </a>
        <div class="dropdown-content">
            <a href="users.php">หน้าหลัก</a>
            <a href="fome_us.php">แบบฟอร์ม</a>
            <a href="datacar_us.php">สถานะการใช้รถ</a>

            <div class="dropdown-submenu">
            <a href="#" class="submenu-link" onclick="toggleSubmenu(event)">สถานะและการตอบกลับ</a>
                <div class="dropdown-content">
                    <a href="Status_and_replies_promb.php">คำร้องขอโครงการ</a>
                    <a href="Status_and_replies_carmb.php">คำร้องขอไปราชการ</a>
                </div>
            <a href="" onclick="markAsRead()"></a>
            </div> 
            <a href="index.php" style="color: red;">ออกจากระบบ</a>
        </div>
    </div> 

    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = document.querySelector(".dropdown-content");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                const dropdown = document.querySelector(".dropdown-content");
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        }

        function markAsRead() {
            fetch('mark_notifications.php', {
                method: 'POST',
                credentials: 'include' 
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.notification-count').textContent = 0;
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        }


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
