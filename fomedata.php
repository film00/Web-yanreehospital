<?php
session_start();

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


if(isset($_SESSION['id_us'])){
    $id_us = $_SESSION['id_us'];

    try {
        // สร้าง connection PDO และ set attribute ERRMODE_EXCEPTION
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $sql = "SELECT * FROM users WHERE id_us = :id_us";

        // ทำการ prepare และ execute คำสั่ง SQL
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_us', $id_us);
        $stmt->execute();

        // Fetch ข้อมูลผู้ใช้
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $file = null;

        // ตรวจสอบว่ามีไฟล์ถูกเลือกมาหรือไม่
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['fileInput']['tmp_name'];
            $fileName = $_FILES['fileInput']['name'];
        
            // อัพโหลดไฟล์ไปยังโฟลเดอร์ที่ต้องการ (เช่น 'uploads/')
            $uploadDir = 'uploads/';
            $uploadPath = $uploadDir . $fileName;
        
            move_uploaded_file($fileTmpPath, $uploadPath);
            $file = $uploadPath;  // กำหนดค่าตัวแปร $file ให้เป็นตำแหน่งที่ไฟล์ถูกบันทึกไว้
        
            // เพิ่มบรรทัดนี้เพื่อบันทึก id_file ของไฟล์
            $stmtFile = $conn->prepare("INSERT INTO files (file_path) VALUES (:file_path)");
            $stmtFile->bindParam(':file_path', $file);
            $stmtFile->execute();
            $id_file = $conn->lastInsertId(); // ดึง id ที่ถูกเพิ่งเพิ่มเข้าไป
        } else {
            // ถ้าไม่มีไฟล์ถูกเลือกให้ $id_file เป็นค่าว่าง
            $id_file = null;
        }


        if (isset($_POST['submitProject'])) {
            // ตรวจสอบว่ามีการส่งข้อมูล project มาหรือไม่
            // และ validate ข้อมูลก่อนบันทึกลงในฐานข้อมูล
            if (isset($_POST['name_project'], $_POST['reason_and_reason'], $_POST['objective'], $_POST['target_group'], $_POST['processing_time'], $_POST['budget'], $_POST['evaluation'])) {
                $id_us_pro = $user['id_us'];
                // ใช้ $id_file ที่ได้จากการบันทึกไฟล์
                $id_fomecar = $use_car['idusecar'];
                $name_project = $_POST['name_project'];
                $name_us = $user['name'] . ' ' . $user['last_name'];
                $reason_and_reason = $_POST['reason_and_reason'];
                $objective = $_POST['objective'];
                $target_group = $_POST['target_group'];
                $processing_time = "ตั้งแต่วันที่ " . $_POST['processing_time_start'] . " ถึง " . $_POST['processing_time_end'];
                $budget = $_POST['budget'];
                $evaluation = $_POST['evaluation'];
                $status_pro = "รออนุมัติ";
                $summarize = "ยังไม่ได้สรุปโครงการ";
                $time = date('Y-m-d H:i:s');
        
                try {
                    $stmt = $conn->prepare("INSERT INTO project (id_us_pro, name_project, name_us, reason_and_reason, objective, Target_group, Processing_time, Budget, Evaluation, summarize, status_pro, time, id_file, id_fomecar) 
                                        VALUES (:id_us_pro, :name_project, :name_us, :reason_and_reason, :objective, :target_group, :processing_time, :budget, :evaluation, :summarize, :status_pro, :time, :id_file, :id_fomecar)");
        
                    $stmt->bindParam(':id_us_pro', $id_us_pro);
                    $stmt->bindParam(':id_fomecar', $id_fomecar);
                    $stmt->bindParam(':name_project', $name_project);
                    $stmt->bindParam(':name_us', $name_us);
                    $stmt->bindParam(':reason_and_reason', $reason_and_reason);
                    $stmt->bindParam(':objective', $objective);
                    $stmt->bindParam(':target_group', $target_group);
                    $stmt->bindParam(':processing_time', $processing_time);
                    $stmt->bindParam(':budget', $budget);
                    $stmt->bindParam(':evaluation', $evaluation);
                    $stmt->bindParam(':status_pro', $status_pro);
                    $stmt->bindParam(':summarize', $summarize);
                    $stmt->bindParam(':time', $time);
                    $stmt->bindParam(':id_file', $id_file);
                    $stmt->execute();
        
                    echo '<script>
                        function showSuccessMessage() {
                            alert("โครงการถูกบันทึกเรียบร้อยแล้ว");
                        }
                        showSuccessMessage();
                    </script>';
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo '<script>
                    function showErrorMessage() {
                        alert("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                    }
                    showErrorMessage();
                </script>';
            }
        }

        if (isset($_POST['submitUseCar'])) {
            // Add the necessary conditions and validation for the use_car table fields
            // Retrieve values from the form
        
            $id_us_car = $user['id_us'];
            $name = $user['name'];
            $last_name = $user['last_name']; 
            $rank = $user['rank'];
            $go_to = $_POST['approval'];
            $about_costs = isset($_POST['approval']) ? $_POST['approval'] : null;
            $from_date = $_POST['startDate'];
            $up_date = $_POST['endDate'];
            $sum_date = $_POST['totalDays'];
            $carrier = isset($_POST['approval']) ? $_POST['approval'] : null;
            $id_car = $_POST['vehicleRegistration'];
            $driver = $_POST['driver']; 
            $gasoline_cost = $_POST['fuel'] === 'fuel' ? 'ขอเบิกค่าน้ำมันเชื้อเพลิง' : 'ไม่ขอเบิกค่าน้ำมันเชื้อเพลิง'; // ให้เป็น '1' ถ้าขอเบิก '0' ถ้าไม่ขอเบิก
            $rest = $_POST['overnightStay'] === 'stayOvernight' ? 'พักค้างคืน' : 'ไม่พักค้างคืน'; // ให้เป็น '1' ถ้าพัก '0' ถ้าไม่พัก
            $subject = isset($_POST['purpose']) ? $_POST['purpose'] : null;
            $Place_of_official_business = isset($_POST['destination']) ? $_POST['destination'] : null;
            $sumfollower = isset($_POST['travelerCount']) ? $_POST['travelerCount'] : null;
            $follower = isset($_POST['travelers']) ? $_POST['travelers'] : null;
            $id_project = ''; 
        
            try {
                // Insert data into use_car table
                $stmt = $conn->prepare("INSERT INTO use_car (id_us_car, name, last_name, `rank`, go_to, about_costs, from_date, up_date, sum_date, carrier, id_car, driver, gasoline_cost, rest, subject, Place_of_official_business, sumfollower, follower, id_project)
                       VALUES (:id_us_car, :name, :last_name, :rank, :go_to, :about_costs, :from_date, :up_date, :sum_date, :carrier, :id_car, :driver, :gasoline_cost, :rest, :subject, :Place_of_official_business, :sumfollower, :follower, :id_project)");
        
                // Bind parameters
                $stmt->bindParam(':id_us_car', $id_us_car);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':rank', $rank);
                $stmt->bindParam(':go_to', $go_to);
                $stmt->bindParam(':about_costs', $about_costs);
                $stmt->bindParam(':from_date', $from_date);
                $stmt->bindParam(':up_date', $up_date);
                $stmt->bindParam(':sum_date', $sum_date);
                $stmt->bindParam(':carrier', $carrier);
                $stmt->bindParam(':id_car', $id_car);
                $stmt->bindParam(':driver', $driver);
                $stmt->bindParam(':gasoline_cost', $gasoline_cost);
                $stmt->bindParam(':rest', $rest);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':Place_of_official_business', $Place_of_official_business);
                $stmt->bindParam(':sumfollower', $sumfollower);
                $stmt->bindParam(':follower', $follower);
                $stmt->bindParam(':id_project', $id_project);
        
                // Execute the query
                $stmt->execute();
        
                // Get the last inserted id
                $id_fomecar = $conn->lastInsertId();
        
                // Update the use_car table with the id_fomecar
                $stmtUpdate = $conn->prepare("UPDATE use_car SET id_project = :id_project WHERE idusecar = :idusecar");
                $stmtUpdate->bindParam(':id_project', $id_project);
                $stmtUpdate->bindParam(':idusecar', $id_fomecar);
                $stmtUpdate->execute();
        
                // Display success message
                echo '<script>
                    function showSuccessMessage() {
                        alert("ข้อมูลใช้รถถูกบันทึกเรียบร้อยแล้ว");
                    }
                    showSuccessMessage();
                </script>';
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // หากไม่มี id_us ใน session ให้ทำการ redirect หรือทำอะไรตามที่ต้องการ
    header("Location: login.php"); 
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/i18n/jquery-ui-i18n.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-6eCt98miMD5vdW60h64V/xEN+5Lz6Jc6InTtJxE1cHKw3uuOJ+qht6QRdD3+tsvYp/i8O6C5FIfqTVZ0cQw7tQ==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyTq5IYFXTA2zI" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJov0ZeKA7xLpLJo3eN0nSGjANf4cZ9S3U8FF4I1dMzWytcQ55P5e" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyTq5IYFXTA2zI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
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
            height: 24mm;
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

        body {
          font-family: Arial, sans-serif;
        }


        .hidden {
            display: none;
        }

        body {
        font-family: Arial, sans-serif;
        }

        .navbar {
        overflow: hidden;
        background-color: #333;
        }

        .navbar ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        }

        .navbar li {
        float: left;
        }

        .navbar li a, .navbar .dropbtn {
        display: inline-block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        }

        .navbar li a:hover, .navbar .dropdown:hover .dropbtn {
        background-color: red;
        }

        .navbar .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        }

        .navbar .dropdown-content a {
        color: black;
        padding: 12px 16px;
        display: block;
        text-align: left;
        }

        .navbar .dropdown-content a:hover {
        background-color: #ddd;
        }

        .navbar .dropdown:hover .dropdown-content {
        display: block;
        }

        /*pro*/
        .project {
            box-sizing: border-box; /* เพิ่มบรรทัดนี้ */
            margin-top: 20px; /* ระยะห่างด้านบน */
            padding-top: 10mm; /* ระยะห่างด้านบนเพิ่มเติม */
            font-size: 18;
        }

        .head {
            display: flex;
            align-items: center;
            justify-content: center; /* เพิ่มบรรทัดนี้เพื่อกึ่งกลางในแนวนอน */
            font-weight: bold;
            font-size: 18px;
        }

        .head label {
            margin-right: 10px;
        }

        .project textarea {
            margin-left: 10mm;
            margin-right: 10mm;
            display: block;
            width: 300mm;
            height: 50mm;
        }

        .project label {
            margin-left: 10mm;
            margin-right: 10mm;

        }

        .project form label {
            font-weight: bold;
        }

        /*procar*/
        .projectcar {
            margin-top: 20px;
            padding-top: 10mm;
            font-size: 18px; /* เพิ่มหน่วยเพื่อให้ได้ผลลัพธ์ที่ถูกต้อง */
            padding-left: 50mm; /* เพิ่ม padding ด้านซ้าย 10mm */
            padding-right: 50mm; /* เพิ่ม padding ด้านขวา 10mm */
        }
        
        .head {
            margin: auto;
            text-align: center; /* ทำให้เนื้อหาอยู่กึ่งกลางตามแนวนอน */
        }

        .applicant-label {
            margin-left: 20mm;
        }

        .applicant-label, input[type="text"], select, textarea {
            margin-top: 5mm;
        }

        #submitFormButton {
            margin-top: 30px;
            margin-left: 20px;
            background-color: #4CAF50; /* สีเขียว */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        #submitFormButton:hover {
            background-color: #45a049; /* เมื่อชี้ที่ปุ่ม */
        }
        

        

    </style>

</head>
    <body>

        <header>
            <h1 style="font-family: 'Kanit', sans-serif;">เว็บแอปจัดการโครงการ</h1>
        </header>


        <nav class="navbar">
    <ul>
        <li><a href="admin.php">หน้าหลัก</a></li>
    <li class="dropdown">
        <a href="fome.php" class="dropbtn">แบบฟอร์ม</a>
        <div class="dropdown-content">
            <a href="fome.php">แบบฟอร์ม</a>
            <a href="pro_history.php">ประวัติการทำโครงการ</a>   
        <li><a href="datausers.php">จัดการสมาชิก</a></li>
        <li><a href="datacar.php">ข้อมูลรถ</a></li>
        <li><a href="pro_statistics.php">สถิติโครงการ</a></li>
        <li><a href="index.php">ออกจากระบบ</a></li>
          
        </div>
        </li>
        
    </ul>
        </nav>

    <script src="script.js"></script>


    <div id="formContainer" style="margin-left: 20mm;">
    <content1>
        <h2>ดาวน์โหลดฟอร์มโครงการ</h2>
        <a href="from_pro.docx" download="from_pro.docx">ดาวน์โหลดฟอร์ม</a>

        <h2>ฟอร์มโครงการ</h2>
        <input type="checkbox" id="loadFormCheckbox" name="formCheckbox" onchange="updateForms()"> กรอกแบบฟอร์ม
        <input type="checkbox" id="uploadCheckbox" name="uploadCheckbox" onchange="updateForms()"> อัพโหลดเอกสารเพิ่มเติม

        <form id="projectForm" class="hidden" action="fome.php" method="post" enctype="multipart/form-data">
            <label for="file">Select File:</label>
            <input type="file" name="file" id="file" required>
        </form>

        <div id="upload" class="project hidden">
            <form id="upload" method="post" action="">
                <div class="head" name="name_project" contenteditable="true" id="projectName"></div>
                <input type="text" class="head" name="name_project" contenteditable="true" placeholder="โครงการ...(ชื่อ)...">

                <label for="projectReason">หลักการและเหตุผล</label>
                <textarea id="projectReason" name="reason_and_reason" placeholder="กรุณากรอกหลักการและเหตุผล"></textarea>

                <label for="projectObjective">วัตถุประสงค์</label>
                <textarea id="projectObjective" name="objective" placeholder="กรุณากรอกวัตถุประสงค์"></textarea>

                <label for="projectTargetGroup">กลุ่มเป้าหมาย</label>
                <textarea id="projectTargetGroup" name="target_group" placeholder="กรุณากรอกกลุ่มเป้าหมาย"></textarea>

                <div class="form-group">
                    <label for="startDate">ตั้งแต่ :</label>
                    <input type="month" id="startDate" name="start_date">
                </div>
                <div class="form-group">
                    <label for="endDate">ถึง :</label>
                    <input type="month" id="endDate" name="end_date">
                </div>

                <label for="projectBudget">งบประมาณ</label>
                <textarea id="projectBudget" name="budget" placeholder="กรุณากรอกงบประมาณ"></textarea>

                <label for="projectEvaluation">การประเมินผล</label>
                <textarea id="projectEvaluation" name="evaluation" placeholder="กรุณากรอกการประเมินผล"></textarea>

            </form>
        </div>



    </content1>

    <content2>
        <h2>ฟอร์มขออนุญาติใช้รถ</h2>
        <label>
            <input type="checkbox" id="loadFormCheckboxCar" name="formCheckboxCar" onchange="updateFormsCar()">กรอกแบบฟอร์มใช้รถ
        </label>
        <div id="content2" class="hidden">
            <div class="projectcar">
                <form id="carForm"  method="post">
                    <h3 class="head" for="travelTitle">ใบขออนุญาตเดินทางไปราชการ</h3><br>
                    <h3 class="head" for="agency">หน่วยงาน โรงพยาบาลส่งเสริมสุขภาตำบลย่านรี</h3>

                    <br><br><br>
                    <label class="position" for="travelInstructions"><br>คำชี้แจงการเดินทางไปราชการ<br></label>

                    <br>
                    <label class="applicant-label" for="applicant">ข้าพเจ้า</label>
                    <input type="text" class="applicant" name="applicant" placeholder="<?php echo isset($user['prefix']) ? $user['prefix'] : ''; ?><?php echo isset($user['name']) ? $user['name'] . ' ' : 'กรุณาเข้าสู่ระบบ'; ?><?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>" style="width: 400px; " >

                    <label for="position">ตำแหน่ง</label>
                    <input type="text" id="position" name="position" placeholder="<?php echo isset($user['rank']) ? $user['rank'] : ''; ?>"style="width: 265px; "><br>

                    <label for="go_to">ขออนุมัติไปราชการ</label>
                    <select id="go" name="go">
                        <option value="ไปราชการในจังหวัด">ไปราชการในจังหวัด</option>
                        <option value="ไปราชการนอกจังหวัด">ไปราชการนอกจังหวัด</option>
                    </select>

                    <label for="expenses"><br>ขอเบิกค่าใช้จ่ายจาก</label>
                    <select id="about_costs" name="about">
                        <option value="เบิกงบผู้จัด">เบิกงบผู้จัด</option>
                        <option value="เบิกงบกลาง">เบิกงบกลาง</option>
                        <option value="สลจ. ตาก">สลจ. ตาก</option>
                        <option value="เบิกเงินบำรุง">เบิกเงินบำรุง</option>
                        <option value="ไม่ขอเบิก">ไม่ขอเบิก</option>
                    </select>

                    <br>
                    <div class="form-group">
                        <label for="startDate" style="margin-right: 2px;">ตั้งแต่วันที่:</label>
                        <input type="text" class="form-control datepicker" id="startDate" name="startDate" placeholder="กรุณาเลือกวันที่" style="display: inline-block; width: auto; margin-right: 2px;">
                        <label for="endDate" style="margin-right: 10px;">ถึงวันที่:</label>
                        <input type="text" class="form-control datepicker" id="endDate" name="endDate" placeholder="กรุณาเลือกวันที่" style="display:  margin-right: 2px;">
                        <label style="margin-right: 10px;">รวม</label>
                        <input type="text" id="totalDays" name="totalDays" placeholder="0" readonly style="width: 50px; display: inline-block;"> วัน
                    </div>

                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJov0ZeKA7xLpLJo3eN0nSGjANf4cZ9S3U8FF4I1dMzWytcQ55P5e" crossorigin="anonymous"></script>
                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyTq5IYFXTA2zI" crossorigin="anonymous"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

                    <br>
                    <label for="transportation"><br>โดยยานพาหนะ</label>
                    <select id="approval" name="approval">
                        <option>รถประจำทาง</option>
                        <option>รถรับจ้าง</option>
                        <option>รถส่วนตัว</option>
                        <option>รถราชการ</option>
                        <option>เครื่องบิน</option>
                    </select>

                    <label for="vehicleRegistration"><br>ทะเบียน <input type="text" id="vehicleRegistration" name="vehicleRegistration" placeholder="กรุณากรอกทะเบียนรถ"> พนักงานขับรถ <input type="text" id="driver" name="driver" placeholder="กรุณากรอกทะเบียนรถ"></label>
                    <br>
                    <input type="radio" id="fuel" name="fuel" value="fuel" style="margin-left: 30mm;">
                    <label for="fuel">ขอเบิกค่าน้ำมันเชื้อเพลิง</label>

                    <input type="radio" id="noFuel" name="fuel" value="noFuel" style="margin-left: 30mm;">
                    <label for="noFuel">ไม่ขอเบิกค่าน้ำมันเชื้อเพลิง</label>
                    
                    <br>
                    <input type="radio" id="stayOvernight" name="overnightStay" value="stayOvernight" style="margin-left: 30mm;">
                    <label for="stayOvernight">พักค้างคืน</label>

                    <input type="radio" id="noStayOvernight" name="overnightStay" value="noStayOvernight" style="margin-left: 57.7mm;">
                    <label for="noStayOvernight">ไม่พักค้างคืน</label>

                    <br>
                    <label for="purposes">เรื่อง/งานที่ไปราชการ</label>
                    <br>
                    <textarea name="purpose" placeholder="เรื่อง/งานที่ไปราชการ" style="width: 232mm; height: 30mm;"></textarea>

                    <br>
                    <label for="destinations">สถานที่ไปราชการ </label>
                    <br>
                    <textarea  name="destination" placeholder="สถานที่ไปราชการ" style="width: 232mm; height: 30mm;"></textarea>

                    <br>
                    <label for="travelerCounts">จำนวนผู้เดินทางไปราชการจำนวน <input type="text"  name="travelerCount" contenteditable="true" placeholder="......" style="width: 10mm;" > คน ดังนี้</label>
                    <br>
                    <textarea name="travelers" placeholder="สมาชิก" style="width: 232mm; height: 30mm;"></textarea>

                </form>
            </div>
        </div>
        <br>
    </content2>
    <button type="button" onclick="submitForm()">ส่งแบบฟอร์ม</button>

    <script>  

        function updateForms() {
            var loadFormCheckbox = document.getElementById('loadFormCheckbox');
            var uploadCheckbox = document.getElementById('uploadCheckbox');
            var uploadDiv = document.getElementById('upload');
            var projectFormDiv = document.getElementById('projectForm');

            if (loadFormCheckbox.checked && uploadCheckbox.checked) {
                // กรณีทั้งสอง Checkbox ถูกติ๊ก
                uploadDiv.classList.remove('hidden');
                projectFormDiv.classList.remove('hidden');
            } else if (loadFormCheckbox.checked) {
                // กรณี Checkbox แรกถูกติ๊ก
                uploadDiv.classList.remove('hidden');
                projectFormDiv.classList.add('hidden');
            } else if (uploadCheckbox.checked) {
                // กรณี Checkbox ที่สองถูกติ๊ก
                uploadDiv.classList.add('hidden');
                projectFormDiv.classList.remove('hidden');
            } else {
                // กรณีทั้งสอง Checkbox ไม่ถูกติ๊ก
                uploadDiv.classList.add('hidden');
                projectFormDiv.classList.add('hidden');
            }
        }

        function uploadFile() {
            var uploadCheckbox = document.getElementById('uploadCheckbox');
            var fileInput = document.getElementById('fileInput');
            var file = fileInput.files[0];

            if (uploadCheckbox.checked && file) {
                alert('อัพโหลดไฟล์สำเร็จ: ' + file.name);
            } else if (uploadCheckbox.checked) {
                alert('ไม่มีไฟล์ที่ถูกเลือก');
            }
        }

        function updateFormsCar() {
            var loadFormCheckboxCar = document.getElementById('loadFormCheckboxCar');
            var content2Div = document.getElementById('content2');

            if (loadFormCheckboxCar.checked) {
                content2Div.classList.remove('hidden');
            } else {
                content2Div.classList.add('hidden');
            }
        }

        $(function () {
                $("#startDate, #endDate").datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    language: 'th',
                    defaultViewDate: { year: new Date().getFullYear(), month: new Date().getMonth(), day: new Date().getDate() },
                }).on('changeDate', function (e) {
                    var startDate = $("#startDate").datepicker('getDate');
                    var endDate = $("#endDate").datepicker('getDate');

                    if (startDate !== null && endDate !== null) {
                        var totalDays = Math.floor((endDate - startDate) / (24 * 60 * 60 * 1000)) + 1;
                        $("#totalDays").val(totalDays);
                    } else {
                        $("#totalDays").val("0");
                    }
                });
            });
</script>


</body>
</html>