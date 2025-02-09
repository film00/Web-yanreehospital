<?php
$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];  // บันทึกรหัสผ่านที่กรอกโดยตรง
        $prefix = $_POST['prefix'];
        $name = $_POST['name'];
        $last_name = $_POST['last_name'];
        $rank = $_POST['rank'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $status = $_POST['status'];
        $profile_picture = null;
        $picture_name = null;

        // Handle file upload
        if ($_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
            $check = getimagesize($_FILES["userImage"]["tmp_name"]);
            if ($check !== false) {
                // Move uploaded file to desired directory
                $target_dir = "imageprofile/";
                $file_name = basename($_FILES["userImage"]["name"]);
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $target_file)) {
                    $picture_name = $file_name; // Store the file name
                    $profile_picture = $target_file; // Optionally store the full path
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    exit;
                }
            } else {
                echo "File is not an image.";
                exit;
            }
        }

        // Prepare SQL statement to insert user data into database
        $stmt = $conn->prepare("INSERT INTO users (prefix, username, password, name, last_name, rank, phone_number, email, status, picture_name, profile_picture) VALUES (:prefix, :username, :password, :name, :last_name, :rank, :phone_number, :email, :status, :picture_name, :profile_picture)");

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);  // บันทึกรหัสผ่านที่กรอกโดยตรง
        $stmt->bindParam(':prefix', $prefix);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':rank', $rank);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':picture_name', $picture_name);
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->execute();

        // Redirect to datausers.php after successful insertion
        header("Location: datausers.php");
        exit();
    } else {
        // Redirect back if accessed without a POST request
        header("Location: addmember.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
