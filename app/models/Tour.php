<?php
require_once "BaseModel.php";

class Tour extends BaseModel {
    
    // Lấy tất cả tours
    public function getAll() {
        $sql = "SELECT * FROM tours ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours đang hoạt động
    public function getActiveTours() {
        $sql = "SELECT * FROM tours WHERE status = 'active' ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm tour theo ID
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tạo tour mới
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO tours (name, description, price, start_date, end_date, duration, destination, image_url, category, max_participants, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "ssdssssssss", 
            $data['name'],
            $data['description'],
            $data['price'],
            $data['start_date'],
            $data['end_date'],
            $data['duration'],
            $data['destination'],
            $data['image_url'],
            $data['category'],
            $data['max_participants'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Cập nhật tour
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE tours SET name = ?, description = ?, price = ?, start_date = ?, end_date = ?, duration = ?, destination = ?, image_url = ?, category = ?, max_participants = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        
        $stmt->bind_param(
            "ssdsssssssi", 
            $data['name'],
            $data['description'],
            $data['price'],
            $data['start_date'],
            $data['end_date'],
            $data['duration'],
            $data['destination'],
            $data['image_url'],
            $data['category'],
            $data['max_participants'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }

    // Lấy tours theo danh mục
    public function getToursByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE category = ? AND status = 'active' ORDER BY created_at DESC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours theo điểm đến
    public function getToursByDestination($destination) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE destination LIKE ? AND status = 'active' ORDER BY created_at DESC");
        $searchTerm = "%$destination%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours trong khoảng giá
    public function getToursByPriceRange($minPrice, $maxPrice) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE price BETWEEN ? AND ? AND status = 'active' ORDER BY price ASC");
        $stmt->bind_param("dd", $minPrice, $maxPrice);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm kiếm tours
    public function search($keyword, $limit = 10) {
        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE (name LIKE ? OR description LIKE ? OR destination LIKE ?) AND status = 'active' ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("sssi", $searchTerm, $searchTerm, $searchTerm, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours phổ biến (theo rating)
    public function getPopularTours($limit = 6) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE status = 'active' ORDER BY rating DESC, total_bookings DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours mới nhất
    public function getLatestTours($limit = 6) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE status = 'active' ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cập nhật số lượng booking
    public function updateBookingCount($tourId, $increment = 1) {
        $stmt = $this->db->prepare("UPDATE tours SET total_bookings = total_bookings + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("ii", $increment, $tourId);
        return $stmt->execute();
    }

    // Cập nhật rating
    public function updateRating($tourId, $newRating) {
        $stmt = $this->db->prepare("UPDATE tours SET rating = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("di", $newRating, $tourId);
        return $stmt->execute();
    }

    // Kiểm tra tour còn chỗ trống
    public function hasAvailableSpots($tourId) {
        $stmt = $this->db->prepare("SELECT max_participants, total_bookings FROM tours WHERE id = ?");
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result['total_bookings'] < $result['max_participants'];
        }
        return false;
    }

    // Lấy số chỗ trống
    public function getAvailableSpots($tourId) {
        $stmt = $this->db->prepare("SELECT max_participants, total_bookings FROM tours WHERE id = ?");
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result['max_participants'] - $result['total_bookings'];
        }
        return 0;
    }

    // Lấy tours theo trạng thái
    public function getToursByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE status = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cập nhật trạng thái tour
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE tours SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    // Lấy tours với phân trang
    public function getPaginated($limit = 10, $offset = 0, $status = null) {
        if ($status) {
            $stmt = $this->db->prepare("SELECT * FROM tours WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("sii", $status, $limit, $offset);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM tours ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Đếm tổng số tours
    public function count($status = null) {
        if ($status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tours WHERE status = ?");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['total'];
        } else {
            $result = $this->db->query("SELECT COUNT(*) as total FROM tours");
            return $result->fetch_assoc()['total'];
        }
    }

    // Đếm tours theo danh mục
    public function countByCategory($category) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tours WHERE category = ? AND status = 'active'");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy thống kê tours theo tháng
    public function getMonthlyStats() {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as total_tours,
                    SUM(total_bookings) as total_bookings,
                    AVG(rating) as avg_rating
                FROM tours 
                WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy top destinations
    public function getTopDestinations($limit = 5) {
        $stmt = $this->db->prepare("SELECT destination, COUNT(*) as tour_count, SUM(total_bookings) as total_bookings FROM tours WHERE status = 'active' GROUP BY destination ORDER BY total_bookings DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours sắp khởi hành
    public function getUpcomingTours($days = 30) {
        $stmt = $this->db->prepare("SELECT * FROM tours WHERE start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) AND status = 'active' ORDER BY start_date ASC");
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy tours đang diễn ra
    public function getOngoingTours() {
        $sql = "SELECT * FROM tours WHERE CURDATE() BETWEEN start_date AND end_date AND status = 'active' ORDER BY end_date ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Kiểm tra tour đã hết hạn
    public function isExpired($tourId) {
        $stmt = $this->db->prepare("SELECT end_date FROM tours WHERE id = ?");
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return date('Y-m-d') > $result['end_date'];
        }
        return true;
    }

    // Xóa tour
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Lọc tours theo nhiều điều kiện
    public function filterTours($filters = []) {
        $sql = "SELECT * FROM tours WHERE status = 'active'";
        $params = [];
        $types = "";

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['destination'])) {
            $sql .= " AND destination LIKE ?";
            $params[] = "%{$filters['destination']}%";
            $types .= "s";
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = $filters['min_price'];
            $types .= "d";
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = $filters['max_price'];
            $types .= "d";
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND start_date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }

        if (!empty($filters['duration'])) {
            $sql .= " AND duration LIKE ?";
            $params[] = "%{$filters['duration']}%";
            $types .= "s";
        }

        // Sắp xếp
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'DESC';
        $sql .= " ORDER BY {$orderBy} {$orderDirection}";

        // Giới hạn
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
            $types .= "i";
        }

        if (empty($params)) {
            $result = $this->db->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}