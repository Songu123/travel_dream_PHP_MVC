<?php
require_once 'includes/functions.php';
checkAdminLogin();

$adminInfo = getAdminInfo();

// Xử lý các hành động CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            if (addTour($_POST['name'], $_POST['destination'], $_POST['duration'], 
                       $_POST['price'], $_POST['description'], $_POST['category'], 
                       $_POST['max_participants'], $_POST['image_url'])) {
                $success = "Thêm tour thành công!";
            } else {
                $error = "Có lỗi xảy ra khi thêm tour!";
            }
            break;
            
        case 'edit':
            if (editTour($_POST['tour_id'], $_POST['name'], $_POST['destination'], 
                        $_POST['duration'], $_POST['price'], $_POST['description'], 
                        $_POST['category'], $_POST['max_participants'], $_POST['image_url'])) {
                $success = "Cập nhật tour thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật tour!";
            }
            break;
            
        case 'delete':
            if (deleteTour($_POST['tour_id'])) {
                $success = "Xóa tour thành công!";
            } else {
                $error = "Có lỗi xảy ra khi xóa tour!";
            }
            break;
            
        case 'toggle_status':
            if (toggleTourStatus($_POST['tour_id'], $_POST['status_action'])) {
                $success = "Cập nhật trạng thái tour thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật trạng thái!";
            }
            break;
    }
}

// Lấy danh sách tours
$tours = getTours();
$totalTours = count($tours);
$activeTours = array_filter($tours, function($tour) { return $tour['status'] == 'active'; });
$activeTourCount = count($activeTours);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tours - TravelDream Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-plane"></i>
                <span>TravelDream</span>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>
            <a href="tours.php" class="menu-item active">
                <i class="fas fa-route"></i>
                <span>Quản lý tours</span>
            </a>
            <a href="bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý đặt tour</span>
            </a>
            <a href="payments.php" class="menu-item">
                <i class="fas fa-credit-card"></i>
                <span>Quản lý thanh toán</span>
            </a>
            <a href="reviews.php" class="menu-item">
                <i class="fas fa-star"></i>
                <span>Quản lý đánh giá</span>
            </a>
            <a href="../index.html" class="menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Xem website</span>
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-route me-3"></i>
                    Quản Lý Tours
                </h1>
                <p class="page-subtitle">Quản lý và theo dõi tất cả các gói tour du lịch</p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTourModal">
                    <i class="fas fa-plus me-2"></i>Thêm Tour Mới
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card primary">
                    <div class="stats-content">
                        <div class="stats-header">
                            <h3 class="stats-number"><?= $totalTours ?></h3>
                            <div class="stats-icon">
                                <i class="fas fa-route"></i>
                            </div>
                        </div>
                        <p class="stats-label">Tổng số tours</p>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card success">
                    <div class="stats-content">
                        <div class="stats-header">
                            <h3 class="stats-number"><?= $activeTourCount ?></h3>
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <p class="stats-label">Tours đang hoạt động</p>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+5% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card warning">
                    <div class="stats-content">
                        <div class="stats-header">
                            <h3 class="stats-number"><?= getTotalBookings() ?></h3>
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <p class="stats-label">Tổng booking</p>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+23% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card info">
                    <div class="stats-content">
                        <div class="stats-header">
                            <h3 class="stats-number"><?= formatCurrency(getTotalRevenue()) ?></h3>
                            <div class="stats-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <p class="stats-label">Doanh thu tháng này</p>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+18% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tours Table -->
        <div class="content-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-list me-2"></i>Danh Sách Tours
                </h5>
                <div class="card-actions">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-2"></i>Lọc theo trạng thái
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="filterTable('all')">Tất cả</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterTable('active')">Đang hoạt động</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterTable('inactive')">Không hoạt động</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="toursTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tên Tour</th>
                                <th>Điểm đến</th>
                                <th>Thời gian</th>
                                <th>Giá</th>
                                <th>Danh mục</th>
                                <th>Số booking</th>
                                <th>Đánh giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $tour): ?>
                            <tr data-status="<?= $tour['status'] ?>">
                                <td><strong>#<?= $tour['id'] ?></strong></td>
                                <td>
                                    <div class="tour-image-thumb">
                                        <img src="<?= $tour['image_url'] ?: '../images/default-tour.jpg' ?>" alt="<?= $tour['name'] ?>" class="img-thumbnail">
                                    </div>
                                </td>
                                <td>
                                    <div class="tour-info">
                                        <h6 class="tour-name"><?= $tour['name'] ?></h6>
                                        <small class="text-muted">Max: <?= $tour['max_participants'] ?> người</small>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    <?= $tour['destination'] ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-clock me-1"></i><?= $tour['duration'] ?> ngày
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary"><?= formatCurrency($tour['price']) ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $categoryClass = $tour['category'] == 'domestic' ? 'bg-success' : 'bg-info';
                                    $categoryText = $tour['category'] == 'domestic' ? 'Trong nước' : 'Nước ngoài';
                                    ?>
                                    <span class="badge <?= $categoryClass ?>"><?= $categoryText ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= $tour['total_bookings'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <div class="rating">
                                        <?php
                                        $rating = $tour['rating'] ?? 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            $starClass = $i <= $rating ? 'fas fa-star text-warning' : 'far fa-star text-muted';
                                            echo "<i class=\"$starClass\"></i>";
                                        }
                                        ?>
                                        <small class="ms-1"><?= number_format($rating, 1) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?= getTourStatusBadge($tour['status']) ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="viewTour(<?= $tour['id'] ?>)" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="editTour(<?= $tour['id'] ?>)" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-<?= $tour['status'] == 'active' ? 'warning' : 'info' ?>" 
                                                onclick="toggleTourStatus(<?= $tour['id'] ?>, '<?= $tour['status'] == 'active' ? 'deactivate' : 'activate' ?>')" 
                                                title="<?= $tour['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                            <i class="fas fa-<?= $tour['status'] == 'active' ? 'pause' : 'play' ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteTour(<?= $tour['id'] ?>)" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Tour Modal -->
    <div class="modal fade" id="addTourModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Thêm Tour Mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="addTourForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Tên tour *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Danh mục *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="domestic">Tour trong nước</option>
                                    <option value="international">Tour nước ngoài</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Điểm đến *</label>
                                <input type="text" class="form-control" name="destination" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Thời gian (ngày) *</label>
                                <input type="number" class="form-control" name="duration" min="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Số người tối đa *</label>
                                <input type="number" class="form-control" name="max_participants" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá tour (VNĐ) *</label>
                                <input type="number" class="form-control" name="price" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">URL ảnh</label>
                                <input type="url" class="form-control" name="image_url" placeholder="https://example.com/image.jpg">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả tour</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Mô tả chi tiết về tour..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu Tour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Tour Modal -->
    <div class="modal fade" id="editTourModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Chỉnh Sửa Tour
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editTourForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="tour_id" id="edit_tour_id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Tên tour *</label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Danh mục *</label>
                                <select class="form-select" name="category" id="edit_category" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="domestic">Tour trong nước</option>
                                    <option value="international">Tour nước ngoài</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Điểm đến *</label>
                                <input type="text" class="form-control" name="destination" id="edit_destination" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Thời gian (ngày) *</label>
                                <input type="number" class="form-control" name="duration" id="edit_duration" min="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Số người tối đa *</label>
                                <input type="number" class="form-control" name="max_participants" id="edit_max_participants" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá tour (VNĐ) *</label>
                                <input type="number" class="form-control" name="price" id="edit_price" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">URL ảnh</label>
                                <input type="url" class="form-control" name="image_url" id="edit_image_url" placeholder="https://example.com/image.jpg">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả tour</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="4" placeholder="Mô tả chi tiết về tour..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Tour Modal -->
    <div class="modal fade" id="viewTourModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Chi Tiết Tour
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewTourContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#toursTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                columnDefs: [
                    { orderable: false, targets: [1, 10] } // Disable sorting for image and actions columns
                ]
            });
        });

        // Filter table by status
        function filterTable(status) {
            const table = $('#toursTable').DataTable();
            if (status === 'all') {
                table.search('').draw();
            } else {
                table.column(9).search(status).draw();
            }
        }

        // View tour details
        function viewTour(tourId) {
            // In a real application, this would fetch data via AJAX
            const tour = <?= json_encode($tours) ?>.find(t => t.id == tourId);
            
            const content = `
                <div class="row">
                    <div class="col-md-4">
                        <img src="${tour.image_url || '../images/default-tour.jpg'}" class="img-fluid rounded" alt="${tour.name}">
                    </div>
                    <div class="col-md-8">
                        <h4>${tour.name}</h4>
                        <p class="text-muted">${tour.description || 'Không có mô tả'}</p>
                        
                        <div class="row">
                            <div class="col-6">
                                <strong>Điểm đến:</strong> ${tour.destination}<br>
                                <strong>Thời gian:</strong> ${tour.duration} ngày<br>
                                <strong>Giá:</strong> ${new Intl.NumberFormat('vi-VN').format(tour.price)}₫
                            </div>
                            <div class="col-6">
                                <strong>Danh mục:</strong> ${tour.category == 'domestic' ? 'Trong nước' : 'Nước ngoài'}<br>
                                <strong>Số người tối đa:</strong> ${tour.max_participants}<br>
                                <strong>Trạng thái:</strong> ${tour.status == 'active' ? 'Hoạt động' : 'Không hoạt động'}
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <strong>Thống kê:</strong><br>
                            <span class="badge bg-primary me-2">Booking: ${tour.total_bookings || 0}</span>
                            <span class="badge bg-warning">Rating: ${tour.rating || 0}/5</span>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('viewTourContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewTourModal')).show();
        }

        // Edit tour
        function editTour(tourId) {
            const tour = <?= json_encode($tours) ?>.find(t => t.id == tourId);
            
            document.getElementById('edit_tour_id').value = tour.id;
            document.getElementById('edit_name').value = tour.name;
            document.getElementById('edit_destination').value = tour.destination;
            document.getElementById('edit_duration').value = tour.duration;
            document.getElementById('edit_price').value = tour.price;
            document.getElementById('edit_description').value = tour.description || '';
            document.getElementById('edit_category').value = tour.category;
            document.getElementById('edit_max_participants').value = tour.max_participants;
            document.getElementById('edit_image_url').value = tour.image_url || '';
            
            new bootstrap.Modal(document.getElementById('editTourModal')).show();
        }

        // Toggle tour status
        function toggleTourStatus(tourId, action) {
            const actionText = action === 'activate' ? 'kích hoạt' : 'vô hiệu hóa';
            
            Swal.fire({
                title: `Bạn có chắc chắn muốn ${actionText} tour này?`,
                text: `Tour sẽ được ${actionText} và thay đổi trạng thái.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="tour_id" value="${tourId}">
                        <input type="hidden" name="status_action" value="${action}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Delete tour
        function deleteTour(tourId) {
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa tour này?',
                text: 'Hành động này không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="tour_id" value="${tourId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Form validation
        document.getElementById('addTourForm').addEventListener('submit', function(e) {
            const price = parseInt(document.querySelector('input[name="price"]').value);
            if (price < 0) {
                e.preventDefault();
                Swal.fire('Lỗi', 'Giá tour phải lớn hơn 0', 'error');
            }
        });

        document.getElementById('editTourForm').addEventListener('submit', function(e) {
            const price = parseInt(document.getElementById('edit_price').value);
            if (price < 0) {
                e.preventDefault();
                Swal.fire('Lỗi', 'Giá tour phải lớn hơn 0', 'error');
            }
        });
    </script>

</body>
</html>