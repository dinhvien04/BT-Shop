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

// Lấy danh sách khách hàng
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = "WHERE kh.makh NOT IN ('admin', 'manager')";
if ($search) {
    $whereClause .= " AND (kh.hoten LIKE '%$search%' OR kh.makh LIKE '%$search%' OR tt.email LIKE '%$search%' OR tt.sodt LIKE '%$search%')";
}

$sql = "SELECT kh.*, tt.sodt, tt.email, tt.captinh, tt.caphuyen, tt.capxa, tt.sonha,
               COUNT(hd.soHD) as total_orders,
               SUM(CASE WHEN hd.trangthai LIKE '%Đã giao%' OR hd.trangthai LIKE '%Đã đặt hàng%' THEN hd.trigia ELSE 0 END) as total_spent
        FROM khachhang kh 
        LEFT JOIN thongtin_lienhe tt ON kh.makh = tt.makh
        LEFT JOIN hoadon hd ON kh.makh = hd.makh
        $whereClause 
        GROUP BY kh.makh 
        ORDER BY total_spent DESC, kh.makh ASC";

$result = mysqli_query($conn, $sql);
$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}

// Lấy lịch sử mua hàng cho modal
$customer_orders = [];
if (isset($_GET['view'])) {
    $makh = $_GET['view'];
    $sql_orders = "
        SELECT hd.*, COUNT(ct.masp) as total_items
        FROM hoadon hd 
        LEFT JOIN chitiethoadon ct ON hd.soHD = ct.soHD
        WHERE hd.makh = '$makh'
        GROUP BY hd.soHD
        ORDER BY hd.ngayHD DESC
    ";
    $result_orders = mysqli_query($conn, $sql_orders);
    while ($row = mysqli_fetch_assoc($result_orders)) {
        $customer_orders[] = $row;
    }
    
    // Lấy thông tin khách hàng
    $customer_info = null;
    foreach ($customers as $customer) {
        if ($customer['makh'] == $makh) {
            $customer_info = $customer;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng - BT Shop Admin</title>
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
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        /* Controls */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }
        
        .search-box {
            flex: 1;
            max-width: 400px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            text-align: center;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card.primary { border-left: 5px solid #3498db; }
        .stat-card.success { border-left: 5px solid #2ecc71; }
        .stat-card.warning { border-left: 5px solid #f39c12; }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-card.primary .stat-number { color: #3498db; }
        .stat-card.success .stat-number { color: #2ecc71; }
        .stat-card.warning .stat-number { color: #f39c12; }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Table */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .customer-level {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        
        .level-bronze {
            background: #cd7f32;
            color: white;
        }
        
        .level-silver {
            background: #c0c0c0;
            color: white;
        }
        
        .level-gold {
            background: #ffd700;
            color: #333;
        }
        
        .level-platinum {
            background: #e5e4e2;
            color: #333;
        }
        
        .price {
            color: #e74c3c;
            font-weight: bold;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 1000px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .customer-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .detail-value {
            color: #2c3e50;
            font-size: 16px;
        }
        
        .orders-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 10px;
            background: white;
        }
        
        .order-info {
            flex: 1;
        }
        
        .order-id {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .order-date {
            color: #6c757d;
            font-size: 14px;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>BT Shop Admin</h2>
                <p>Quản trị hệ thống</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="ti-home"></i>Dashboard</a></li>
                <li><a href="products.php"><i class="ti-package"></i>Quản lý sản phẩm</a></li>
                <li><a href="orders.php"><i class="ti-shopping-cart"></i>Quản lý đơn hàng</a></li>
                <li><a href="customers.php" class="active"><i class="ti-user"></i>Quản lý khách hàng</a></li>
                <li><a href="reports.php"><i class="ti-bar-chart"></i>Báo cáo thống kê</a></li>
                <li><a href="settings.php"><i class="ti-settings"></i>Cài đặt</a></li>
                <li><a href="../logout.php"><i class="ti-power-off"></i>Đăng xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Quản lý khách hàng</h1>
                <p>Theo dõi thông tin và hoạt động của khách hàng</p>
            </div>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <?php
                $total_customers = count($customers);
                $vip_customers = 0;
                $active_customers = 0;
                
                foreach ($customers as $customer) {
                    if ($customer['total_spent'] > 5000000) $vip_customers++;
                    if ($customer['total_orders'] > 0) $active_customers++;
                }
                ?>
                <div class="stat-card primary">
                    <div class="stat-number"><?php echo $total_customers; ?></div>
                    <div class="stat-label">Tổng khách hàng</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number"><?php echo $active_customers; ?></div>
                    <div class="stat-label">Đã mua hàng</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo $vip_customers; ?></div>
                    <div class="stat-label">Khách VIP (>5M)</div>
                </div>
            </div>
            
            <div class="content-section">
                <div class="controls">
                    <div class="search-box">
                        <form method="GET" style="display: flex; gap: 10px;">
                            <input type="text" name="search" placeholder="Tìm tên, mã KH, email, SĐT..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </form>
                    </div>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã KH</th>
                                <th>Họ tên</th>
                                <th>Liên hệ</th>
                                <th>Địa chỉ</th>
                                <th>Tổng đơn</th>
                                <th>Tổng chi tiêu</th>
                                <th>Hạng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                        Không có khách hàng nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['makh']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['hoten']); ?></td>
                                        <td>
                                            <?php if ($customer['sodt']): ?>
                                                <div><?php echo htmlspecialchars($customer['sodt']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($customer['email']): ?>
                                                <div style="font-size: 12px; color: #6c757d;"><?php echo htmlspecialchars($customer['email']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($customer['captinh']): ?>
                                                <?php echo htmlspecialchars($customer['captinh']); ?>
                                            <?php else: ?>
                                                <em style="color: #999;">Chưa cập nhật</em>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $customer['total_orders']; ?> đơn</td>
                                        <td class="price"><?php echo number_format($customer['total_spent'] ?? 0); ?>đ</td>
                                        <td>
                                            <?php
                                            $spent = $customer['total_spent'] ?? 0;
                                            if ($spent >= 10000000) {
                                                echo '<span class="customer-level level-platinum">Platinum</span>';
                                            } elseif ($spent >= 5000000) {
                                                echo '<span class="customer-level level-gold">Gold</span>';
                                            } elseif ($spent >= 1000000) {
                                                echo '<span class="customer-level level-silver">Silver</span>';
                                            } else {
                                                echo '<span class="customer-level level-bronze">Bronze</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="?view=<?php echo $customer['makh']; ?>" class="btn btn-info">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal chi tiết khách hàng -->
    <?php if (isset($_GET['view']) && $customer_info): ?>
        <div id="customerModal" class="modal" style="display: block;">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Thông tin khách hàng: <?php echo htmlspecialchars($customer_info['hoten']); ?></h2>
                
                <div class="customer-details">
                    <div class="detail-item">
                        <div class="detail-label">Mã khách hàng</div>
                        <div class="detail-value"><?php echo htmlspecialchars($customer_info['makh']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Họ và tên</div>
                        <div class="detail-value"><?php echo htmlspecialchars($customer_info['hoten']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Số điện thoại</div>
                        <div class="detail-value"><?php echo $customer_info['sodt'] ? htmlspecialchars($customer_info['sodt']) : 'Chưa cập nhật'; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?php echo $customer_info['email'] ? htmlspecialchars($customer_info['email']) : 'Chưa cập nhật'; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Ngày sinh</div>
                        <div class="detail-value"><?php echo $customer_info['ngaysinh'] ? date('d/m/Y', strtotime($customer_info['ngaysinh'])) : 'Chưa cập nhật'; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Địa chỉ</div>
                        <div class="detail-value">
                            <?php 
                            $address_parts = array_filter([$customer_info['sonha'], $customer_info['capxa'], $customer_info['caphuyen'], $customer_info['captinh']]);
                            echo !empty($address_parts) ? implode(', ', $address_parts) : 'Chưa cập nhật';
                            ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Tổng đơn hàng</div>
                        <div class="detail-value"><?php echo $customer_info['total_orders']; ?> đơn</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Tổng chi tiêu</div>
                        <div class="detail-value price"><?php echo number_format($customer_info['total_spent'] ?? 0); ?>đ</div>
                    </div>
                </div>
                
                <!-- Lịch sử đơn hàng -->
                <div class="orders-section">
                    <h3 class="section-title">Lịch sử đơn hàng</h3>
                    <?php if (empty($customer_orders)): ?>
                        <p style="text-align: center; color: #7f8c8d; padding: 20px;">Khách hàng chưa có đơn hàng nào</p>
                    <?php else: ?>
                        <?php foreach ($customer_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-info">
                                    <div class="order-id">Đơn hàng #<?php echo htmlspecialchars($order['soHD']); ?></div>
                                    <div class="order-date">
                                        <?php echo date('d/m/Y', strtotime($order['ngayHD'])); ?> • 
                                        <?php echo $order['total_items']; ?> sản phẩm
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <?php
                                    $status_class = 'status-info';
                                    if (strpos($order['trangthai'], 'Đã giao') !== false) {
                                        $status_class = 'status-success';
                                    } elseif (strpos($order['trangthai'], 'Hủy') !== false) {
                                        $status_class = 'status-danger';
                                    } elseif (strpos($order['trangthai'], 'Đã đặt') !== false) {
                                        $status_class = 'status-warning';
                                    }
                                    ?>
                                    <span class="order-status <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($order['trangthai']); ?>
                                    </span>
                                    <div class="price"><?php echo number_format($order['trigia']); ?>đ</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 30px; text-align: right;">
                    <button onclick="closeModal()" class="btn btn-primary">Đóng</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <script>
        function closeModal() {
            window.location.href = 'customers.php';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            var modal = document.getElementById('customerModal');
            if (modal && event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 