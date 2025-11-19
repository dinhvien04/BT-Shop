<?php
session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['username']) || $_SESSION['vaitro'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../connect.php';
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Xử lý form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_info':
            // Cập nhật thông tin website (giả lập)
            $message = 'Cập nhật thông tin website thành công!';
            break;
            
        case 'change_password':
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($new_password !== $confirm_password) {
                $error = 'Mật khẩu xác nhận không khớp!';
            } elseif (strlen($new_password) < 6) {
                $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
            } else {
                // Kiểm tra mật khẩu hiện tại
                $username = $_SESSION['username'];
                $check_query = "SELECT password FROM taikhoan WHERE username = '$username'";
                $result = mysqli_query($conn, $check_query);
                $user = mysqli_fetch_assoc($result);
                
                if ($user && $user['password'] === $current_password) {
                    // Cập nhật mật khẩu mới
                    $update_query = "UPDATE taikhoan SET password = '$new_password' WHERE username = '$username'";
                    if (mysqli_query($conn, $update_query)) {
                        $message = 'Đổi mật khẩu thành công!';
                    } else {
                        $error = 'Lỗi khi cập nhật mật khẩu!';
                    }
                } else {
                    $error = 'Mật khẩu hiện tại không đúng!';
                }
            }
            break;
            
        case 'backup_db':
            // Giả lập backup database
            $message = 'Sao lưu database thành công! File được lưu tại: backup_' . date('Y-m-d_H-i-s') . '.sql';
            break;
            
        case 'clear_cache':
            // Giả lập xóa cache
            $message = 'Xóa cache thành công!';
            break;
    }
}

// Lấy thống kê hệ thống
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sanpham"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM hoadon"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM khachhang WHERE makh NOT IN ('admin', 'manager')"))['count'];
$db_size = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS size_mb FROM information_schema.tables WHERE table_schema = 'qlbh'"))['size_mb'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - BT Shop Admin</title>
    <link rel="stylesheet" href="../accsets/css/base.css">
    <link rel="stylesheet" href="../accsets/fonts/themify-icons/themify-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            color: #ecf0f1;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            color: #bdc3c7;
            font-size: 14px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #3498db;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        /* Settings Tabs */
        .settings-tabs {
            display: flex;
            background: white;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .tab-button {
            padding: 15px 25px;
            background: none;
            border: none;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            color: #7f8c8d;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .tab-button.active {
            color: #3498db;
            border-bottom-color: #3498db;
        }
        
        .tab-button:hover {
            background: #f8f9fa;
        }
        
        /* Settings Content */
        .settings-content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        /* Buttons */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        /* Info Cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .info-card i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .info-card h3 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .info-card p {
            color: #7f8c8d;
        }
        
        /* Action Buttons */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .action-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .action-card i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .action-card h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .action-card p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .settings-tabs {
                flex-direction: column;
            }
            
            .form-grid,
            .info-cards,
            .action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>BT Shop Admin</h2>
                <p>Quản trị hệ thống</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="ti-home"></i>Dashboard</a></li>
                <li><a href="products.php"><i class="ti-package"></i>Quản lý sản phẩm</a></li>
                <li><a href="orders.php"><i class="ti-shopping-cart"></i>Quản lý đơn hàng</a></li>
                <li><a href="customers.php"><i class="ti-user"></i>Quản lý khách hàng</a></li>
                <li><a href="reports.php"><i class="ti-bar-chart"></i>Báo cáo thống kê</a></li>
                <li><a href="settings.php" class="active"><i class="ti-settings"></i>Cài đặt</a></li>
                <li><a href="../logout.php"><i class="ti-power-off"></i>Đăng xuất</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">Cài đặt hệ thống</h1>
                <p>Quản lý cấu hình và bảo mật website</p>
            </div>

            <!-- Alert Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="ti-check-box"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="ti-close"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- System Info Cards -->
            <div class="info-cards">
                <div class="info-card">
                    <i class="ti-package"></i>
                    <h3><?= number_format($total_products) ?></h3>
                    <p>Sản phẩm</p>
                </div>
                <div class="info-card">
                    <i class="ti-shopping-cart"></i>
                    <h3><?= number_format($total_orders) ?></h3>
                    <p>Đơn hàng</p>
                </div>
                <div class="info-card">
                    <i class="ti-user"></i>
                    <h3><?= number_format($total_customers) ?></h3>
                    <p>Khách hàng</p>
                </div>
                <div class="info-card">
                    <i class="ti-server"></i>
                    <h3><?= $db_size ?> MB</h3>
                    <p>Dung lượng DB</p>
                </div>
            </div>

            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button class="tab-button active" onclick="switchTab(event, 'general')">
                    <i class="ti-settings"></i> Tổng quan
                </button>
                <button class="tab-button" onclick="switchTab(event, 'security')">
                    <i class="ti-shield"></i> Bảo mật
                </button>
                <button class="tab-button" onclick="switchTab(event, 'email')">
                    <i class="ti-email"></i> Email
                </button>
                <button class="tab-button" onclick="switchTab(event, 'system')">
                    <i class="ti-server"></i> Hệ thống
                </button>
            </div>

            <div class="settings-content">
                <!-- General Tab -->
                <div id="general" class="tab-pane active">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Thông tin website</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_info">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tên website</label>
                                <input type="text" name="site_name" value="BT Shop" required>
                            </div>
                            <div class="form-group">
                                <label>Email liên hệ</label>
                                <input type="email" name="contact_email" value="admin@btshop.com" required>
                            </div>
                            <div class="form-group">
                                <label>Số điện thoại</label>
                                <input type="tel" name="phone" value="0123456789" required>
                            </div>
                            <div class="form-group">
                                <label>Địa chỉ</label>
                                <input type="text" name="address" value="123 Admin Street, Hà Nội" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mô tả website</label>
                            <textarea name="description" placeholder="Nhập mô tả về website...">BT Shop - Cửa hàng thời trang uy tín, chất lượng cao với nhiều sản phẩm đa dạng.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>

                <!-- Security Tab -->
                <div id="security" class="tab-pane">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Đổi mật khẩu</h3>
                    <form method="POST" style="max-width: 500px;">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label>Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_password" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label>Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="ti-key"></i> Đổi mật khẩu
                        </button>
                    </form>

                    <hr style="margin: 30px 0;">

                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Cài đặt bảo mật</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Thời gian hết phiên (phút)</label>
                            <select name="session_timeout">
                                <option value="30">30 phút</option>
                                <option value="60" selected>60 phút</option>
                                <option value="120">120 phút</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Số lần đăng nhập sai tối đa</label>
                            <select name="max_login_attempts">
                                <option value="3" selected>3 lần</option>
                                <option value="5">5 lần</option>
                                <option value="10">10 lần</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Email Tab -->
                <div id="email" class="tab-pane">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Cấu hình email</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_email">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" name="smtp_host" value="smtp.gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="number" name="smtp_port" value="587" required>
                            </div>
                            <div class="form-group">
                                <label>Email gửi</label>
                                <input type="email" name="smtp_email" value="noreply@btshop.com" required>
                            </div>
                            <div class="form-group">
                                <label>Mật khẩu email</label>
                                <input type="password" name="smtp_password" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti-save"></i> Lưu cấu hình email
                        </button>
                    </form>
                </div>

                <!-- System Tab -->
                <div id="system" class="tab-pane">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Công cụ hệ thống</h3>
                    <div class="action-grid">
                        <div class="action-card">
                            <i class="ti-download" style="color: #2ecc71;"></i>
                            <h4>Sao lưu dữ liệu</h4>
                            <p>Tạo bản sao lưu toàn bộ cơ sở dữ liệu</p>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="backup_db">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti-download"></i> Sao lưu ngay
                                </button>
                            </form>
                        </div>

                        <div class="action-card">
                            <i class="ti-trash" style="color: #f39c12;"></i>
                            <h4>Xóa cache</h4>
                            <p>Xóa tất cả file cache để cải thiện hiệu suất</p>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="clear_cache">
                                <button type="submit" class="btn btn-warning">
                                    <i class="ti-trash"></i> Xóa cache
                                </button>
                            </form>
                        </div>

                        <div class="action-card">
                            <i class="ti-info-alt" style="color: #3498db;"></i>
                            <h4>Thông tin hệ thống</h4>
                            <p>Xem thông tin chi tiết về server và PHP</p>
                            <button type="button" class="btn btn-primary" onclick="showSystemInfo()">
                                <i class="ti-info-alt"></i> Xem thông tin
                            </button>
                        </div>

                        <div class="action-card">
                            <i class="ti-reload" style="color: #e74c3c;"></i>
                            <h4>Khởi động lại</h4>
                            <p>Khởi động lại các dịch vụ hệ thống</p>
                            <button type="button" class="btn btn-danger" onclick="confirmRestart()">
                                <i class="ti-reload"></i> Khởi động lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(event, tabName) {
            // Hide all tab panes
            var tabPanes = document.getElementsByClassName('tab-pane');
            for (var i = 0; i < tabPanes.length; i++) {
                tabPanes[i].classList.remove('active');
            }
            
            // Remove active class from all tab buttons
            var tabButtons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show selected tab pane and mark button as active
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function showSystemInfo() {
            alert('Thông tin hệ thống:\n\n' +
                  'PHP Version: <?= phpversion() ?>\n' +
                  'Server: <?= $_SERVER["SERVER_SOFTWARE"] ?? "Unknown" ?>\n' +
                  'MySQL Version: <?= mysqli_get_server_info($conn) ?>\n' +
                  'Max Upload Size: <?= ini_get("upload_max_filesize") ?>\n' +
                  'Memory Limit: <?= ini_get("memory_limit") ?>');
        }

        function confirmRestart() {
            if (confirm('Bạn có chắc chắn muốn khởi động lại hệ thống?\nViệc này có thể làm gián đoạn dịch vụ trong vài phút.')) {
                alert('Tính năng khởi động lại đang được phát triển...');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html> 