<?php
echo "<h1>XAMPP MySQL Connection Test</h1>";
echo "<hr>";

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "du_lich";
$port = 3306;

echo "<h2>1. Testing MySQL Connection...</h2>";

// Test MySQL connection
try {
    $conn = new mysqli($servername, $username, $password, "", $port);
    if ($conn->connect_error) {
        throw new Exception("MySQL connection failed: " . $conn->connect_error);
    }
    echo "<p style='color: green;'>✓ MySQL server connection successful!</p>";
    
    // Check MySQL version
    $version = $conn->server_info;
    echo "<p>MySQL Version: <strong>$version</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ " . $e->getMessage() . "</p>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Start XAMPP Control Panel</li>";
    echo "<li>Click 'Start' next to MySQL</li>";
    echo "<li>Make sure port 3306 is not blocked</li>";
    echo "</ul>";
    exit;
}

echo "<h2>2. Checking Database '$dbname'...</h2>";

// Check if database exists
$db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");

if ($db_check->num_rows == 0) {
    echo "<p style='color: orange;'>⚠ Database '$dbname' not found. Creating...</p>";
    
    // Create database
    $create_db_sql = "CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($create_db_sql) === TRUE) {
        echo "<p style='color: green;'>✓ Database '$dbname' created successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p>";
        exit;
    }
} else {
    echo "<p style='color: green;'>✓ Database '$dbname' already exists!</p>";
}

// Connect to the specific database
$conn->select_db($dbname);

echo "<h2>3. Checking Tables...</h2>";

// Check for tours table
$table_check = $conn->query("SHOW TABLES LIKE 'tours'");
if ($table_check->num_rows == 0) {
    echo "<p style='color: orange;'>⚠ Table 'tours' not found. Creating...</p>";
    
    // Create tours table
    $create_tours_sql = "
    CREATE TABLE `tours` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(200) NOT NULL,
        `description` TEXT,
        `price` DECIMAL(10,2) NOT NULL,
        `start_date` DATE,
        `end_date` DATE,
        `duration` VARCHAR(50),
        `destination` VARCHAR(200),
        `image_url` VARCHAR(255),
        `category` ENUM('domestic', 'international') DEFAULT 'domestic',
        `max_participants` INT DEFAULT 10,
        `total_bookings` INT DEFAULT 0,
        `rating` DECIMAL(3,1) DEFAULT 0.0,
        `status` ENUM('active', 'inactive') DEFAULT 'active',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($create_tours_sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'tours' created successfully!</p>";
        
        // Insert sample data
        $sample_tours = "
        INSERT INTO `tours` (`name`, `description`, `price`, `start_date`, `end_date`, `duration`, `destination`, `image_url`, `category`, `max_participants`, `total_bookings`, `rating`, `status`) VALUES
        ('Tour Hạ Long 2N1Đ - Vịnh Kỳ Quan Thế Giới', 'Khám phá vẻ đẹp tuyệt vời của Vịnh Hạ Long với những hang động kỳ thú và cảnh quan hùng vĩ.', 2500000.00, '2025-01-15', '2025-01-16', '2 ngày 1 đêm', 'Vịnh Hạ Long, Quảng Ninh', 'https://images.unsplash.com/photo-1528127269322-539801943592?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'domestic', 20, 15, 4.8, 'active'),
        ('Tour Sapa 3N2Đ - Chinh Phục Fansipan', 'Khám phá vùng núi Sapa hùng vĩ, chinh phục đỉnh Fansipan và trải nghiệm văn hóa các dân tộc thiểu số.', 3200000.00, '2025-02-01', '2025-02-03', '3 ngày 2 đêm', 'Sapa, Lào Cai', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'domestic', 15, 12, 4.7, 'active'),
        ('Tour Phú Quốc 4N3Đ - Đảo Ngọc Trai', 'Tận hưởng kỳ nghỉ tuyệt vời tại đảo Phú Quốc với bãi biển cát trắng, nước biển trong xanh.', 4800000.00, '2025-01-20', '2025-01-23', '4 ngày 3 đêm', 'Phú Quốc, Kiên Giang', 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'domestic', 25, 20, 4.9, 'active'),
        ('Tour Thái Lan 5N4Đ - Bangkok Pattaya', 'Khám phá xứ sở chùa vàng với Bangkok sầm uất và Pattaya náo nhiệt.', 8900000.00, '2025-02-10', '2025-02-14', '5 ngày 4 đêm', 'Bangkok - Pattaya, Thái Lan', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'international', 22, 18, 4.8, 'active')";
        
        if ($conn->query($sample_tours) === TRUE) {
            echo "<p style='color: green;'>✓ Sample tour data inserted!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Error creating tours table: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Table 'tours' exists!</p>";
    
    // Count tours
    $count_result = $conn->query("SELECT COUNT(*) as count FROM tours");
    $count = $count_result->fetch_assoc()['count'];
    echo "<p>Number of tours: <strong>$count</strong></p>";
}

// Check for users table
$users_check = $conn->query("SHOW TABLES LIKE 'users'");
if ($users_check->num_rows == 0) {
    echo "<p style='color: orange;'>⚠ Table 'users' not found. Creating...</p>";
    
    $create_users_sql = "
    CREATE TABLE `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20),
        `address` VARCHAR(255),
        `role` ENUM('user','admin') DEFAULT 'user',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($create_users_sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table 'users' created successfully!</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Table 'users' exists!</p>";
}

echo "<h2>4. Connection Test Complete!</h2>";
echo "<p style='color: green; font-size: 18px;'><strong>✓ Database setup successful!</strong></p>";

echo "<p><strong>Database Information:</strong></p>";
echo "<ul>";
echo "<li>Server: $servername:$port</li>";
echo "<li>Database: $dbname</li>";
echo "<li>Username: $username</li>";
echo "<li>Character Set: utf8mb4</li>";
echo "</ul>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='../public/index.php'>Visit Website</a></li>";
echo "<li><a href='http://localhost/phpmyadmin'>Open phpMyAdmin</a></li>";
echo "</ul>";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database Setup - TravelDream</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 20px auto; 
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 { color: #333; }
        ul { margin: 10px 0; }
        a { 
            color: #007bff; 
            text-decoration: none;
            padding: 5px 10px;
            background: #e7f3ff;
            border-radius: 3px;
            margin-right: 10px;
        }
        a:hover { background: #cce7ff; }
        hr { margin: 20px 0; border: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content generated by PHP above -->
    </div>
</body>
</html>