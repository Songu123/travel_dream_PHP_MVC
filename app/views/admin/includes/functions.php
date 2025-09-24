<?php
session_start();

// Kết nối database
function connectDB() {
    $host = '127.0.0.1';
    $dbname = 'du_lich';
    $username = 'root';
    $password = ''; // Để trống nếu không có mật khẩu

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Lỗi kết nối database: " . $e->getMessage());
    }
}

// Kiểm tra đăng nhập admin
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Lấy thông tin admin
function getAdminInfo() {
    $pdo = connectDB();
    $adminId = $_SESSION['admin_id'];
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$adminId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách người dùng
function getUsers() {
    $pdo = connectDB();
    $stmt = $pdo->query("
        SELECT u.id, u.name, u.email, u.phone, u.created_at AS join_date, 
               COUNT(b.id) AS total_bookings, 
               COALESCE(SUM(b.total_price), 0) AS total_spent,
               u.status
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id
        GROUP BY u.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy danh sách tour cho trang chủ
function getTours() {
    $pdo = connectDB();
    $stmt = $pdo->query("
        SELECT id, name, description, price, start_date, end_date, duration, destination, image_url, category, max_participants, total_bookings, rating
        FROM tours
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT 2
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy danh sách tour cho admin panel
function getAdminTours() {
    $pdo = connectDB();
    $stmt = $pdo->query("
        SELECT id, name, description, price, start_date, end_date, duration, destination, image_url, category, max_participants, total_bookings, rating, status
        FROM tours
        ORDER BY created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Thêm tour mới
function addTour($name, $destination, $duration, $price, $description, $category, $max_participants, $image_url) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        INSERT INTO tours (name, description, price, duration, destination, image_url, category, max_participants, total_bookings, rating, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0.0, 'active', NOW())
    ");
    try {
        $stmt->execute([$name, $description, $price, $duration, $destination, $image_url, $category, $max_participants]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Cập nhật tour
function editTour($tour_id, $name, $destination, $duration, $price, $description, $category, $max_participants, $image_url) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        UPDATE tours 
        SET name = ?, description = ?, price = ?, duration = ?, destination = ?, image_url = ?, category = ?, max_participants = ?, updated_at = NOW()
        WHERE id = ?
    ");
    try {
        $stmt->execute([$name, $description, $price, $duration, $destination, $image_url, $category, $max_participants, $tour_id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Xóa tour
function deleteTour($tour_id) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("DELETE FROM tours WHERE id = ?");
    try {
        $stmt->execute([$tour_id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Chuyển đổi trạng thái tour
function toggleTourStatus($tour_id, $action) {
    $pdo = connectDB();
    $status = ($action == 'activate') ? 'active' : 'inactive';
    $stmt = $pdo->prepare("UPDATE tours SET status = ?, updated_at = NOW() WHERE id = ?");
    try {
        $stmt->execute([$status, $tour_id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Lấy tổng số booking
function getTotalBookings() {
    $pdo = connectDB();
    $stmt = $pdo->query("SELECT SUM(total_bookings) as total FROM tours");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
}

// Lấy tổng doanh thu
function getTotalRevenue() {
    $pdo = connectDB();
    $stmt = $pdo->query("SELECT SUM(price * total_bookings) as revenue FROM tours");
    return $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;
}

// Định dạng ngày
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Định dạng tiền tệ
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . '₫';
}

// Lấy badge trạng thái tour
function getTourStatusBadge($status) {
    if ($status == 'active') {
        return '<span class="badge bg-success">Hoạt động</span>';
    }
    return '<span class="badge bg-secondary">Không hoạt động</span>';
}

// Đăng xuất admin
function adminLogout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Xử lý thêm người dùng
function addUser($name, $email, $phone, $password, $status) {
    $pdo = connectDB();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status, created_at) VALUES (?, ?, ?, ?, 'user', ?, NOW())");
    try {
        $stmt->execute([$name, $email, $phone, $hashedPassword, $status]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Xử lý chỉnh sửa người dùng
function editUser($userId, $name, $email, $phone, $status) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?");
    try {
        $stmt->execute([$name, $email, $phone, $status, $userId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Xử lý xóa người dùng
function deleteUser($userId) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    try {
        $stmt->execute([$userId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Xử lý kích hoạt/vô hiệu hóa người dùng
function toggleUserStatus($userId, $action) {
    $pdo = connectDB();
    $status = ($action == 'activate') ? 'active' : 'inactive';
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    try {
        $stmt->execute([$status, $userId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>