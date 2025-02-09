<?php
session_start();

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['id_us'])) {
        $id_us = $_SESSION['id_us'];

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $stmt->bindParam(':id_us', $id_us);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: index.php");
        exit();
    }

    // ดึงข้อมูลทะเบียนรถจากฐานข้อมูล
    $stmt = $conn->query("SELECT number FROM car");
    
    // ตรวจสอบว่า $stmt มีข้อมูลหรือไม่
    if ($stmt->rowCount() > 0) {
        $carNumbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $carNumbers = [];
    }

    // ดึงข้อมูลพนักงานขับรถ
    $stmt = $conn->query("SELECT id_us, rank, prefix, name, last_name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>


<style>
        body {
            background-color: #f5f5f5;
            font-family: "Prompt", sans-serif;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300mm;
            margin: auto;
        }
        .containercar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300mm;
            margin: auto;
            display: none;
        }
        .form-group, .form-inline {
            margin-bottom: 1rem;
        }
        .form-control {
            box-shadow: none;
            border-radius: 4px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            margin-left: 100px;
        }
        textarea {
            display: block;
            margin: 0 auto;
        }
        textarea#projectReason, textarea#projectObjective, textarea#projectTargetGroup, textarea#projectBudget, textarea#projectEvaluation, textarea[name="purpose"], textarea[name="destination"], textarea[name="travelers"] {
            width: 170mm;
            height: 30mm;
        }
        textarea#name_project {
            width: 170mm;
            height: 30mm;
            text-align: center;
        }
        .b1 {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .b1:hover {
            background-color: #117a8b;
        }
        .form-check-button {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .form-check-button button {
            background-color: #8470FF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-check-button button:hover {
            background-color: #483D8B;
        }

        h3 {
            text-align: center;
            color: #333;
        }

        .submit {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .submit button {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit button:hover {
            background-color: #117a8b;
        }

        
        .custom-alert-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .custom-alert-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            text-align: center;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }


</style>

</head>
<body>

    <div class="container">
        <form id="projectForm" action="submit_project.php" method="post">
                <textarea id="name_project" name="name_project" style="width: 170mm; height: 15mm;" placeholder="ชื่อโครงการ" required></textarea><br>
                
                <label for="projectReason">1.หลักการและเหตุผล</label><br>
                <textarea id="projectReason" name="reason_and_reason" style="width: 170mm; height: 30mm;" placeholder="กรุณากรอกหลักการและเหตุผล" required></textarea><br>

                <label for="projectObjective">2.วัตถุประสงค์</label><br>
                <textarea id="projectObjective" name="objective" style="width: 170mm; height: 30mm;" placeholder="กรุณากรอกวัตถุประสงค์" required></textarea><br>

                <label for="projectTargetGroup">3.กลุ่มเป้าหมาย</label><br>
                <textarea id="projectTargetGroup" name="target_group" style="width: 170mm; height: 30mm;" placeholder="กรุณากรอกกลุ่มเป้าหมาย" required></textarea><br>

                <div style="display: flex;">
                    <label for="text" style="margin-right: 10px;">4.ระยะเวลาในการดำเนินการ:</label >
                    <label for="startDate" style="margin-right: 2px; margin-left: 10px;">ตั้งแต่:</label>
                    <input type="text" id="startDate" name="start_date" required>
                    <label for="endDate" style="margin-left: 10px;">ถึง:</label>
                    <input type="text" id="endDate" name="end_date" required>
                </div>

                <br>
                <label for="projectBudget">5.งบประมาณ</label><br>
                <textarea id="projectBudget" name="budget" style="width: 170mm; height: 30mm;" placeholder="กรุณากรอกงบประมาณ" required></textarea><br>

                <label for="projectEvaluation">6.การประเมินผล</label><br>
                <textarea id="projectEvaluation" name="evaluation" style="width: 170mm; height: 30mm;" placeholder="กรุณากรอกการประเมินผล" required></textarea>
        </form>
        <div>
            <br>
          

            <label for="projectFile" style="display: inline-block; margin-right: 10px;">เอกสารแนบ(ห้ามเกิน 50 MB)</label>
            <input type="file" id="projectFile" name="project_file" style="display: inline-block; margin-right: 10px;">

            <a type="button" id="clearProjectFile" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16" style="color: black; margin-right: 5px;">
                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                </svg>
            </a>    

        </div>

        
    </div>
    
    <div class="submit">
        <button button type="button" id="submit">ส่งแบบฟอร์ม</button>
    </div>

<script>
    $(document).ready(function() {
    var today = new Date();
    
    // กำหนดรูปแบบปฏิทินและวันที่เริ่มต้น
    $('#startDate, #endDate, #startDatecar, #endDatecar').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'th',
        startDate: today
    });

    // ปรับการตั้งค่าของวันที่สิ้นสุดตามวันที่เริ่มต้น
    $('#startDate').change(function() {
        var startDate = $('#startDate').datepicker('getDate');
        if (startDate !== null) {
            $('#endDate').datepicker('setStartDate', startDate);
        }
    });

    $('#endDate').change(function() {
        var endDate = $('#endDate').datepicker('getDate');
        if (endDate !== null) {
            $('#startDate').datepicker('setEndDate', endDate);
        }
    });

    $('#startDatecar').change(function() {
        var startDatecar = $('#startDatecar').datepicker('getDate');
        if (startDatecar !== null) {
            $('#endDatecar').datepicker('setStartDate', startDatecar);
        }
    });

    $('#endDatecar').change(function() {
        var endDatecar = $('#endDatecar').datepicker('getDate');
        if (endDatecar !== null) {
            $('#startDatecar').datepicker('setEndDate', endDatecar);
        }
    });

    // คำนวณจำนวนวัน
    $('#startDatecar, #endDatecar').change(function() {
        var startDateStr = $('#startDatecar').val();
        var endDateStr = $('#endDatecar').val();

        var startDate = new Date(startDateStr.split("/").reverse().join("-"));
        var endDate = new Date(endDateStr.split("/").reverse().join("-"));

        var timeDiff = Math.abs(endDate.getTime() - startDate.getTime() + 1);
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        $('#totalDays').val(diffDays);
    });

    // สลับฟอร์มรถยนต์
    $('#toggleCarForm').click(function() {
        $('#carFormContainer').toggle();
    });

    // จัดการไฟล์โปรเจกต์
    $('#projectFile').change(function() {
        checkFileSelection(this);
    });

    $('#clearProjectFile').click(function() {
        $('#projectFile').val(''); // ล้างไฟล์ที่เลือก
        $(this).hide();
    });

    function checkFileSelection(input) {
        var maxSize = 50 * 1024 * 1024; // ขนาดไฟล์สูงสุด 50MB

        if (input.files && input.files.length > 0) {
            var fileInput = input.files[0];

            // ตรวจสอบขนาดไฟล์
            if (fileInput.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 50MB',
                    confirmButtonText: 'ตกลง'
                });
                input.value = ''; // เคลียร์ค่าที่เลือกไว้ใน input file
                $('#clearProjectFile').hide(); // ซ่อนปุ่มลบ
                return false; // ป้องกันการส่งฟอร์ม
            } else {
                $('#clearProjectFile').show(); // แสดงปุ่มลบเมื่อมีไฟล์เลือก
            }
        } else {
            $('#clearProjectFile').hide(); // ซ่อนปุ่มลบถ้าไม่มีไฟล์เลือก
        }
    }

    // ส่งข้อมูลฟอร์ม
    $('#submit').click(function(event) {
    event.preventDefault(); // ป้องกันการส่งฟอร์มทันที

    var projectData = {
        name_project: $('#name_project').val(),
        reason_and_reason: $('#projectReason').val(),
        objective: $('#projectObjective').val(),
        target_group: $('#projectTargetGroup').val(),
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        budget: $('#projectBudget').val(),
        evaluation: $('#projectEvaluation').val(),
        project_file: $('#projectFile')[0].files[0] || null // อนุญาตให้ไม่มีไฟล์ได้
    };

    // เช็คว่าทุกฟิลด์ต้องมีค่า ยกเว้น project_file
    for (var key in projectData) {
        if (key !== 'project_file' && (projectData[key] === null || projectData[key] === "" || projectData[key] === undefined)) {
            var fieldName = '';
            switch (key) {
                case 'name_project':
                    fieldName = 'ชื่อโครงการ';
                    break;
                case 'reason_and_reason':
                    fieldName = 'หลักการและเหตุผล';
                    break;
                case 'objective':
                    fieldName = 'วัตถุประสงค์';
                    break;
                case 'target_group':
                    fieldName = 'กลุ่มเป้าหมาย';
                    break;
                case 'start_date':
                case 'end_date':
                    fieldName = 'วันที่ระยะเวลาในการดำเนินการ';
                    break;
                case 'budget':
                    fieldName = 'งบประมาณ';
                    break;
                default:
                    fieldName = key; // กรณีที่ไม่ได้กำหนดค่าเฉพาะไว้
                    break;
            }
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ครบถ้วน',
                text: 'กรุณากรอกข้อมูลในช่อง: ' + fieldName,
                confirmButtonText: 'ตกลง'
            });
            return; // หยุดการทำงานหากพบฟิลด์ที่ว่าง
        }
    }

    // การตรวจสอบประเภทไฟล์
    if (projectData.project_file) {
        var fileInput = $('#projectFile')[0].files[0];
        var maxSize = 50 * 1024 * 1024; // ขนาดไฟล์สูงสุด 50MB

        // ตรวจสอบว่ามีไฟล์เลือกหรือไม่
        if (fileInput) {
            // ตรวจสอบขนาดไฟล์
            if (fileInput.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 50MB',
                    confirmButtonText: 'ตกลง'
                });
                return false; // ป้องกันการส่งฟอร์ม
            }
        }
    }

    var formData = new FormData();
    formData.append('projectData', JSON.stringify(projectData));
    if (projectData.project_file) {
        formData.append('project_file', projectData.project_file);
    }

    $.ajax({
        url: 'submit_data.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'ส่งเอกสารสำเร็จ',
                text: response,
                confirmButtonText: 'ตกลง'
            }).then(function() {
                window.location.href = window.location.href; // รีเฟรชหน้าเว็บเดิม
            });
        },

        error: function(xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการส่งเอกสาร: ' + errorMessage,
                confirmButtonText: 'ตกลง'
            });
        }
    });
});
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        function validateProjectForm() {
            const form = document.getElementById('projectForm');
            const inputs = form.getElementsByTagName('input');
            const textareas = form.getElementsByTagName('textarea');
            let valid = true;
            
            // Check input fields
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].value === '') {
                    valid = false;
                    break;
                }
            }

            // Check textarea fields
            if (valid) {
                for (let i = 0; i < textareas.length; i++) {
                    if (textareas[i].value === '') {
                        valid = false;
                        break;
                    }
                }
            }
            
            if (!valid) {
                alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
            } else {
                form.submit();
            }
        }

        
    </script>

</body>
</html>
