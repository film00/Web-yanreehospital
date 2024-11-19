<?php
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
 
$data = json_decode(file_get_contents('php://input'), true);
$number = $data['number'];

$sql = "SELECT COUNT(*) as count FROM car WHERE number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $number);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$response = array('exists' => $row['count'] > 0);

echo json_encode($response);

$conn->close();
?>
