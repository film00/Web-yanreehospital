<?php
$servername = "localhost";
$username = "yanreeho_yanree_db";
$password = "B@4N+209rhMfoT";
$dbname = "yanreeho_yanree_db";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_car = $_POST['id_car'];
        $logo = $_POST['logo'];
        $number = $_POST['number'];
        $status_car = $_POST['status_car']; // Changed variable name

        $stmt = $conn->prepare("UPDATE car SET logo = :logo, number = :number, status_car = :status_car WHERE id_car = :id_car"); // Changed column name
        $stmt->bindParam(':id_car', $id_car);
        $stmt->bindParam(':logo', $logo);
        $stmt->bindParam(':number', $number); 
        $stmt->bindParam(':status_car', $status_car); // Changed variable name
        $stmt->execute();

        header("Location: datacar.php");
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
