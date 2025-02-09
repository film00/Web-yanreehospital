<?php
require_once('./TCPDF-main/tcpdf.php');

// Database connection
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get id_project from URL
$id_project = $_GET['id_project'];

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

// Set document information
$pdf->SetCreator('Mindphp');
$pdf->SetAuthor('Mindphp Developer');
$pdf->SetTitle('Mindphp Example 02');
$pdf->SetSubject('Mindphp Example');
$pdf->SetKeywords('Mindphp, TCPDF, PDF, example, guide');

// Remove header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins (millimeters)
$pdf->SetMargins(20, 20, 25, true); // Left, Right, Top, Bottom

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20); // 20mm bottom margin

// Set font
$pdf->SetFont('THSarabun', '', 16);

// Add a page
$pdf->AddPage();

// SQL query to fetch data for specific id_project and user rank
$sql = "SELECT project.*, users.rank 
        FROM project 
        LEFT JOIN users ON project.id_us_pro = users.id_us
        WHERE project.id_project = $id_project";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    $id_project = $row['id_project'];
    $name_project = $row['name_project'];
    $name_us = $row['name_us'];
    $reason_and_reason = $row['reason_and_reason'];
    $objective = $row['objective'];
    $Target_group = $row['Target_group'];
    $Processing_time = $row['Processing_time'];
    $Budget = $row['Budget'];
    $Evaluation = $row['Evaluation'];
    $status_pro = $row['status_pro'];
    $rank = $row['rank']; // Fetching user rank from users table
    $prefix_boss_pro = $row['prefix_boss_pro'];
    $name_boss_pro = $row['name_boss_pro'];
    $rank_boss_pro = $row['rank_boss_pro'];

    // Create HTML content for left part including project name
    $left_html = "
        <div style=\"text-align: center;\">
            <b style=\"display: block;\">$name_project</b>
        </div>
        <p><b>หลักการและเหตุผล:</b><br> $reason_and_reason</p>
        <p><b>กลุ่มเป้าหมาย:</b><br> $objective</p>
        <p><b>วิธีดำเนินการ:</b><br> $Target_group</p>
        <p><b>ระยะเวลาดำเนินการ:</b><br> $Processing_time</p>
        <p><b>งบประมาณ:</b><br> $Budget</p>
        <p><b>การประเมิน:</b><br> $Evaluation</p>
    ";

    // Output the left HTML content
    $pdf->writeHTML($left_html, true, false, true, false, '');

    // Add a gap of 20mm
    $pdf->Ln(20);

    // Check if enough space is left for the right_html block
    if ($pdf->getY() > ($pdf->getPageHeight() - $pdf->getBreakMargin() - 50)) { // If not enough space for 50mm height block
        $pdf->AddPage();
    }

    // Create HTML content for right part using table
    $right_html = "
        <table width=\"100%\">
            <tr>
                <td width=\"50%\"></td>
                <td width=\"50%\" style=\"text-align: right;\">
                    <div style=\"text-align: center;\">
                        <span>ลงชื่อ ...$name_us... ผู้เขียนโครงการ</span><br>
                        <span>($name_us)</span><br>
                        <span>($rank)</span>
                    </div>
                </td>
            </tr>
        </table>
    ";

    // Output the right HTML content
    $pdf->writeHTML($right_html, true, false, true, false, '');

    // Add a gap of 20mm
    $pdf->Ln(20);

    // Check if enough space is left for the right2_html block
    if ($pdf->getY() > ($pdf->getPageHeight() - $pdf->getBreakMargin() - 50)) { // If not enough space for 50mm height block
        $pdf->AddPage();
    }

    // Create HTML content for the second right part using table
    $right2_html = "
        <table width=\"100%\">
            <tr>
                <td width=\"50%\"></td>
                <td width=\"50%\" style=\"text-align: right;\">
                    <div style=\"text-align: center;\">
                        <span>ลงชื่อ ...$name_boss_pro... ผู้อนุมัติโครงการ</span><br>
                        <span>($prefix_boss_pro$name_boss_pro)</span><br>
                        <span>($rank_boss_pro)</span>
                    </div>
                </td>
            </tr>
        </table>
    ";

    // Output the second right HTML content
    $pdf->writeHTML($right2_html, true, false, true, false, '');

} else {
    $pdf->writeHTML('<p>No data found</p>', true, false, true, false, '');
}

// Close and output PDF document
$pdf->Output('mindphp02.pdf', 'I');

// Close database connection
$conn->close();
?>
