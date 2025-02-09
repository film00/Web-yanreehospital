<?php
    session_start();
    require_once "./nav/navbar.php";

    $servername = "**********";
    $username = "**********";
    $password = "**********";
    $dbname = "**********";
    

    if(isset($_SESSION['id_us'])){
        $id_us = $_SESSION['id_us'];
    } else {
        header("Location: login.php"); 
        exit();
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

<style>
   body {
    font-family: "Prompt", sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    background-image: url('../background.jpg'); 
    background-size: cover;
    background-repeat: no-repeat; 
    background-attachment: fixed;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    margin-bottom: 100px;
    }
    .h1, .h2, .h3 {
        display: inline-block; 
        margin: 5px 5px -20px;
        padding: 0px;
    }

    .h1 {
        color: #000;
        font-size: 50px;
    }

    .h2 {
        color: #ffffff;
        font-size: 50px;
    }

    .h3 {
        color: #ffffff;
        font-size: 30px;
    }

    .content {
        padding: 20px;
        display: flex;
        flex: 1;
        background-color: rgba(255, 255, 255);
        border-radius: 8px;
    }

    .d-flex {
        display: flex;
        flex-direction: row; /* แนวตั้ง */
    }

    .sidebar-column {
        width: 12rem;
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-right: 20px;
        flex-shrink: 0;
        overflow-y: auto;
        margin-bottom: 70px;
    }

    .main-column {
        flex: 1; /* ขยายให้เต็มพื้นที่ที่เหลือ */
        padding: 20px;
        background-color: #CCFF99;
        border-radius: 8px;
        overflow-y: auto;
        height: auto;
        margin-bottom: 70px;
    }

    .nav-link {
        border: 1px solid #ccc; /* Border properties */
        border-radius: 8px; /* Rounded corners */
        padding: 10px; /* Padding around each link */
        display: block;
        text-decoration: none; /* Remove underline */
        color: #000; /* Link text color */
        transition: all 0.3s ease; /* Smooth transition */
    }

    .nav-link:hover {
        background-color: #f0f0f0; /* Background color on hover */
    }

    .nav-link svg {
        /* Icon styles */
        margin-bottom: 5px; /* Adjust spacing */
    }
    @media (max-width: 768px) {
    .sidebar-column {
        display: none;
    }
}
</style>


<body>

    <div style="text-align:center;">
        <h1 class="h1">แบบฟอร์มโครงการ</h1><h2 class="h2">&</h2><br><h3 class="h3">ขออนุญาตเดินทางไปราชการ</h3>
    </div>
    <div class="d-flex flex-row flex-shrink-0">
        <!-- Sidebar column -->
        <div class="sidebar-column">
            <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="border-3 border-primary rounded-3">
                    <a href="indexform.php" class="nav-link py-3 border-bottom rounded-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dashboard" data-bs-original-title="Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-pass-fill" viewBox="0 0 16 16">
                            <path d="M10 0a2 2 0 1 1-4 0H3.5A1.5 1.5 0 0 0 2 1.5v13A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 12.5 0zM4.5 5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1m0 2h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1"/>
                        </svg><br>
                        <span>แบบฟอร์ม</span>
                    </a>
                </li>
    
                <li class="border-3 border-primary rounded-3">
                    <a href="pro_history.php" class="nav-link py-3 border-bottom rounded-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                    <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z"/>
                    </svg><br>
                        <span>ประวัติโครงการ</span>
                    </a> 
                </li>

                <li class="border-3 border-primary rounded-3">
                    <a href="car_history.php" class="nav-link py-3 border-bottom rounded-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
                            <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                        </svg><br>
                            <span>ประวัติขออนุญาตไปราชการ</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main content column -->
        <div class="main-column">
            <!-- Content will be loaded here -->
        </div>
    </div>

    <?php require_once "./footer/footer.php"; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function() {
    // โหลดเนื้อหาของ indexform.php เมื่อหน้าเว็บโหลด
    $('.main-column').load('indexform.php');

    // เมื่อคลิกที่ลิงก์ "แบบฟอร์มโครงการ"
    $('a[href="indexform.php"]').click(function(event) {
        event.preventDefault(); // ป้องกันการทำงานเริ่มต้นของลิงก์
        // โหลดเนื้อหาจาก indexform.php ลงใน main-column
        console.log('Loading indexform.php');
        $('.main-column').load($(this).attr('href'));
    });

    // เมื่อคลิกที่ลิงก์ "ประวัติการทำโครงการ"
    $('a[href="pro_history.php"]').click(function(event) {
        event.preventDefault(); // ป้องกันการทำงานเริ่มต้นของลิงก์
        // โหลดเนื้อหาจาก pro_history.php ลงใน main-column
        console.log('Loading pro_history.php');
        $('.main-column').load($(this).attr('href'));
    });

    // เมื่อคลิกที่ลิงก์ "ประวัติการทำโครงการใช้รถ"
    $('a[href="car_history.php"]').click(function(event) {
        event.preventDefault(); // ป้องกันการทำงานเริ่มต้นของลิงก์
        // โหลดเนื้อหาจาก car_history.php ลงใน main-column
        console.log('Loading car_history.php');
        $('.main-column').load($(this).attr('href'));
    });

    // เรียกใช้ฟังก์ชันเมื่อลดขนาดหน้าจอ
    $(window).resize(adjustLayout);

    // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเว็บ
    adjustLayout();
        });
</script>

</body>
</html>
