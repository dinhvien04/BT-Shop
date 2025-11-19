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

// Xử lý filter
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'today';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Xây dựng WHERE clause dựa trên filter
$where_clause = "";
switch($date_filter) {
    case 'today':
        $where_clause = "DATE(ngayHD) = CURDATE()";
        break;
    case 'week':
        $where_clause = "YEARWEEK(ngayHD, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $where_clause = "YEAR(ngayHD) = YEAR(CURDATE()) AND MONTH(ngayHD) = MONTH(CURDATE())";
        break;
    case 'year':
        $where_clause = "YEAR(ngayHD) = YEAR(CURDATE())";
        break;
    case 'custom':
        $where_clause = "DATE(ngayHD) BETWEEN '$start_date' AND '$end_date'";
        break;
    default:
        $where_clause = "1=1";
}

// Báo cáo doanh thu
$revenue_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(trigia) as total_revenue,
    AVG(trigia) as avg_order_value
    FROM hoadon 
    WHERE $where_clause AND (trangthai LIKE '%Đã đặt hàng%' OR trangthai LIKE '%Đã giao%')";

$revenue_result = mysqli_query($conn, $revenue_query);
$revenue_stats = mysqli_fetch_assoc($revenue_result);

// Doanh thu theo ngày (7 ngày gần nhất)
$daily_revenue_query = "SELECT 
    DATE(ngayHD) as date,
    COUNT(*) as orders,
    SUM(trigia) as revenue
    FROM hoadon 
    WHERE DATE(ngayHD) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND (trangthai LIKE '%Đã đặt hàng%' OR trangthai LIKE '%Đã giao%')
    GROUP BY DATE(ngayHD)
    ORDER BY DATE(ngayHD) DESC";

$daily_revenue = mysqli_query($conn, $daily_revenue_query);

// Top 10 sản phẩm bán chạy
$bestsellers_query = "SELECT 
    sp.tensp,
    sp.gia,
    SUM(ct.soluong) as total_sold,
    SUM(ct.soluong * sp.gia) as total_revenue
    FROM chitiethoadon ct 
    JOIN sanpham sp ON ct.masp = sp.masp 
    JOIN hoadon hd ON ct.soHD = hd.soHD 
    WHERE $where_clause AND (hd.trangthai LIKE '%Đã đặt hàng%' OR hd.trangthai LIKE '%Đã giao%')
    GROUP BY ct.masp 
    ORDER BY total_sold DESC 
    LIMIT 10";

$bestsellers = mysqli_query($conn, $bestsellers_query);

// Báo cáo theo trạng thái đơn hàng
$status_query = "SELECT 
    trangthai,
    COUNT(*) as count,
    SUM(trigia) as total
    FROM hoadon 
    WHERE $where_clause
    GROUP BY trangthai
    ORDER BY count DESC";

$status_stats = mysqli_query($conn, $status_query);

// Top khách hàng mua nhiều nhất
$top_customers_query = "SELECT 
    kh.hoten,
    COALESCE(ttlh.email, 'Chưa cập nhật') as email,
    COUNT(hd.soHD) as total_orders,
    SUM(hd.trigia) as total_spent
    FROM khachhang kh
    JOIN hoadon hd ON kh.makh = hd.makh
    LEFT JOIN thongtin_lienhe ttlh ON kh.makh = ttlh.makh
    WHERE $where_clause AND (hd.trangthai LIKE '%Đã đặt hàng%' OR hd.trangthai LIKE '%Đã giao%')
    GROUP BY kh.makh
    ORDER BY total_spent DESC
    LIMIT 10";

$top_customers = mysqli_query($conn, $top_customers_query);

// Thống kê theo tháng (12 tháng gần nhất)
$monthly_stats_query = "SELECT 
    YEAR(ngayHD) as year,
    MONTH(ngayHD) as month,
    COUNT(*) as orders,
    SUM(trigia) as revenue
    FROM hoadon 
    WHERE ngayHD >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    AND (trangthai LIKE '%Đã đặt hàng%' OR trangthai LIKE '%Đã giao%')
    GROUP BY YEAR(ngayHD), MONTH(ngayHD)
    ORDER BY year DESC, month DESC";

$monthly_stats = mysqli_query($conn, $monthly_stats_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - BT Shop Admin</title>
    <link rel="stylesheet" href="../accsets/css/base.css">
    <link rel="stylesheet" href="../accsets/fonts/themify-icons/themify-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group select,
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn-filter {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            height: fit-content;
        }
        
        .btn-filter:hover {
            background: #2980b9;
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
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        /* Report Sections */
        .report-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        /* Charts */
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
        
        /* Tables */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .report-table th,
        .report-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .report-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .report-table tr:hover {
            background: #f8f9fa;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        
        .money {
            font-weight: 600;
            color: #27ae60;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .stats-grid {
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
                <li><a href="reports.php" class="active"><i class="ti-bar-chart"></i>Báo cáo thống kê</a></li>
                <li><a href="settings.php"><i class="ti-settings"></i>Cài đặt</a></li>
                <li><a href="../logout.php"><i class="ti-power-off"></i>Đăng xuất</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">Báo cáo bán hàng</h1>
                <p>Thống kê chi tiết về doanh thu và hoạt động kinh doanh</p>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label>Thời gian</label>
                        <select name="date_filter" id="dateFilter">
                            <option value="today" <?= $date_filter == 'today' ? 'selected' : '' ?>>Hôm nay</option>
                            <option value="week" <?= $date_filter == 'week' ? 'selected' : '' ?>>Tuần này</option>
                            <option value="month" <?= $date_filter == 'month' ? 'selected' : '' ?>>Tháng này</option>
                            <option value="year" <?= $date_filter == 'year' ? 'selected' : '' ?>>Năm này</option>
                            <option value="custom" <?= $date_filter == 'custom' ? 'selected' : '' ?>>Tùy chọn</option>
                        </select>
                    </div>
                    <div class="form-group" id="customDateRange" style="display: <?= $date_filter == 'custom' ? 'flex' : 'none' ?>;">
                        <label>Từ ngày</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>">
                    </div>
                    <div class="form-group" id="customDateRange2" style="display: <?= $date_filter == 'custom' ? 'flex' : 'none' ?>;">
                        <label>Đến ngày</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>">
                    </div>
                    <button type="submit" class="btn-filter">Lọc</button>
                </form>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-number"><?= number_format($revenue_stats['total_orders'] ?? 0) ?></div>
                    <div class="stat-label">Tổng đơn hàng</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number"><?= number_format($revenue_stats['total_revenue'] ?? 0) ?>đ</div>
                    <div class="stat-label">Tổng doanh thu</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number"><?= number_format($revenue_stats['avg_order_value'] ?? 0) ?>đ</div>
                    <div class="stat-label">Giá trị đơn hàng TB</div>
                </div>
            </div>

            <!-- Daily Revenue Chart -->
            <div class="report-section">
                <h2 class="section-title">Doanh thu 7 ngày gần nhất</h2>
                <div class="chart-container">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="report-section">
                <h2 class="section-title">Doanh thu 12 tháng gần nhất</h2>
                <div class="chart-container">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>

            <!-- Best Sellers -->
            <div class="report-section">
                <h2 class="section-title">Top 10 sản phẩm bán chạy</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Số lượng bán</th>
                            <th class="text-right">Giá</th>
                            <th class="text-right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($row = mysqli_fetch_assoc($bestsellers)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['tensp']) ?></td>
                            <td class="text-center"><?= number_format($row['total_sold']) ?></td>
                            <td class="text-right money"><?= number_format($row['gia']) ?>đ</td>
                            <td class="text-right money"><?= number_format($row['total_revenue']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Status -->
            <div class="report-section">
                <h2 class="section-title">Thống kê theo trạng thái đơn hàng</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Trạng thái</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-right">Tổng giá trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($status_stats)): ?>
                        <tr>
                            <td>
                                <?php
                                $status = $row['trangthai'];
                                $badge_class = 'badge-info';
                                if (strpos($status, 'Đã giao') !== false) $badge_class = 'badge-success';
                                elseif (strpos($status, 'Hủy') !== false) $badge_class = 'badge-danger';
                                elseif (strpos($status, 'Đang') !== false) $badge_class = 'badge-warning';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td class="text-center"><?= number_format($row['count']) ?></td>
                            <td class="text-right money"><?= number_format($row['total']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Customers -->
            <div class="report-section">
                <h2 class="section-title">Top 10 khách hàng VIP</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th class="text-center">Số đơn hàng</th>
                            <th class="text-right">Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($row = mysqli_fetch_assoc($top_customers)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['hoten']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td class="text-center"><?= number_format($row['total_orders']) ?></td>
                            <td class="text-right money"><?= number_format($row['total_spent']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Handle date filter
        document.getElementById('dateFilter').addEventListener('change', function() {
            const customRange = document.getElementById('customDateRange');
            const customRange2 = document.getElementById('customDateRange2');
            if (this.value === 'custom') {
                customRange.style.display = 'flex';
                customRange2.style.display = 'flex';
            } else {
                customRange.style.display = 'none';
                customRange2.style.display = 'none';
            }
        });

        // Daily Revenue Chart
        <?php
        $daily_dates = [];
        $daily_revenues = [];
        $daily_orders = [];
        mysqli_data_seek($daily_revenue, 0);
        while ($row = mysqli_fetch_assoc($daily_revenue)) {
            $daily_dates[] = date('d/m', strtotime($row['date']));
            $daily_revenues[] = $row['revenue'];
            $daily_orders[] = $row['orders'];
        }
        ?>
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_reverse($daily_dates)) ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?= json_encode(array_reverse($daily_revenues)) ?>,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Số đơn hàng',
                    data: <?= json_encode(array_reverse($daily_orders)) ?>,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Doanh thu (VNĐ)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Số đơn hàng'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Monthly Revenue Chart
        <?php
        $monthly_labels = [];
        $monthly_revenues = [];
        $monthly_orders = [];
        mysqli_data_seek($monthly_stats, 0);
        while ($row = mysqli_fetch_assoc($monthly_stats)) {
            $monthly_labels[] = $row['month'] . '/' . $row['year'];
            $monthly_revenues[] = $row['revenue'];
            $monthly_orders[] = $row['orders'];
        }
        ?>
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_reverse($monthly_labels)) ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?= json_encode(array_reverse($monthly_revenues)) ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.8)',
                    borderColor: '#3498db',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (VNĐ)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 