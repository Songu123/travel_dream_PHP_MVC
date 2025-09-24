<?php
require_once 'includes/functions.php';
checkAdminLogin();

$admin = getAdminInfo();

// Mock statistics data
$stats = [
    'total_users' => 1247,
    'total_tours' => 48,
    'total_bookings' => 2851,
    'total_revenue' => 4789650000,
    'monthly_bookings' => 284,
    'pending_reviews' => 12,
    'active_tours' => 42,
    'cancelled_bookings' => 23
];

// Recent activities
$recent_activities = [
    [
        'type' => 'booking',
        'message' => 'Booking mới từ Nguyễn Văn An cho tour Phú Quốc',
        'time' => '2 phút trước',
        'icon' => 'fas fa-calendar-plus',
        'color' => 'success'
    ],
    [
        'type' => 'payment',
        'message' => 'Thanh toán thành công 6.250.000₫ từ Trần Thị Bình',
        'time' => '15 phút trước',
        'icon' => 'fas fa-credit-card',
        'color' => 'primary'
    ],
    [
        'type' => 'review',
        'message' => 'Đánh giá mới 5 sao cho tour Nhật Bản',
        'time' => '1 giờ trước',
        'icon' => 'fas fa-star',
        'color' => 'warning'
    ],
    [
        'type' => 'user',
        'message' => 'Người dùng mới đăng ký: Lê Minh Tuấn',
        'time' => '2 giờ trước',
        'icon' => 'fas fa-user-plus',
        'color' => 'info'
    ],
    [
        'type' => 'booking',
        'message' => 'Booking bị hủy từ Phạm Thị Hoa',
        'time' => '3 giờ trước',
        'icon' => 'fas fa-calendar-times',
        'color' => 'danger'
    ]
];

// Chart data for revenue (last 6 months)
$revenue_data = [
    'Apr 2024' => 745000000,
    'May 2024' => 825000000,
    'Jun 2024' => 892000000,
    'Jul 2024' => 934000000,
    'Aug 2024' => 1025000000,
    'Sep 2024' => 1156000000
];

// Top tours data
$top_tours = [
    ['name' => 'Phú Quốc 3N2Đ', 'bookings' => 156, 'revenue' => 390000000],
    ['name' => 'Thái Lan 4N3Đ', 'bookings' => 234, 'revenue' => 1846260000],
    ['name' => 'Singapore 3N2Đ', 'bookings' => 189, 'revenue' => 1795500000],
    ['name' => 'Nhật Bản 7N6Đ', 'bookings' => 98, 'revenue' => 1852200000],
    ['name' => 'Hàn Quốc 5N4Đ', 'bookings' => 67, 'revenue' => 837500000]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TravelDream Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" data-aos="slide-right" data-aos-duration="800">
            <div class="sidebar-header">
                <h3><i class="fas fa-plane"></i> TravelDream</h3>
                <small>Admin Panel</small>
            </div>
            <ul class="sidebar-menu">
                <li class="active">
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        <span>Quản lý người dùng</span>
                    </a>
                </li>
                <li>
                    <a href="tours.php">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Quản lý tour</span>
                    </a>
                </li>
                <li>
                    <a href="bookings.php">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý đặt tour</span>
                    </a>
                </li>
                <li>
                    <a href="payments.php">
                        <i class="fas fa-credit-card"></i>
                        <span>Quản lý thanh toán</span>
                    </a>
                </li>
                <li>
                    <a href="reviews.php">
                        <i class="fas fa-star"></i>
                        <span>Quản lý đánh giá</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <button class="btn btn-link sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Thông báo</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-plus text-success"></i> Booking mới từ Nguyễn Văn An</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-star text-warning"></i> Đánh giá mới 5 sao</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-credit-card text-primary"></i> Thanh toán thành công</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Xem tất cả</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['name']); ?>&background=0d6efd&color=fff" alt="Avatar" class="user-avatar">
                            <span><?php echo $admin['name']; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Cài đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?logout=1"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Welcome Message -->
                <div class="welcome-banner" data-aos="fade-up" data-aos-duration="600">
                    <div class="welcome-content">
                        <h2>Chào mừng trở lại, <?php echo $admin['name']; ?>! 👋</h2>
                        <p>Hôm nay là ngày tuyệt vời để quản lý du lịch</p>
                    </div>
                    <div class="welcome-stats">
                        <div class="welcome-stat">
                            <i class="fas fa-calendar-day"></i>
                            <span><?php echo date('d/m/Y'); ?></span>
                        </div>
                        <div class="welcome-stat">
                            <i class="fas fa-clock"></i>
                            <span id="currentTime"></span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter" data-target="<?php echo $stats['total_users']; ?>">0</h3>
                                <p>Tổng người dùng</p>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +12% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter" data-target="<?php echo $stats['total_tours']; ?>">0</h3>
                                <p>Tổng số tour</p>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +3 tour mới</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter" data-target="<?php echo $stats['total_bookings']; ?>">0</h3>
                                <p>Tổng booking</p>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +8% tháng này</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo formatCurrency($stats['total_revenue']); ?></h3>
                                <p>Tổng doanh thu</p>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +15% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-xl-8" data-aos="fade-right" data-aos-delay="500">
                        <div class="card chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">📈 Biểu đồ doanh thu 6 tháng gần đây</h5>
                                <div class="chart-controls">
                                    <button class="btn btn-sm btn-outline-primary active" data-period="6months">6 tháng</button>
                                    <button class="btn btn-sm btn-outline-primary" data-period="year">1 năm</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4" data-aos="fade-left" data-aos-delay="600">
                        <div class="card top-tours-card">
                            <div class="card-header">
                                <h5 class="card-title">🏆 Top 5 Tour phổ biến</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($top_tours as $index => $tour): ?>
                                <div class="tour-item" data-aos="fade-up" data-aos-delay="<?php echo 700 + ($index * 100); ?>">
                                    <div class="tour-rank rank-<?php echo $index + 1; ?>"><?php echo $index + 1; ?></div>
                                    <div class="tour-info">
                                        <h6><?php echo $tour['name']; ?></h6>
                                        <div class="tour-stats">
                                            <span class="badge bg-primary"><?php echo $tour['bookings']; ?> bookings</span>
                                            <small class="text-success fw-bold"><?php echo formatCurrency($tour['revenue']); ?></small>
                                        </div>
                                    </div>
                                    <div class="tour-trend">
                                        <i class="fas fa-trending-up text-success"></i>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities and Quick Stats -->
                <div class="row">
                    <div class="col-xl-8" data-aos="fade-up" data-aos-delay="800">
                        <div class="card activity-card">
                            <div class="card-header">
                                <h5 class="card-title">🔔 Hoạt động gần đây</h5>
                                <button class="btn btn-sm btn-outline-primary">Xem tất cả</button>
                            </div>
                            <div class="card-body">
                                <div class="activity-list">
                                    <?php foreach ($recent_activities as $index => $activity): ?>
                                    <div class="activity-item" data-aos="slide-right" data-aos-delay="<?php echo 900 + ($index * 100); ?>">
                                        <div class="activity-icon bg-<?php echo $activity['color']; ?>">
                                            <i class="<?php echo $activity['icon']; ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p><?php echo $activity['message']; ?></p>
                                            <small class="text-muted"><i class="fas fa-clock"></i> <?php echo $activity['time']; ?></small>
                                        </div>
                                        <div class="activity-action">
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4" data-aos="fade-left" data-aos-delay="900">
                        <div class="card quick-stats-card">
                            <div class="card-header">
                                <h5 class="card-title">⚡ Thống kê nhanh</h5>
                            </div>
                            <div class="card-body">
                                <div class="quick-stats">
                                    <div class="quick-stat-item" data-aos="zoom-in" data-aos-delay="1000">
                                        <div class="quick-stat-icon bg-success">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="quick-stat-content">
                                            <h6 class="counter" data-target="<?php echo $stats['monthly_bookings']; ?>">0</h6>
                                            <small>Booking tháng này</small>
                                        </div>
                                        <div class="quick-stat-trend">
                                            <i class="fas fa-arrow-up text-success"></i>
                                        </div>
                                    </div>
                                    <div class="quick-stat-item" data-aos="zoom-in" data-aos-delay="1100">
                                        <div class="quick-stat-icon bg-warning">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="quick-stat-content">
                                            <h6><?php echo $stats['pending_reviews']; ?></h6>
                                            <small>Đánh giá chờ duyệt</small>
                                        </div>
                                        <div class="quick-stat-trend">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="quick-stat-item" data-aos="zoom-in" data-aos-delay="1200">
                                        <div class="quick-stat-icon bg-primary">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div class="quick-stat-content">
                                            <h6><?php echo $stats['active_tours']; ?></h6>
                                            <small>Tour đang hoạt động</small>
                                        </div>
                                        <div class="quick-stat-trend">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                    <div class="quick-stat-item" data-aos="zoom-in" data-aos-delay="1300">
                                        <div class="quick-stat-icon bg-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div class="quick-stat-content">
                                            <h6><?php echo $stats['cancelled_bookings']; ?></h6>
                                            <small>Booking bị hủy</small>
                                        </div>
                                        <div class="quick-stat-trend">
                                            <i class="fas fa-arrow-down text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Current time update
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.floor(current).toLocaleString('vi-VN');
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString('vi-VN');
                    }
                };
                
                // Start animation when element is visible
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(counter);
            });
        }

        // Chart configuration with animations
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($revenue_data)); ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?php echo json_encode(array_values($revenue_data)); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#764ba2',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#2d3748',
                        bodyColor: '#2d3748',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#718096',
                            font: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(113, 128, 150, 0.1)',
                            borderDash: [5, 5]
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#718096',
                            font: {
                                family: 'Inter',
                                size: 12
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0,
                                    notation: 'compact'
                                }).format(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    }
                }
            }
        });

        // Sidebar toggle with animation
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
            
            // Animate chart resize
            setTimeout(() => {
                revenueChart.resize();
            }, 300);
        });

        // Chart period toggle
        document.querySelectorAll('.chart-controls button').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-controls button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Add loading animation
                const chartContainer = document.querySelector('#revenueChart').parentElement;
                chartContainer.style.opacity = '0.5';
                
                setTimeout(() => {
                    chartContainer.style.opacity = '1';
                }, 500);
            });
        });

        // Activity items hover effect
        document.querySelectorAll('.activity-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Tour items animation
        document.querySelectorAll('.tour-item').forEach((item, index) => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.zIndex = '10';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.zIndex = '1';
            });
        });

        // Quick stats pulse animation
        function pulseQuickStats() {
            document.querySelectorAll('.quick-stat-icon').forEach((icon, index) => {
                setTimeout(() => {
                    icon.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        icon.style.transform = 'scale(1)';
                    }, 200);
                }, index * 200);
            });
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            animateCounters();
            
            // Pulse quick stats every 5 seconds
            setInterval(pulseQuickStats, 5000);
            
            // Add loading states
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 1000);
        });

        // Notification bell animation
        setInterval(() => {
            const bell = document.querySelector('.fa-bell');
            if (bell) {
                bell.classList.add('fa-shake');
                setTimeout(() => {
                    bell.classList.remove('fa-shake');
                }, 1000);
            }
        }, 10000);
    </script>

    <?php
    // Handle logout
    if (isset($_GET['logout'])) {
        adminLogout();
    }
    ?>
</body>
</html>