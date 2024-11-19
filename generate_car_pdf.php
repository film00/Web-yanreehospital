<?php
require_once('./TCPDF-main/tcpdf.php');

// Database connection
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";

 
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure idusecar is set and is an integer
if (isset($_GET['idusecar']) && is_numeric($_GET['idusecar'])) {
    $idusecar = intval($_GET['idusecar']);
} else {
    die('Invalid car request ID');
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

// Set document information
$pdf->SetCreator('Mindphp');
$pdf->SetAuthor('Mindphp Developer');
$pdf->SetTitle('Car Request Details');
$pdf->SetSubject('Car Request Details');
$pdf->SetKeywords('Car, TCPDF, PDF, example');

// Remove header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins (millimeters)
$pdf->SetMargins(20, 20, 25, true); // Left, Right, Top, Bottom

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20); // 20mm bottom margin

// Set font with size 18mm
$pdf->SetFont('THSarabun', '', 18);

// Add a page
$pdf->AddPage();

// SQL query to fetch data for specific idusecar
$sql = "SELECT * FROM use_car WHERE idusecar = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idusecar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $last_name = $row['last_name'];
    $rank = $row['rank'];
    $status_car = $row['status_car'];
    $go_to = $row['go_to'];
    $about_costs = $row['about_costs'];
    $from_date = $row['from_date'];
    $up_date = $row['up_date'];
    $sum_date = $row['sum_date'];
    $carrier = $row['carrier'];
    $id_car = $row['id_car'];
    $driver = $row['driver'];
    $gasoline_cost = $row['gasoline_cost'];
    $rest = $row['rest'];
    $purposes = $row['purposes'];
    $destinations = $row['destinations'];
    $id_project = $row['id_project'];
    $sumfollower = $row['sumfollower'];
    $follower1 = $row['follower1'];
    $update_time = $row['update_time'];
    $id_boss_car = $row['id_boss_car']; // ID ของผู้อนุมัติโครงการ
    $id_us_car = $row['id_us_car']; // ID ของผู้ขออนุมัติ
    $prefix_boss = $row['prefix_boss'];
    $name_boss = $row['name_boss'];
    $rank_boss = $row['rank_boss'];

    // Fetch approver details
    $sql_boss = "SELECT prefix, name, last_name, rank FROM users WHERE id_us = ?";
    $stmt_boss = $conn->prepare($sql_boss);
    $stmt_boss->bind_param("i", $id_boss_car);
    $stmt_boss->execute();
    $result_boss = $stmt_boss->get_result();
    $approver = $result_boss->fetch_assoc();
    
    $prefix = isset($approver['prefix']) ? $approver['prefix'] : '';
    $approver_name = isset($approver['name']) ? $approver['name'] : '';
    $approver_last_name = isset($approver['last_name']) ? $approver['last_name'] : '';
    $approver_rank = isset($approver['rank']) ? $approver['rank'] : '';

    // Fetch user details for id_us_car
    $sql_us = "SELECT prefix FROM users WHERE id_us = ?";
    $stmt_us = $conn->prepare($sql_us);
    $stmt_us->bind_param("i", $id_us_car);
    $stmt_us->execute();
    $result_us = $stmt_us->get_result();
    $approverus = $result_us->fetch_assoc();

    $prefixus = isset($approverus['prefix']) ? $approverus['prefix'] : '';

    // Create HTML content for the PDF
    $content = <<<EOD
<div style="text-align: center; margin-bottom: 10px;">
    ใบขออนุญาเดินทางไปราชการ<br>
    หน่วยงาน โรงพยาบาลส่งเสริมสุขภาพตำบลย่านรี
</div>
<p>คำชี้แจงการเดินทางไปราชการ</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ข้าพเจ้า .......$name....$last_name....... ตำแหน่ง .......$rank.......</p>
<p><b>ขอไปอนุมัติราชการ</b> ........$go_to........  <b>ขอเบิกค่าใช้จ่ายจาก</b> ........$about_costs........</p>
<p><b>ไปราชการตั้งต่วันที่</b> ........$from_date........ <b>ถึงวันที่</b> ........$up_date........ <b>รวม</b> ........$sum_date........ วัน</p>
<p><b>โดยยานพาหนะ</b> ...$carrier... <b>ทะเบียน</b> ...$id_car... <b>พนักงานขับรถ:</b> ....$driver....</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $gasoline_cost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  $rest</p>
<p><b>เรื่อง/งานที่ไปราชการ</b> ...$purposes...</p>
<p><b>สถานที่ไปราชการ</b> ...$destinations...  จำนวนผู้เดินทางไปราชการ  จำนวน ...$sumfollower... คน ดังนี้</p>
<p>$follower1</p>
EOD;

    // Output the content
    $pdf->writeHTML($content, true, false, true, false, '');

    // Check if there is enough space for the signatures, otherwise add a new page
    $signature_height = 60; // Height needed for the signatures
    if ($pdf->getY() + $signature_height > $pdf->getPageHeight() - 20) { // 20mm bottom margin
        $pdf->AddPage();
    }

    // Signature block
    $signature_html = <<<EOD
<table width="100%">
    <tr>
        <td width="50%"></td>
        <td width="50%" style="text-align: right;">
            <div style="text-align: center; display: inline-block;">
                <span>ลงชื่อ ...$name $last_name... ผู้ขออนุมัติ</span><br>
                <span>($prefixus $name $last_name)</span><br>
                <span>$rank</span><br>
            </div>
        </td>
    </tr>
</table>
<table width="100%">
    <tr>
        <td width="50%"></td>
        <td width="50%" style="text-align: right;">
            <div style="text-align: center; display: inline-block;">
                <span>ลงชื่อ ...$name_boss... ผู้อนุมัติ</span><br>
                <span>($prefix_boss $name_boss)</span><br>
                <span>$rank_boss</span><br>
            </div>
        </td>
    </tr>
</table>
EOD;

    $pdf->writeHTML($signature_html, true, false, true, false, '');

} else {
    $pdf->writeHTML('<p>ไม่พบข้อมูลการใช้รถ</p>', true, false, true, false, '');
}

// Close and output PDF document
$pdf->Output('car_request_details.pdf', 'I');

// Close the database connection
$conn->close();
?>
