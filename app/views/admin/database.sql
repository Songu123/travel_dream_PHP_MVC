-- Tạo database du lịch
CREATE DATABASE IF NOT EXISTS du_lich CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE du_lich;

-- Bảng users (người dùng)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng tours (các gói tour)
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    duration INT NOT NULL COMMENT 'Số ngày',
    price DECIMAL(12,2) NOT NULL,
    description TEXT,
    category ENUM('domestic', 'international') NOT NULL,
    max_participants INT NOT NULL DEFAULT 20,
    image_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_destination (destination)
);

-- Bảng bookings (đặt tour)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tour_id INT NOT NULL,
    booking_code VARCHAR(50) UNIQUE NOT NULL,
    departure_date DATE NOT NULL,
    adults INT NOT NULL DEFAULT 1,
    children INT NOT NULL DEFAULT 0,
    infants INT NOT NULL DEFAULT 0,
    total_price DECIMAL(12,2) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_address TEXT,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'partial', 'refunded') DEFAULT 'pending',
    payment_method ENUM('bank_transfer', 'credit_card', 'momo', 'office') DEFAULT 'bank_transfer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_tour (tour_id),
    INDEX idx_status (status),
    INDEX idx_departure (departure_date)
);

-- Bảng booking_guests (thông tin khách tham gia)
CREATE TABLE IF NOT EXISTS booking_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_type ENUM('adult', 'child', 'infant') NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    guest_gender ENUM('male', 'female') NOT NULL,
    guest_birthday DATE,
    guest_id_number VARCHAR(50),
    guest_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id)
);

-- Bảng payments (thanh toán)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('bank_transfer', 'credit_card', 'momo', 'office', 'refund') NOT NULL,
    transaction_id VARCHAR(255),
    payment_date TIMESTAMP NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date)
);

-- Bảng reviews (đánh giá)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tour_id INT NOT NULL,
    booking_id INT,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_tour (tour_id),
    INDEX idx_rating (rating),
    INDEX idx_status (status)
);

-- Insert dữ liệu mẫu

-- Admin user
INSERT INTO users (name, email, phone, password, role) VALUES 
('Administrator', 'admin@traveldream.vn', '1900202024', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample users
INSERT INTO users (name, email, phone, password, role) VALUES 
('Nguyễn Văn An', 'nguyenvanan@email.com', '0901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Trần Thị Bình', 'tranthibinh@email.com', '0907654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Lê Hoàng Nam', 'lehoangnam@email.com', '0912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Phạm Thị Hoa', 'phamthihoa@email.com', '0923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Võ Minh Tuấn', 'vominhtuan@email.com', '0934567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample tours
INSERT INTO tours (name, destination, duration, price, description, category, max_participants, image_url, status) VALUES 
('Phú Quốc 3N2Đ - Đảo Ngọc Kiên Giang', 'Phú Quốc, Kiên Giang', 3, 2500000, 'Khám phá đảo ngọc Phú Quốc với những bãi biển tuyệt đẹp, làng chài cổ kính và các hoạt động thể thao dưới nước thú vị.', 'domestic', 25, 'https://images.unsplash.com/photo-1528181304800-259b08848526?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Thái Lan 4N3Đ - Bangkok & Pattaya', 'Bangkok, Pattaya - Thái Lan', 4, 7890000, 'Tour Thái Lan khám phá Bangkok sôi động và thành phố biển Pattaya xinh đẹp với đầy đủ tiện nghi và dịch vụ 5 sao.', 'international', 20, 'https://images.unsplash.com/photo-1528181304800-259b08848526?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Singapore 3N2Đ - Đảo Quốc Sư Tử', 'Singapore', 3, 9500000, 'Trải nghiệm Singapore hiện đại với Gardens by the Bay, Universal Studios và Marina Bay Sands.', 'international', 18, 'https://images.unsplash.com/photo-1555400082-8dd4d78462b6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Nhật Bản 7N6Đ - Tokyo & Osaka', 'Tokyo, Osaka - Nhật Bản', 7, 18900000, 'Hành trình khám phá xứ sở hoa anh đào với Tokyo hiện đại và Osaka truyền thống, trải nghiệm văn hóa độc đáo.', 'international', 15, 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Hàn Quốc 5N4Đ - Seoul & Busan', 'Seoul, Busan - Hàn Quốc', 5, 12500000, 'Khám phá Hàn Quốc với Seoul hiện đại và Busan thơ mộng, trải nghiệm K-culture và ẩm thực tuyệt vời.', 'international', 20, 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'inactive'),
('Đà Lạt 2N1Đ - Thành Phố Ngàn Hoa', 'Đà Lạt, Lâm Đồng', 2, 1500000, 'Khám phá Đà Lạt thơ mộng với khí hậu mát mẻ, những đồi hoa đẹp và ẩm thực địa phương đặc trưng.', 'domestic', 30, 'https://images.unsplash.com/photo-1520637836862-4d197d17c10a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Hạ Long 2N1Đ - Vịnh Di Sản', 'Hạ Long, Quảng Ninh', 2, 2200000, 'Tham quan vịnh Hạ Long nổi tiếng thế giới với hàng nghìn đảo đá vôi và hang động tuyệt đẹp.', 'domestic', 25, 'https://images.unsplash.com/photo-1559592413-7cec4d0d5d7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active'),
('Sapa 3N2Đ - Mây Trời Tây Bắc', 'Sapa, Lào Cai', 3, 1800000, 'Khám phá Sapa với ruộng bậc thang hùng vĩ, văn hóa dân tộc thiểu số và đỉnh Fansipan.', 'domestic', 20, 'https://images.unsplash.com/photo-1587841797892-a0b32e3f4da5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'active');

-- Sample bookings
INSERT INTO bookings (user_id, tour_id, booking_code, departure_date, adults, children, infants, total_price, customer_name, customer_phone, customer_email, customer_address, status, payment_status, payment_method) VALUES 
(2, 1, 'TD20240001', '2024-10-20', 2, 1, 0, 6250000, 'Nguyễn Văn An', '0901234567', 'nguyenvanan@email.com', '123 Trần Hưng Đạo, Q1, TP.HCM', 'confirmed', 'paid', 'bank_transfer'),
(3, 2, 'TD20240002', '2024-11-05', 4, 0, 0, 31560000, 'Trần Thị Bình', '0907654321', 'tranthibinh@email.com', '456 Nguyễn Huệ, Q1, TP.HCM', 'pending', 'pending', 'credit_card'),
(4, 3, 'TD20240003', '2024-10-30', 2, 2, 0, 28500000, 'Lê Hoàng Nam', '0912345678', 'lehoangnam@email.com', '789 Lê Lợi, Q1, TP.HCM', 'confirmed', 'partial', 'bank_transfer'),
(5, 4, 'TD20240004', '2024-12-15', 2, 0, 0, 37800000, 'Phạm Thị Hoa', '0923456789', 'phamthihoa@email.com', '321 Pasteur, Q3, TP.HCM', 'confirmed', 'paid', 'momo'),
(6, 5, 'TD20240005', '2024-11-12', 3, 1, 0, 43750000, 'Võ Minh Tuấn', '0934567890', 'vominhtuan@email.com', '654 Cách Mạng Tháng 8, Q10, TP.HCM', 'cancelled', 'refunded', 'bank_transfer');

-- Sample payments
INSERT INTO payments (booking_id, amount, payment_method, transaction_id, payment_date, status) VALUES 
(1, 6250000, 'bank_transfer', 'TXN001234567', '2024-09-16 10:30:00', 'completed'),
(3, 14250000, 'bank_transfer', 'TXN001234568', '2024-09-21 14:15:00', 'completed'),
(4, 37800000, 'momo', 'TXN001234569', '2024-09-22 09:45:00', 'completed'),
(5, -43750000, 'refund', 'REF001234567', '2024-09-20 16:20:00', 'refunded');

-- Sample reviews
INSERT INTO reviews (user_id, tour_id, booking_id, rating, comment, status) VALUES 
(2, 1, 1, 5, 'Tour rất tuyệt vời! Hướng dẫn viên nhiệt tình, lịch trình hợp lý. Sẽ đi tour với TravelDream lần nữa.', 'approved'),
(5, 4, 4, 4, 'Chuyến đi Nhật Bản rất đáng nhớ. Tuy nhiên thời gian tại Tokyo hơi ngắn. Nhìn chung rất hài lòng.', 'approved'),
(4, 3, 3, 3, 'Tour ổn nhưng khách sạn không như mong đợi. Hy vọng công ty cải thiện chất lượng khách sạn.', 'pending'),
(3, 2, 2, 5, 'Excellent service! Everything was perfect from start to finish. Highly recommended!', 'approved'),
(6, 5, 5, 2, 'Tour bị hủy vào phút chót do lý do không rõ ràng. Rất thất vọng về dịch vụ.', 'rejected');