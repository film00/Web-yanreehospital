<?php
session_start();

$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


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

    // ดึงข้อมูลทะเบียนรถจากฐานข้อมูลเฉพาะรถที่มี status_car = 'ว่าง'
    $stmt = $conn->query("SELECT number, status_car FROM car WHERE status_car = 'ว่าง'");

    // ตรวจสอบว่า $stmt มีข้อมูลหรือไม่
    if ($stmt->rowCount() > 0) {
        $carNumbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $carNumbers = [];
    }

    // ดึงข้อมูลพนักงานขับรถ
    $stmt = $conn->query("SELECT id_us, prefix, name, last_name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบขออนุญาตเดินทางไปราชการ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding-top: 20px;
        }
        h3 {
            text-align: center;
            color: #333;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300mm;
            margin: auto;
        }
        .header-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-control {
            box-shadow: none;
            border-radius: 4px;
        }
        .br, .label, .textarea {
            display: block;
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
    </style>
</head>
<body>

<div class="container" >
    <form id="carForm" action="submit_use_car.php" method="post" style="width: 210mm; margin: auto; text-align: left; margin-left: 12mm;" >

                <h3 class="head" >ใบขออนุญาตเดินทางไปราชการ</h3><br>
                <h3 class="head" >หน่วยงาน โรงพยาบาลส่งเสริมสุขภาพตำบลย่านรี</h3>
 
                <br><br><br>
                <label class="position" for="travelInstructions"><br>คำชี้แจงการเดินทางไปราชการ<br></label>
 
                <br>
                <label class="applicant-label" for="applicant" style="margin-left: 100px;">ข้าพเจ้า</label>
                <input type="text" class="applicant" name="applicant" placeholder="<?php echo isset($user['prefix']) ? $user['prefix'] : ''; ?><?php echo isset($user['name']) ? $user['name'] . ' ' : 'กรุณาเข้าสู่ระบบ'; ?><?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>" style="width: 300px; margin-left: 10px;" >
 
                <label for="position" style="margin-left: 10px;">ตำแหน่ง</label>
                <input type="text" id="position" name="position" placeholder="<?php echo isset($user['rank']) ? $user['rank'] : ''; ?>"style="width: 200px; margin-left: 10px;"><br>

                <label for="go_to" style="margin-left: 100px; margin-top: 20px;">ขออนุมัติไปราชการ</label>
                <select id="go" name="go" style="margin-top: 20px; height: 30px; margin-left: 10px;">
                    <option value="ไปราชการในจังหวัด">ไปราชการในจังหวัด</option> 
                    <option value="ไปราชการนอกจังหวัด">ไปราชการนอกจังหวัด</option>
                </select>

                <label for="expenses" style="margin-left: 10px;"><br>ขอเบิกค่าใช้จ่ายจาก</label>
                <select id="about_costs" name="about" style="margin-top: 20px; height: 30px; margin-left: 10px;">
                    <option value="เบิกงบผู้จัด">เบิกงบผู้จัด</option>
                    <option value="เบิกงบกลาง">เบิกงบกลาง</option>
                    <option value="สลจ. ตาก">สลจ. ตาก</option>                        
                    <option value="เบิกเงินบำรุง">เบิกเงินบำรุง</option>
                    <option value="ไม่ขอเบิก">ไม่ขอเบิก</option>
                </select>

                <br>
                <div class="d-flex flex-row align-items-center" style="margin-top: 20px; height: 30px;">
                    <label for="startDate" >ตั้งแต่วันที่1:</label>
                    <input type="text" class="form-control mr-2" id="startDate" name="startDate" placeholder="กรุณาเลือกวันที่" style="width: 160px; margin-left: 5px;">

                    <label for="endDate" class="mr-2">ถึงวันที่:</label>
                    <input type="text" class="form-control mr-2" id="endDate" name="endDate" placeholder="กรุณาเลือกวันที่" style="width: 160px; margin-left: 5px;">

                    <label for="totalDays" class="mr-2">รวม:</label>
                    <input type="text" id="totalDays" name="totalDays" class="form-control" placeholder="0" readonly style="width: 80px; margin-left: 5px;">
                </div>
                
                <br>
                <label for="transportation">โดยยานพาหนะ</label>
                <div id="approval-options" style="margin-left: 35mm;">
                    <label><input type="checkbox" name="approval[]" value="รถประจำทาง"> รถประจำทาง</label>
                    <label style="margin-left: 17mm;"><input type="checkbox" name="approval[]" value="รถรับจ้าง"> รถรับจ้าง</label>
                    <label style="margin-left: 17mm;"><input type="checkbox" name="approval[]" value="รถส่วนตัว"> รถส่วนตัว</label><br>
                    <label><input type="checkbox" id="official-car" name="approval[]" value="รถราชการ"> รถราชการ</label>
                    <label style="margin-left: 22mm;"><input type="checkbox" name="approval[]" value="เครื่องบิน"> เครื่องบิน</label>
                </div>
                <br>
                <div class="form-inline">
                    <label for="number" style="display: inline-block; margin-left: 0px;">ทะเบียน
                        <select id="number" name="number" style="margin-left: 5px; height: 30px;">
                            <option value="">-- เลือกทะเบียนรถ --</option>
                            <?php
                            if (!empty($carNumbers)) {
                                foreach ($carNumbers as $car) {
                                    echo "<option value='" . htmlspecialchars($car['number']) . "'>" . htmlspecialchars($car['number']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>ไม่มีข้อมูล</option>";
                            }
                            ?>
                            <option value="other">ระบุ</option>
                            <option value="none">ไม่ระบุ</option>
                        </select>
                    </label>

                    <label for="driver" style="margin-left: 10px;">พนักงานขับรถ
                        <select id="driver" name="driver" style="margin-left: 5px; height: 30px;">
                            <option value="">-- เลือกพนักงานขับรถ --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo htmlspecialchars($user['id_us']); ?>">
                                    <?php echo htmlspecialchars($user['prefix'] . ' ' . $user['name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="other">ระบุ</option>
                            <option value="none">ไม่ระบุ</option>
                        </select>
                    </label>
                </div>
                <br>
                <input type="radio" id="fuel" name="fuel" value="fuel" style="margin-left: 35mm;">
                <label for="fuel">ขอเบิกค่าน้ำมันเชื้อเพลิง</label>

                <input type="radio" id="noFuel" name="fuel" value="noFuel" style="margin-left: 35mm;">
                <label for="noFuel">ไม่ขอเบิกค่าน้ำมันเชื้อเพลิง</label>
                
                <br>
                <input type="radio" id="stayOvernight" name="overnightStay" value="stayOvernight" style="margin-left: 35mm;">
                <label for="stayOvernight">พักค้างคืน</label>

                <input type="radio" id="noStayOvernight" name="overnightStay" value="noStayOvernight" style="margin-left: 59.5mm;">
                <label for="noStayOvernight">ไม่พักค้างคืน</label>

                <br>
                <label for="purposes">เรื่อง/งานที่ไปราชการ</label>
                <br>
                <textarea name="purpose" placeholder="เรื่อง/งานที่ไปราชการ" style="width: 170mm; height: 30mm; display: block; margin: 0 auto;"></textarea>

                <br>
                <label for="destinations">สถานที่ไปราชการ</label>
                <br>
                <textarea  name="destination" placeholder="สถานที่ไปราชการ" style="width: 170mm; height: 30mm; display: block; margin: 0 auto;"></textarea>

                <br>
                <label for="travelerCounts">
                จำนวนผู้เดินทางไปราชการจำนวน 
                <input type="text" id="travelerCount" name="travelerCount" placeholder="......" style="width: 10mm;">
                คน ดังนี้
                </label>
                <br>
                <textarea id="travelers" name="travelers" placeholder="สมาชิก" style="width: 170mm; height: 30mm; display: block; margin: 0 auto;"></textarea>
                <div class="submit" style="margin-top: 50px;">
                    <button type="submit" class="btn btn-primary"style="justify-content: center;">ส่งแบบฟอร์ม</button>
                </div>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

<script>
    // ปิดการใช้งาน checkbox อื่น ๆ เมื่อเลือก "รถราชการ"
    document.getElementById('official-car').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#approval-options input[type="checkbox"]');
        
        if (this.checked) {
            checkboxes.forEach(checkbox => {
                if (checkbox !== this) {
                    checkbox.disabled = true; // ปิดการใช้งาน checkbox อื่น ๆ
                }
            });
        } else {
            checkboxes.forEach(checkbox => {
                checkbox.disabled = false; // เปิดการใช้งาน checkbox อื่น ๆ เมื่อยกเลิกการเลือก "รถราชการ"
            });
        }
    });

    // ฟังก์ชันที่ใช้ในการแสดง input
    document.getElementById('number').addEventListener('change', function() {
        var select = this;
        var input = document.getElementById('custom_number');
        var selectedValue = select.value;

        // หาค่าของ status_car ที่ตรงกับ selected value
        var matchingCar = carNumbers.find(function(car) {
            return car.number === selectedValue;
        });

        if (matchingCar && matchingCar.status_car === "ว่าง" && selectedValue === 'other') {
            // ถ้าค่าของ status_car เป็น "ว่าง" และเลือก 'other'
            if (!input) {
                input = document.createElement('input');
                input.type = 'text';
                input.name = 'custom_number';
                input.id = 'custom_number';
                input.placeholder = 'กรอกทะเบียนใหม่';
                input.style.marginLeft = '10px';
                select.parentNode.appendChild(input);
            }
            input.style.display = 'inline';
            select.style.display = 'none';
        } else {
            // ถ้าค่าไม่ตรงกับที่กำหนด หรือไม่มีรถที่ตรงกัน
            if (input) {
                input.remove();
                select.style.display = 'inline-block';
            }
        }
    });

    // แสดง input สำหรับกรอกชื่อพนักงานขับรถใหม่
    document.getElementById('driver').addEventListener('change', function() {
        var select = this;
        var input = document.getElementById('custom_driver');

        if (select.value === 'other') {
            if (!input) {
                input = document.createElement('input');
                input.type = 'text';
                input.name = 'custom_driver';
                input.id = 'custom_driver';
                input.placeholder = 'กรอกชื่อพนักงานขับรถใหม่';
                input.style.marginLeft = '10px';
                select.parentNode.appendChild(input);
            }
            input.style.display = 'inline';
            select.style.display = 'none';
        } else {
            if (input) {
                input.remove();
                select.style.display = 'inline-block';
            }
        }
    });

    // ตรวจสอบการเปลี่ยนแปลงใน checkbox สำหรับ "รถประจำทาง", "รถรับจ้าง", "เครื่องบิน"
    document.querySelectorAll('input[name="approval[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            var numberDropdown = document.getElementById('number');
            var driverDropdown = document.getElementById('driver');

            // ถ้าหาก "รถประจำทาง", "รถรับจ้าง", หรือ "เครื่องบิน" ถูกเลือก
            if (checkbox.checked && (checkbox.value === 'รถประจำทาง' || checkbox.value === 'รถรับจ้าง' || checkbox.value === 'เครื่องบิน')) {
                // ตั้งค่า dropdown ทะเบียนและพนักงานขับรถเป็น "none"
                numberDropdown.value = 'none';
                driverDropdown.value = 'none';

                // ปิดการใช้งาน dropdown ทะเบียนและพนักงานขับรถ
                numberDropdown.disabled = true;
                driverDropdown.disabled = true;
            } else {
                // ถ้าหากไม่มี checkbox อื่นที่ถูกเลือก
                let anyChecked = Array.from(document.querySelectorAll('input[name="approval[]"]')).some(checkbox => checkbox.checked);
                if (!anyChecked) {
                    // เปิดการใช้งาน dropdown และแสดงค่าเริ่มต้น
                    numberDropdown.disabled = false;
                    driverDropdown.disabled = false;
                    numberDropdown.value = '';
                    driverDropdown.value = '';
                }
            }
        });
    });


    $(document).ready(function() {
        document.getElementById('travelerCount').addEventListener('input', function() {
            var count = parseInt(this.value) || 0; // Get the count from input
            var textarea = document.getElementById('travelers');
            var list = '';

            // Generate the list with numbers ๑, ๒, ๓, ..., based on the count
            for (var i = 1; i <= count; i++) {
                list += (i + '. ') + '\n';
            }

            // Set the generated list into the textarea
            textarea.value = list;
            });

            $(document).ready(function(){
                $('#startDate, #endDate').datepicker({
                    format: 'dd/mm/yyyy',
                    language: 'th', // ใช้ภาษาไทย
                    todayHighlight: true,
                    autoclose: true,
                    startDate: new Date()
                }).on('changeDate', function(e) {
                    var startDate = $('#startDate').datepicker('getDate');
                    var endDate = $('#endDate').datepicker('getDate');

                    if (startDate) {
                        $('#endDate').datepicker('setStartDate', startDate);
                    }

                    if (endDate) {
                        $('#startDate').datepicker('setEndDate', endDate);
                    } 

                    calculateDays();
                });

                function calculateDays() {
                    var startDate = $("#startDate").datepicker('getDate');
                    var endDate = $("#endDate").datepicker('getDate');

                    if (startDate && endDate) {
                        var totalDays = Math.floor((endDate - startDate) / (24 * 60 * 60 * 1000)) + 1;
                        $("#totalDays").val(totalDays);
                    } else {
                        $("#totalDays").val("0");
                    }
                }
            });

            document.getElementById('number').addEventListener('change', function() {
                var selectedValue = this.value;
            console.log("Selected vehicle registration: " + selectedValue);
        });

        // Handle form submission
        $(document).ready(function() {
    $('#carForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Collect data
        var formData = {
            applicant: $('input[name="applicant"]').val(),
            position: $('input[name="position"]').val(),
            go: $('select[name="go"]').val(),
            about: $('select[name="about"]').val(),
            startDate: $('input[name="startDate"]').val(),
            endDate: $('input[name="endDate"]').val(),
            totalDays: $('input[name="totalDays"]').val(),
            approval: $('input[name="approval[]"]:checked').map(function() {
                return $(this).val();
            }).get(), // ดึงค่าที่เลือกทั้งหมดใน approval[]
            number: $('select[name="number"]').val(),
            driver: $('select[name="driver"]').val(),
            fuel: $('input[name="fuel"]:checked').val(),
            overnightStay: $('input[name="overnightStay"]:checked').val(),
            purpose: $('textarea[name="purpose"]').val(),
            destination: $('textarea[name="destination"]').val(),
            travelerCount: $('input[name="travelerCount"]').val(),
            travelers: $('textarea[name="travelers"]').val()
        };

        // Mapping field names to display messages
        var fieldNames = {
            startDate: 'วันที่',
            endDate: 'วันที่',
            approval: 'ยานพาหนะ',
            number: 'ทะเบียนรถ',
            driver: 'พนักงานขับรถ',
            fuel: 'ค่าน้ำมันเชื้อเพลิง',
            overnightStay: 'การค้างคืน',
            purpose: 'เรื่อง/งานที่ไปราชการ', // Corrected key
            destination: 'สถานที่ไปราชการ' // Corrected key
        };

        // List of required fields to check
        var requiredFields = ['startDate', 'endDate', 'approval', 'number', 'driver', 'fuel', 'overnightStay', 'purpose', 'destination'];

        // Check only the required fields
        for (var i = 0; i < requiredFields.length; i++) {
            var key = requiredFields[i];
            if (!formData[key]) {
                var fieldName = fieldNames[key]; // Get the custom message for the field
                Swal.fire({
                    icon: 'warning',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณากรอกข้อมูลในช่อง: ' + fieldName,
                    confirmButtonText: 'ตกลง'
                });
                return; // Stop execution if a required field is empty
            }
        }

        // If all required fields are filled, submit the form
        Swal.fire({
            title: 'สำเร็จ!',
            text: 'ข้อมูลได้ถูกส่งเรียบร้อยแล้ว',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            $('#carForm')[0].submit(); // Submit the form after validation
        });
    });
}); 
});

</script>

</body>
</html>