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

// Lấy thống kê tổng quan
$stats = [];

// Tổng số khách hàng
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM khachhang WHERE makh NOT IN ('admin', 'manager')");
$stats['khachhang'] = mysqli_fetch_assoc($result)['total'];

// Tổng số sản phẩm
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM sanpham");
$stats['sanpham'] = mysqli_fetch_assoc($result)['total'];

// Tổng số đơn hàng
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM hoadon");
$stats['donhang'] = mysqli_fetch_assoc($result)['total'];

// Tổng doanh thu
$result = mysqli_query($conn, "SELECT SUM(trigia) as total FROM hoadon WHERE trangthai LIKE '%Đã đặt hàng%' OR trangthai LIKE '%Đã giao%'");
$stats['doanhthu'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Đơn hàng hôm nay
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM hoadon WHERE DATE(ngayHD) = CURDATE()");
$stats['donhang_homnay'] = mysqli_fetch_assoc($result)['total'];

// Sản phẩm bán chạy
$result = mysqli_query($conn, "
    SELECT sp.tensp, SUM(ct.soluong) as total_sold 
    FROM chitiethoadon ct 
    JOIN sanpham sp ON ct.masp = sp.masp 
    JOIN hoadon hd ON ct.soHD = hd.soHD 
    WHERE hd.trangthai LIKE '%Đã đặt hàng%' OR hd.trangthai LIKE '%Đã giao%'
    GROUP BY ct.masp 
    ORDER BY total_sold DESC 
    LIMIT 5
");
$bestsellers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bestsellers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BT Shop</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome {
            font-size: 24px;
            color: #2c3e50;
        }
        
        .user-info {
            color: #7f8c8d;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card.primary { border-left: 5px solid #3498db; }
        .stat-card.success { border-left: 5px solid #2ecc71; }
        .stat-card.warning { border-left: 5px solid #f39c12; }
        .stat-card.danger { border-left: 5px solid #e74c3c; }
        .stat-card.info { border-left: 5px solid #9b59b6; }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-card.primary .stat-number { color: #3498db; }
        .stat-card.success .stat-number { color: #2ecc71; }
        .stat-card.warning .stat-number { color: #f39c12; }
        .stat-card.danger .stat-number { color: #e74c3c; }
        .stat-card.info .stat-number { color: #9b59b6; }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        /* Bestsellers */
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .bestseller-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .bestseller-item:last-child {
            border-bottom: none;
        }
        
        .product-name {
            color: #2c3e50;
        }
        
        .sold-count {
            color: #27ae60;
            font-weight: bold;
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
                <li><a href="dashboard.php" class="active"><i class="ti-home"></i>Dashboard</a></li>
                <li><a href="products.php"><i class="ti-package"></i>Quản lý sản phẩm</a></li>
                <li><a href="orders.php"><i class="ti-shopping-cart"></i>Quản lý đơn hàng</a></li>
                <li><a href="customers.php"><i class="ti-user"></i>Quản lý khách hàng</a></li>
                <li><a href="reports.php"><i class="ti-bar-chart"></i>Báo cáo thống kê</a></li>
                <li><a href="settings.php"><i class="ti-settings"></i>Cài đặt</a></li>
                <li><a href="../logout.php"><i class="ti-power-off"></i>Đăng xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1 class="welcome">Chào mừng, <?php echo $_SESSION['name']; ?>!</h1>
                    <p class="user-info">Vai trò: <?php echo ucfirst($_SESSION['vaitro']); ?> | Hôm nay: <?php echo date('d/m/Y'); ?></p>
                </div>
                <a href="../logout.php" class="logout-btn">Đăng xuất</a>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-number"><?php echo number_format($stats['khachhang']); ?></div>
                    <div class="stat-label">Khách hàng</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number"><?php echo number_format($stats['sanpham']); ?></div>
                    <div class="stat-label">Sản phẩm</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo number_format($stats['donhang']); ?></div>
                    <div class="stat-label">Đơn hàng</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo number_format($stats['doanhthu']); ?>đ</div>
                    <div class="stat-label">Doanh thu</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-number"><?php echo number_format($stats['donhang_homnay']); ?></div>
                    <div class="stat-label">Đơn hàng hôm nay</div>
                </div>
            </div>
            
            <!-- Bestsellers -->
            <div class="content-section">
                <h2 class="section-title">Sản phẩm bán chạy nhất</h2>
                <?php if (empty($bestsellers)): ?>
                    <p style="text-align: center; color: #7f8c8d; padding: 20px;">Chưa có dữ liệu bán hàng</p>
                <?php else: ?>
                    <?php foreach ($bestsellers as $item): ?>
                        <div class="bestseller-item">
                            <span class="product-name"><?php echo htmlspecialchars($item['tensp']); ?></span>
                            <span class="sold-count">Đã bán: <?php echo $item['total_sold']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 