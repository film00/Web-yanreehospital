<?php
session_start();

$servername = "**********";
$username = "**********";
$password = "**********";
$dbname = "**********";


// Check if id_us exists in session
if (isset($_SESSION['id_us'])) {
    $id_us = $_SESSION['id_us'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the delete request is sent
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
            $deleteUserId = $_POST['delete_user_id'];

            // Delete user from the database
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE id_us = :id_us");
            $deleteStmt->bindParam(':id_us', $deleteUserId, PDO::PARAM_INT);
            $deleteStmt->execute();
        }

        // Fetch user data
        $userStmt = $conn->prepare("SELECT * FROM users WHERE id_us = :id_us");
        $userStmt->bindParam(':id_us', $id_us, PDO::PARAM_INT);
        $userStmt->execute();
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: index.php");
            exit();
        }

        // Fetch all users for the table
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}

$conn = null;

// Include the nav and footer files after the header function calls
require_once "./nav/navbar_addmin.php";
require_once "./footer/footer.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
   
    <style>
        body {
            font-family: "Prompt", sans-serif;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
            background-image: url('../background.jpg'); 
            background-size: cover; /* ขยายรูปภาพให้ครอบคลุมพื้นที่ทั้งหมด */
            margin-bottom: 3rem;
        }

        h2 {
            font-family: "Prompt", sans-serif;
        }

        .prompt-bold {
            font-family: "Prompt", sans-serif;
            font-weight: 700;
            font-style: normal;
        } 

        content {
            padding: 20px;
            font-family: "Prompt", sans-serif;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: right;
        }

        .us {
            font-size: 30px;
            margin-top: 20px;
            text-align: center;
        }

        .grid {
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        table {
            width: 100%;
            border-collapse: collapse;
            
        }

        td{
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            white-space: nowrap;
           
        }
        th {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            background-color: #191970;
            color: white;
            white-space: nowrap;
           
        }

        th:last-child,
        td:last-child {
            border-right: none;
        }


        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 35%;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            text-align: center;
        }

        p.modal-text {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p.small-text {
            font-size: 16px;
            margin-top: 10px;
        }
        
        p.smalless-text {
            font-size: 12px;
            margin-top: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        #projectDetails {
            white-space: pre-wrap;
        }

        .h1, .h2, .h3 {
            display: inline-block;
            margin: 5px;
            padding: 0px;
            font-size: 50px;
            margin-top: 10px;
        }

        .h1 {
            color: #000;
        }

        .h2 {
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tr:hover {
            background-color: #e0e0e0; 
        }

        .add-member-button {
            margin-left: auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-align: center;
            display: inline-block;
            margin-left: 10px;
        }

        .add-member-button:hover {
            background-color: #45a049;
        }

        .search-container {
            float: left;
            margin-right: auto;
        }

        input[type="text"] {
            padding: 5px;
            border-radius: 5px;
        }

        .action-link {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            
        }
       

        .action-text {
            position: absolute;
            bottom: -20px;
            transform: translateX(-50%);
            background-color: white;
            color: black;
            font-size: 12px;
            padding: 2px 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, bottom 0.3s;
            border-radius: 10px;
        }

        .action-link:hover .action-text {
            opacity: 1;
            bottom: 35px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .confirm-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-button:hover {
            background-color: #45a049;
        }

        .cancel-button {
            background-color: #f44336; /* สีแดง */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-button:hover {
            background-color: #d32f2f; /* สีแดงเข้มเมื่อเอาเมาส์ไปชี้ */
        }

        th {
            background-color: #191970;
            color: white;
           
        }


        th.width-9 {
            width: 10%; 
        }
                
        .container {
            display: flex; /* ใช้ Flexbox สำหรับจัดตำแหน่งลิงก์ */
            justify-content: space-between; /* จัดตำแหน่งให้มีพื้นที่ระหว่างลิงก์เท่ากัน */
            align-items: center; /* จัดตำแหน่งกลางแนวตั้ง */
            width: 100%; /* ให้คอนเทนเนอร์มีความกว้างเต็มที่ */
        }

        table {
            width: 100%; /* ให้ตารางใช้ความกว้างทั้งหมดของคอนเทนเนอร์ */
            border-collapse: collapse; /* รวมขอบของเซลล์ให้ไม่ทับซ้อน */
        }
    </style>
    
</head>
<body>
    <script src="script.js"></script>
    <content>
        <div style="text-align:center;">
            <h1 class="h1">รายชื่อ</h1><h2 class="h2">สมาชิก</h2>
        </div>
        <div>
            <a class="add-member-button" href="addmember.php">เพิ่มสมาชิก</a>
        </div>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">รูป</th>
                    <th style="width: 15%; text-align: center;">ชื่อผู้ใช้</th>
                    <th style="width: 15%; text-align: center;">รหัสผ่าน</th>
                    <th style="width: 10%; text-align: center;">คำนำหน้า</th>
                    <th style="width: 15%; text-align: center;">ชื่อ</th>
                    <th style="width: 15%; text-align: center;">นามสกุล</th>
                    <th style="width: 15%; text-align: center;">ตำแหน่ง</th>
                    <th style="width: 15%; text-align: center;">หมายเลขโทรศัพท์</th>
                    <th style="width: 10%; text-align: center;">สถานะ</th>
                    <th style="width: 10%; text-align: center;">แก้ไข</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td style="width: 10%;">
                        <div class="profile-picture">
                            <?php if (!empty($user['picture_name'])): ?>
                                <img src="<?php echo './imageprofile/' . $user['picture_name']; ?>" alt="Profile Picture">
                            <?php else: ?>
                                <img src="./image/user.png" alt="Default Profile Picture">
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </div>
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['password']); ?>
                        </div>
                    </td>
                    <td style="width: 10%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 100px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['prefix']); ?>
                        </div> 
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </div>
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['last_name']); ?>
                        </div>
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['rank']); ?>
                        </div>
                    </td>
                    <td style="width: 15%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 150px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['phone_number']); ?>
                        </div>
                    </td>
                    <td style="width: 10%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div style="max-width: 100px; overflow-x: auto;">
                            <?php echo htmlspecialchars($user['status']); ?>
                        </div>
                    </td>
                    <td style="width: 10%;">
                        <div class="container">
                            <a class="action-link" href="edit_user.php?id=<?php echo $user['id_us']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" justify-content="left" width="25" height="25" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16" style="color: black;">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21 4.5 11.5l2.29-.439 6.823-6.823z"></path>
                                    <path fill-rule="evenodd" d="M1 13.5V16h2.5L14.379 5.621l-2-2L1 13.5zm1-.5H1v1h1v-1z"></path>
                                </svg>
                                <span class="action-text">แก้ไข</span>
                            </a>

                            <?php if ($user['status'] !== 'ผู้ดูแลระบบ') : ?>
                            <a class="action-link" href="javascript:void(0);" onclick="confirmDelete('<?php echo $user['id_us']; ?>', '<?php echo htmlspecialchars($user['prefix']); ?> <?php echo htmlspecialchars($user['name']); ?> <?php echo htmlspecialchars($user['last_name']); ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" justify-content="right" width="25" height="25" fill="#000" class="bi bi-trash3" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3v1h-1v10.5c0 .5-.5 1-1 1h-8c-.5 0-1-.5-1-1v-10.5h-1v-1h3v-1h4zM4 3v10h8v-10h-8zm4-2v1h-2v-1h2zm4 1v1h-3v-1h3zm-8 0v1h-3v-1h3zm8 2h-8v8h8v-8z"></path>
                                </svg>
                                <span class="action-text">ลบ</span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
    </content>
     
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <p id="modal-text" class="modal-text">คุณต้องการลบสมาชิกที่มีชื่อว่า :</p>
            <form id="deleteForm" method="POST" action="delete_user.php">
                <input type="hidden" name="delete_user_id" id="deleteUserId">
                <button type="submit" class="confirm-button">ยืนยัน</button>
                <button type="button" class="cancel-button" onclick="closeDeleteModal()">ยกเลิก</button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(userId, userName) {
            document.getElementById('deleteUserId').value = userId;
            console.log("User ID to delete:", userId); // เพิ่มบรรทัดนี้
            document.getElementById('modal-text').textContent = 'คุณต้องการลบสมาชิกที่มีชื่อว่า: ' + userName + ' ใช่หรือไม่?';
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
</body>
</html>
