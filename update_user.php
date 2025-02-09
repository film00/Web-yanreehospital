<?php
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate input
        $id = $_POST['id'];
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $prefix = htmlspecialchars($_POST['prefix']);
        $name = htmlspecialchars($_POST['name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $status = htmlspecialchars($_POST['status']);

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET username = :username, password = :password, prefix = :prefix, name = :name, last_name = :last_name, phone_number = :phone_number, status = :status WHERE id_us = :id");

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':prefix', $prefix);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        // Redirect after successful update
        header("Location: datausers.php");
        exit();
    } else {
        // Redirect if not a POST request
        header("Location: datausers.php");
        exit();
    }
} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
}

$conn = null;
?>
