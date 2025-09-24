<?php
// File khởi tạo database và tables
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Kết nối MySQL server (không chỉ định database)
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Đọc file SQL
    $sql = file_get_contents(__DIR__ . '/init_database.sql');
    
    if ($sql === false) {
        throw new Exception("Không thể đọc file init_database.sql");
    }

    // Tách các câu lệnh SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "<h2>Khởi tạo database...</h2>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "<p style='color: green;'>✓ Thực hiện thành công: " . substr($statement, 0, 50) . "...</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>⚠ Bỏ qua: " . substr($statement, 0, 50) . "... (Có thể đã tồn tại)</p>";
            }
        }
    }

    echo "<h3 style='color: green;'>✓ Khởi tạo database thành công!</h3>";
    echo "<p><strong>Thông tin kết nối:</strong></p>";
    echo "<ul>";
    echo "<li>Host: localhost</li>";
    echo "<li>Database: du_lich</li>";
    echo "<li>Username: root</li>";
    echo "<li>Password: (trống)</li>";
    echo "</ul>";
    
    echo "<p><strong>Tài khoản demo:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: admin@traveldream.vn / password</li>";
    echo "<li>User: user@example.com / password</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</h3>";
    echo "<p>Vui lòng kiểm tra:</p>";
    echo "<ul>";
    echo "<li>XAMPP đã được khởi động</li>";
    echo "<li>MySQL service đang chạy</li>";
    echo "<li>Thông tin kết nối database đúng</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khởi tạo Database - TravelDream</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($statements)): ?>
            <hr>
            <p><a href="../public/index.php" class="btn">Trở về trang chủ</a></p>
        <?php endif; ?>
    </div>
</body>
</html>