<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['number'])) {
        $number = $_POST['number'];

        // SQL query to check if the car registration number already exists
        $query = "SELECT COUNT(*) FROM cars WHERE registration_number = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$number]);
        $count = $stmt->fetchColumn();

        // Return the result
        echo $count > 0 ? 'exists' : 'not_exists';
    } else {
        echo 'invalid_request';
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
