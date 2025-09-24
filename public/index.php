<?php

// Define possible paths for the database config file
$config_paths = [
    __DIR__ . '/../config/database.php',
    __DIR__ . '/config/database.php',
    './config/database.php',
    '../config/database.php'
];

$config_loaded = false;
foreach ($config_paths as $path) {
    if (file_exists($path)) {
        include $path;
        $config_loaded = true;
        break;
    }
}

if (!$config_loaded) {
    die('Database configuration file not found. Please check if config/database.php exists.');
}

// Check if database connection exists
if (!isset($conn) || $conn->connect_error) {
    die('Database connection failed: ' . ($conn->connect_error ?? 'Connection variable not found'));
}

$sql = "SELECT * FROM tours ORDER BY created_at DESC LIMIT 6";
$result = $conn->query($sql);

$sql_key = "SELECT * FROM tours ORDER BY RAND() LIMIT 4";
$result_key = $conn->query($sql_key);

?>
<!DOCTYPE html>
<html lang="vi">
<?php include 'app/views/layouts/header.php'; ?>
<body>

    <!-- Navigation -->
    <!-- Navbar -->
    <?php include 'inc/navbar.php'; ?>


    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title" data-aos="fade-up">
                        Khám Phá Thế Giới <br>
                        <span class="text-primary">Cùng TravelDream</span>
                    </h1>
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Những hành trình tuyệt vời đang chờ đón bạn. Trải nghiệm văn hóa,
                        ẩm thực và cảnh đẹp từ khắp nơi trên thế giới.
                    </p>
                    <div class="hero-buttons" data-aos="fade-up" data-aos-delay="200">
                        <a href="#destinations" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-search me-2"></i>Khám Phá Ngay
                        </a>
                        <a href="#tours" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>Xem Tours
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Down Arrow -->
        <div class="scroll-down">
            <a href="#features" class="scroll-arrow">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>An Toàn & Tin Cậy</h4>
                        <p>Đảm bảo an toàn tuyệt đối với các chuyến đi được tổ chức chuyên nghiệp</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h4>Giá Cả Hợp Lý</h4>
                        <p>Những gói tour với mức giá phù hợp, chất lượng dịch vụ tốt nhất</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>Hỗ Trợ 24/7</h4>
                        <p>Đội ngũ tư vấn viên sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Destinations Section -->
    <section id="destinations" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Điểm Đến Phổ Biến</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                    Khám phá những địa điểm tuyệt đẹp được yêu thích nhất
                </p>
            </div>

            <div class="row">
                <?php
                if ($result->num_rows > 0) {
                    foreach ($result as $tour):
                ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="<?php echo $tour['image_url']; ?>" alt="<?php echo $tour['name']; ?>" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ <?php echo number_format($tour['price'], 0, ',', '.'); ?>₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5><?php echo $tour['name']; ?></h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i><?php echo $tour['destination']; ?></p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-2">4.8 (245 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div>
                  <?php endforeach; ?>
        <?php } else { ?>
            <tr><td colspan="3">Không có dữ liệu</td></tr>
        <?php } ?>
                
                <!-- 
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Bali" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ 8,900,000₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5>Bali</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i>Indonesia</p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-2">4.9 (324 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Santorini" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ 35,000,000₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5>Santorini</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i>Hy Lạp</p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-2">4.7 (189 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Seoul" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ 12,500,000₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5>Seoul</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i>Hàn Quốc</p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-muted"></i>
                                <span class="ms-2">4.5 (412 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="https://images.unsplash.com/photo-1542051841857-5f90071e7989?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Tokyo" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ 18,900,000₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5>Tokyo</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i>Nhật Bản</p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-2">4.8 (567 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Paris" class="img-fluid">
                            <div class="destination-overlay">
                                <div class="destination-price">Từ 42,000,000₫</div>
                            </div>
                        </div>
                        <div class="destination-content">
                            <h5>Paris</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i>Pháp</p>
                            <div class="destination-rating">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-2">4.9 (723 đánh giá)</span>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <div class="text-center mt-4">
                <a href="pages/destinations.html" class="btn btn-primary btn-lg">
                    <i class="fas fa-globe-americas me-2"></i>Xem Tất Cả Điểm Đến
                </a>
            </div>
        </div>
    </section>

    <!-- Tours Section -->
    <section id="tours" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Gói Tour Nổi Bật</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                    Những gói tour được lựa chọn cẩn thận với trải nghiệm tuyệt vời
                </p>
            </div>

            <div class="row">
                <?php foreach ($result_key as $tour) : ?>
                <div class="col-lg-6 mb-4" data-aos="fade-up">
                    <div class="tour-card">
                        <div class="tour-image">
                            <img src="<?php echo $tour['image_url']; ?>" alt="<?php echo $tour['name']; ?>" class="img-fluid">
                            <div class="tour-badge">Bán Chạy</div>
                        </div>
                        <div class="tour-content">
                            <div class="tour-header">
                                <h5><?php echo $tour['name']; ?></h5>
                                <span class="tour-price"><?php echo number_format($tour['price'], 0, ',', '.'); ?>₫</span>
                            </div>
                            <p class="tour-description">
                                <?php echo $tour['description']; ?>
                            </p>
                            <div class="tour-details">
                                <span><i class="fas fa-calendar-alt text-primary me-1"></i><?php echo $tour['duration']; ?></span>
                                <span><i class="fas fa-users text-primary me-1"></i><?php echo $tour['max_participants']; ?> người</span>
                                <span><i class="fas fa-plane text-primary me-1"></i>Máy bay</span>
                            </div>  
                            <div class="tour-footer">
                                <div class="tour-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <span>4.8 (156 đánh giá)</span>
                                </div>
                                <a href="./pages/tour-detail.php?id=<?php echo $tour['id']; ?>" class="btn btn-primary">Đặt Ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="tour-card">
                        <div class="tour-image">
                            <img src="https://images.unsplash.com/photo-1555400082-8dd4d78462b6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Singapore Tour" class="img-fluid">
                            <div class="tour-badge">Mới</div>
                        </div>
                        <div class="tour-content">
                            <div class="tour-header">
                                <h5>Singapore 3N2Đ - Đảo Quốc Sư Tử</h5>
                                <span class="tour-price">9,500,000₫</span>
                            </div>
                            <p class="tour-description">
                                Trải nghiệm Singapore hiện đại với Gardens by the Bay, Universal Studios và Marina Bay Sands.
                            </p>
                            <div class="tour-details">
                                <span><i class="fas fa-calendar-alt text-primary me-1"></i>3 ngày 2 đêm</span>
                                <span><i class="fas fa-users text-primary me-1"></i>12 người</span>
                                <span><i class="fas fa-plane text-primary me-1"></i>Máy bay</span>
                            </div>
                            <div class="tour-footer">
                                <div class="tour-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <span>4.9 (89 đánh giá)</span>
                                </div>
                                <a href="./pages/tour-detail.html" class="btn btn-primary">Đặt Ngay</a>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <div class="text-center">
                <a href="pages/tours.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-route me-2"></i>Xem Tất Cả Tours
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="section-title">Về TravelDream</h2>
                    <p class="lead mb-4">
                        Với hơn 10 năm kinh nghiệm trong lĩnh vực du lịch, chúng tôi tự hào là đối tác đáng tin cậy
                        cho những chuyến hành trình khám phá thế giới của bạn.
                    </p>
                    <p class="mb-4">
                        TravelDream cam kết mang đến những trải nghiệm du lịch chất lượng cao, an toàn và đáng nhớ.
                        Đội ngũ chuyên gia giàu kinh nghiệm của chúng tôi luôn sẵn sàng tư vấn và thiết kế những
                        chuyến đi phù hợp với nhu cầu và ngân sách của từng khách hàng.
                    </p>

                    <div class="row">
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="stat-number text-primary">50,000+</h3>
                                <p class="stat-label">Khách Hàng Hài Lòng</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="stat-number text-primary">150+</h3>
                                <p class="stat-label">Điểm Đến</p>
                            </div>
                        </div>
                    </div>

                    <a href="pages/about.html" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-info-circle me-2"></i>Tìm Hiểu Thêm
                    </a>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-image">
                        <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Us" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Khách Hàng Nói Gì Về Chúng Tôi</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                    Những chia sẻ chân thật từ khách hàng đã trải nghiệm dịch vụ
                </p>
            </div>

            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="testimonial-card text-center" data-aos="fade-up">
                                    <div class="testimonial-avatar">
                                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Customer 1" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-content">
                                        <div class="testimonial-rating mb-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="mb-4">"Chuyến đi Bali cùng TravelDream thật tuyệt vời! Mọi thứ đều được sắp xếp chu đáo, từ khách sạn, di chuyển đến các hoạt động. Tôi chắc chắn sẽ quay lại với TravelDream cho những chuyến đi tiếp theo."</p>
                                        </blockquote>
                                        <footer class="blockquote-footer">
                                            <cite title="Source Title">Nguyễn Thị Lan - Hà Nội</cite>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="testimonial-card text-center" data-aos="fade-up">
                                    <div class="testimonial-avatar">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Customer 2" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-content">
                                        <div class="testimonial-rating mb-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="mb-4">"Dịch vụ chuyên nghiệp, hướng dẫn viên nhiệt tình và am hiểu. Tour Nhật Bản 7 ngày đã để lại cho gia đình tôi những kỷ niệm khó quên. Cảm ơn TravelDream!"</p>
                                        </blockquote>
                                        <footer class="blockquote-footer">
                                            <cite title="Source Title">Trần Văn Minh - TP.HCM</cite>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="testimonial-card text-center" data-aos="fade-up">
                                    <div class="testimonial-avatar">
                                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Customer 3" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-content">
                                        <div class="testimonial-rating mb-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="mb-4">"Lần đầu đi tour nước ngoài, tôi khá lo lắng. Nhưng TravelDream đã hỗ trợ tôi từ A-Z, từ làm visa đến tư vấn hành lý. Thực sự đáng tin cậy!"</p>
                                        </blockquote>
                                        <footer class="blockquote-footer">
                                            <cite title="Source Title">Phạm Thị Hoa - Đà Nẵng</cite>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h2 class="mb-4">Sẵn Sàng Cho Chuyến Phiêu Lưu Tiếp Theo?</h2>
                    <p class="lead mb-4">
                        Liên hệ với chúng tôi ngay hôm nay để được tư vấn và thiết kế chuyến đi trong mơ của bạn!
                    </p>
                    <div class="contact-info">
                        <div class="contact-item mb-3">
                            <i class="fas fa-phone me-3"></i>
                            <strong>Hotline:</strong> 1900 2024
                        </div>
                        <div class="contact-item mb-3">
                            <i class="fas fa-envelope me-3"></i>
                            <strong>Email:</strong> info@traveldream.vn
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt me-3"></i>
                            <strong>Địa chỉ:</strong> 123 Nguyễn Huệ, Quận 1, TP.HCM
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-left">
                    <div class="text-center">
                        <a href="pages/contact.html" class="btn btn-light btn-lg px-5">
                            <i class="fas fa-paper-plane me-2"></i>Liên Hệ Ngay
                        </a>
                        <div class="social-links mt-4">
                            <h6>Theo dõi chúng tôi:</h6>
                            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-2x"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-youtube fa-2x"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-tiktok fa-2x"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'inc/footer.php'; ?>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JavaScript -->
    <script src="js/script.js"></script>

</body>

</html>