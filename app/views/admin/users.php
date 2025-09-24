<?php
require_once 'includes/functions.php';
checkAdminLogin();

$admin = getAdminInfo();
$users = getUsers(); // Lấy danh sách người dùng từ database

// Handle actions
$message = '';
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    switch ($_POST['action']) {
        case 'delete':
            if (deleteUser($userId)) {
                $message = 'Đã xóa người dùng thành công!';
            } else {
                $message = 'Lỗi khi xóa người dùng!';
            }
            break;
        case 'activate':
            if (toggleUserStatus($userId, 'activate')) {
                $message = 'Đã kích hoạt tài khoản thành công!';
            } else {
                $message = 'Lỗi khi kích hoạt tài khoản!';
            }
            break;
        case 'deactivate':
            if (toggleUserStatus($userId, 'deactivate')) {
                $message = 'Đã vô hiệu hóa tài khoản thành công!';
            } else {
                $message = 'Lỗi khi vô hiệu hóa tài khoản!';
            }
            break;
    }
}

// Handle add user
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $status = $_POST['status'];
    if (addUser($name, $email, $phone, $password, $status)) {
        $message = 'Đã thêm người dùng thành công!';
    } else {
        $message = 'Lỗi khi thêm người dùng! Vui lòng kiểm tra email đã tồn tại.';
    }
}

// Handle edit user
if (isset($_POST['edit_user'])) {
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];
    if (editUser($userId, $name, $email, $phone, $status)) {
        $message = 'Đã cập nhật người dùng thành công!';
    } else {
        $message = 'Lỗi khi cập nhật người dùng! Vui lòng kiểm tra email đã tồn tại.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - TravelDream Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-plane"></i> TravelDream</h3>
                <small>Admin Panel</small>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="active">
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
                    <h1>Quản lý người dùng</h1>
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
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['name']); ?>&background=0d6efd&color=fff" alt="Avatar" class="user-avatar">
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

            <!-- Content -->
            <div class="content-body">
                <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách người dùng</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Thêm người dùng
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, email, số điện thoại...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Không hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option value="">Sắp xếp theo</option>
                                    <option value="name">Tên A-Z</option>
                                    <option value="date">Ngày tham gia</option>
                                    <option value="bookings">Số lượng booking</option>
                                    <option value="spent">Tổng chi tiêu</option>
                                </select>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Người dùng</th>
                                        <th>Liên hệ</th>
                                        <th>Ngày tham gia</th>
                                        <th>Booking</th>
                                        <th>Tổng chi tiêu</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input user-checkbox" value="<?php echo $user['id']; ?>">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=6c757d&color=fff&size=40" 
                                                     alt="Avatar" class="rounded-circle me-3" width="40" height="40">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $user['name']; ?></h6>
                                                    <small class="text-muted">ID: #<?php echo $user['id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div><i class="fas fa-envelope text-muted me-1"></i><?php echo $user['email']; ?></div>
                                                <small class="text-muted"><i class="fas fa-phone text-muted me-1"></i><?php echo $user['phone']; ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo formatDate($user['join_date']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $user['total_bookings']; ?> tour</span>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?php echo formatCurrency($user['total_spent']); ?></strong>
                                        </td>
                                        <td><?php echo getStatusBadge($user['status']); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" data-bs-target="#viewUserModal"
                                                        onclick="viewUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                        onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <?php if ($user['status'] == 'active'): ?>
                                                        <li>
                                                            <form method="post" style="display: inline;">
                                                                <input type="hidden" name="action" value="deactivate">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="dropdown-item text-warning">
                                                                    <i class="fas fa-pause"></i> Vô hiệu hóa
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <?php else: ?>
                                                        <li>
                                                            <form method="post" style="display: inline;">
                                                                <input type="hidden" name="action" value="activate">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-play"></i> Kích hoạt
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <?php endif; ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="post" style="display: inline;" 
                                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash"></i> Xóa
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Trước</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Sau</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="add_user" value="1">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu *</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewUserContent">
                    <!-- Content will be loaded by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editUserForm">
                    <div class="modal-body" id="editUserContent">
                        <!-- Content will be loaded by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // View user function
        function viewUser(user) {
            const content = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0d6efd&color=fff&size=120" 
                             alt="Avatar" class="rounded-circle mb-3" width="120" height="120">
                        <h5>${user.name}</h5>
                        <p class="text-muted">ID: #${user.id}</p>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th>Email:</th>
                                <td>${user.email}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>${user.phone}</td>
                            </tr>
                            <tr>
                                <th>Ngày tham gia:</th>
                                <td>${new Date(user.join_date).toLocaleDateString('vi-VN')}</td>
                            </tr>
                            <tr>
                                <th>Tổng số booking:</th>
                                <td><span class="badge bg-info">${user.total_bookings} tour</span></td>
                            </tr>
                            <tr>
                                <th>Tổng chi tiêu:</th>
                                <td><strong class="text-success">${user.total_spent.toLocaleString('vi-VN')}₫</strong></td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td><span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-secondary'}">${user.status === 'active' ? 'Hoạt động' : 'Không hoạt động'}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
            document.getElementById('viewUserContent').innerHTML = content;
        }

        // Edit user function
        function editUser(user) {
            const content = `
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" name="user_id" value="${user.id}">
                <div class="mb-3">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" class="form-control" name="name" value="${user.name}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" value="${user.email}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại *</label>
                    <input type="tel" class="form-control" name="phone" value="${user.phone}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="active" ${user.status === 'active' ? 'selected' : ''}>Hoạt động</option>
                        <option value="inactive" ${user.status === 'inactive' ? 'selected' : ''}>Không hoạt động</option>
                    </select>
                </div>
            `;
            document.getElementById('editUserContent').innerHTML = content;
        }
    </script>

    <?php
    // Handle logout
    if (isset($_GET['logout'])) {
        adminLogout();
    }
    ?>
</body>
</html>