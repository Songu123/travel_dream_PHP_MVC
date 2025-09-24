<?php
require_once "BaseModel.php";

class User extends BaseModel {
    
    // Lấy tất cả users
    public function getAll() {
        $sql = "SELECT id, name, email, phone, address, role, created_at, updated_at FROM users ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm user theo ID
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, phone, address, role, created_at, updated_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tìm user theo email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tạo user mới
    public function create($name, $email, $password, $phone = null, $address = null, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $hashedPassword, $phone, $address, $role);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Cập nhật thông tin user
    public function update($id, $name, $email, $phone = null, $address = null, $role = null) {
        if ($role !== null) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $address, $role, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
        }
        return $stmt->execute();
    }

    // Cập nhật mật khẩu
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    // Xác thực đăng nhập
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Không trả về password
            unset($user['password']);
            return $user;
        }
        return false;
    }

    // Kiểm tra email đã tồn tại
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Lấy users theo role
    public function getUsersByRole($role) {
        $stmt = $this->db->prepare("SELECT id, name, email, phone, address, role, created_at, updated_at FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Đếm tổng số users
    public function count() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM users");
        return $result->fetch_assoc()['total'];
    }

    // Đếm users theo role
    public function countByRole($role) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy users với phân trang
    public function getPaginated($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare("SELECT id, name, email, phone, address, role, created_at, updated_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm kiếm users
    public function search($keyword, $limit = 10) {
        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare("SELECT id, name, email, phone, address, role, created_at, updated_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ssi", $searchTerm, $searchTerm, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cập nhật role user
    public function updateRole($id, $role) {
        $stmt = $this->db->prepare("UPDATE users SET role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("si", $role, $id);
        return $stmt->execute();
    }

    // Xóa user
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Lấy thống kê users theo tháng
    public function getMonthlyStats() {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM users 
                WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
