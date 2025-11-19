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

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $soHD = $_POST['soHD'];
    $trangthai = $_POST['trangthai'];
    
    $sql = "UPDATE hoadon SET trangthai = '$trangthai' WHERE soHD = '$soHD'";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}

// Lấy danh sách đơn hàng
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$whereClause = "WHERE 1=1";
if ($search) {
    $whereClause .= " AND (hd.soHD LIKE '%$search%' OR kh.hoten LIKE '%$search%')";
}
if ($status_filter) {
    $whereClause .= " AND hd.trangthai LIKE '%$status_filter%'";
}

$sql = "SELECT hd.*, kh.hoten, kh.makh 
        FROM hoadon hd 
        LEFT JOIN khachhang kh ON hd.makh = kh.makh 
        $whereClause 
        ORDER BY hd.ngayHD DESC, hd.soHD DESC";

$result = mysqli_query($conn, $sql);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Lấy chi tiết đơn hàng cho modal
$order_details = [];
if (isset($_GET['view'])) {
    $soHD = $_GET['view'];
    $sql_details = "
        SELECT ct.*, sp.tensp, sp.url 
        FROM chitiethoadon ct 
        JOIN sanpham sp ON ct.masp = sp.masp 
        WHERE ct.soHD = '$soHD'
    ";
    $result_details = mysqli_query($conn, $sql_details);
    while ($row = mysqli_fetch_assoc($result_details)) {
        $order_details[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - BT Shop Admin</title>
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
            flex-wrap: wrap;
        }
        
        .filters {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box input,
        .status-filter select {
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
        
        .btn-success {
            background: #28a745;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .btn-success:hover {
            background: #218838;
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
        
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
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
        
        .price {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .actions {
            display: flex;
            gap: 5px;
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
            max-width: 800px;
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
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #2c3e50;
            font-size: 16px;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        
        .product-details {
            flex: 1;
        }
        
        .product-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .product-specs {
            color: #6c757d;
            font-size: 14px;
        }
        
        .product-price {
            text-align: right;
            color: #e74c3c;
            font-weight: bold;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
                <li><a href="orders.php" class="active"><i class="ti-shopping-cart"></i>Quản lý đơn hàng</a></li>
                <li><a href="customers.php"><i class="ti-user"></i>Quản lý khách hàng</a></li>
                <li><a href="reports.php"><i class="ti-bar-chart"></i>Báo cáo thống kê</a></li>
                <li><a href="settings.php"><i class="ti-settings"></i>Cài đặt</a></li>
                <li><a href="../logout.php"><i class="ti-power-off"></i>Đăng xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Quản lý đơn hàng</h1>
                <p>Theo dõi và xử lý tất cả đơn hàng</p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <div class="controls">
                    <div class="filters">
                        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="search" placeholder="Tìm mã đơn hoặc tên khách..." value="<?php echo htmlspecialchars($search); ?>" class="search-box">
                            
                            <select name="status" class="status-filter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đã đặt hàng" <?php echo $status_filter == 'Đã đặt hàng' ? 'selected' : ''; ?>>Đã đặt hàng</option>
                                <option value="Đã giao" <?php echo $status_filter == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                                <option value="Hủy" <?php echo $status_filter == 'Hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                <option value="Mua ngay" <?php echo $status_filter == 'Mua ngay' ? 'selected' : ''; ?>>Mua ngay</option>
                            </select>
                            
                            <button type="submit" class="btn btn-primary">Lọc</button>
                        </form>
                    </div>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                        Không có đơn hàng nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['soHD']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($order['ngayHD'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['hoten']); ?></td>
                                        <td class="price"><?php echo number_format($order['trigia']); ?>đ</td>
                                        <td>
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
                                            <span class="status <?php echo $status_class; ?>">
                                                <?php echo htmlspecialchars($order['trangthai']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a href="?view=<?php echo $order['soHD']; ?>" class="btn btn-info">Xem</a>
                                                <button onclick="openStatusModal('<?php echo $order['soHD']; ?>', '<?php echo $order['trangthai']; ?>')" class="btn btn-success">Cập nhật</button>
                                            </div>
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
    
    <!-- Modal xem chi tiết đơn hàng -->
    <?php if (isset($_GET['view']) && !empty($order_details)): ?>
        <?php
        $order_info = null;
        foreach ($orders as $order) {
            if ($order['soHD'] == $_GET['view']) {
                $order_info = $order;
                break;
            }
        }
        ?>
        <div id="detailModal" class="modal" style="display: block;">
            <div class="modal-content">
                <span class="close" onclick="closeDetailModal()">&times;</span>
                <h2>Chi tiết đơn hàng #<?php echo htmlspecialchars($_GET['view']); ?></h2>
                
                <?php if ($order_info): ?>
                    <div class="order-info">
                        <div class="info-item">
                            <div class="info-label">Ngày đặt</div>
                            <div class="info-value"><?php echo date('d/m/Y', strtotime($order_info['ngayHD'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Khách hàng</div>
                            <div class="info-value"><?php echo htmlspecialchars($order_info['hoten']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tổng tiền</div>
                            <div class="info-value price"><?php echo number_format($order_info['trigia']); ?>đ</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Trạng thái</div>
                            <div class="info-value"><?php echo htmlspecialchars($order_info['trangthai']); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <h3>Sản phẩm đã đặt:</h3>
                <div>
                    <?php foreach ($order_details as $detail): ?>
                        <div class="product-item">
                            <img src="../<?php echo htmlspecialchars($detail['url']); ?>" 
                                 alt="<?php echo htmlspecialchars($detail['tensp']); ?>" 
                                 class="product-image"
                                 onerror="this.src='../accsets/images/no-image.png'">
                            <div class="product-details">
                                <div class="product-name"><?php echo htmlspecialchars($detail['tensp']); ?></div>
                                <div class="product-specs">
                                    Size: <?php echo htmlspecialchars($detail['size']); ?> | 
                                    Số lượng: <?php echo $detail['soluong']; ?>
                                </div>
                            </div>
                            <div class="product-price">
                                <?php echo number_format($detail['giaban']); ?>đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button onclick="closeDetailModal()" class="btn btn-primary">Đóng</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Modal cập nhật trạng thái -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h2>Cập nhật trạng thái đơn hàng</h2>
            
            <form method="POST">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="soHD" id="statusOrderId">
                
                <div class="form-group">
                    <label for="trangthai">Trạng thái:</label>
                    <select id="trangthai" name="trangthai" required>
                        <option value="Đã đặt hàng">Đã đặt hàng</option>
                        <option value="Đang xử lý">Đang xử lý</option>
                        <option value="Đang giao hàng">Đang giao hàng</option>
                        <option value="Đã giao">Đã giao</option>
                        <option value="Hủy">Hủy</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
    
    <script>
        function openStatusModal(orderId, currentStatus) {
            document.getElementById('statusOrderId').value = orderId;
            document.getElementById('trangthai').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        function closeDetailModal() {
            window.location.href = 'orders.php';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            var statusModal = document.getElementById('statusModal');
            var detailModal = document.getElementById('detailModal');
            
            if (event.target == statusModal) {
                statusModal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 