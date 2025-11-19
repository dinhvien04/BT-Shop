<?php
session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['username']) || $_SESSION['vaitro'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../connect.php';
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $masp = $conn->real_escape_string($_POST['masp']);
    $tensp = $conn->real_escape_string($_POST['tensp']);
    $danhmuc = $conn->real_escape_string($_POST['danhmuc']);
    $kieu = $conn->real_escape_string($_POST['kieu']);
    $nuocsx = $conn->real_escape_string($_POST['nuocsx']);
    $gia = floatval($_POST['gia']);
    $mota = $conn->real_escape_string($_POST['mota']);
    
    $sql = "INSERT INTO sanpham (masp, tensp, danhmuc, kieu, nuocsx, gia, mota) 
            VALUES ('$masp', '$tensp', '$danhmuc', '$kieu', '$nuocsx', $gia, '$mota')";
    
    if ($conn->query($sql)) {
        $success = "Thêm sản phẩm thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $masp = $conn->real_escape_string($_GET['delete']);
    
    // Kiểm tra xem sản phẩm có trong đơn hàng không
    $check_sql = "SELECT COUNT(*) as count FROM chitiethoadon WHERE masp = '$masp'";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['count'] > 0) {
        $error = "Không thể xóa sản phẩm này vì đã có trong đơn hàng!";
    } else {
        // Xóa từ size_sanpham trước
        $conn->query("DELETE FROM size_sanpham WHERE masp = '$masp'");
        
        // Sau đó xóa sản phẩm
        $sql = "DELETE FROM sanpham WHERE masp = '$masp'";
        if ($conn->query($sql)) {
            $success = "Xóa sản phẩm thành công!";
        } else {
            $error = "Không thể xóa sản phẩm: " . $conn->error;
        }
    }
}

// Lấy danh sách sản phẩm
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$whereClause = '';
if ($search) {
    $whereClause = "WHERE tensp LIKE '%$search%' OR masp LIKE '%$search%' OR danhmuc LIKE '%$search%'";
}

$sql = "SELECT * FROM sanpham $whereClause ORDER BY masp ASC";
$result = $conn->query($sql);
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - BT Shop Admin</title>
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
        
        .section-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
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
        
        .btn-danger {
            background: #e74c3c;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .btn-success:hover {
            background: #229954;
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
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
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
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
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
                <li><a href="products.php" class="active"><i class="ti-package"></i>Quản lý sản phẩm</a></li>
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
                <h1>Quản lý sản phẩm</h1>
                <p>Quản lý toàn bộ sản phẩm trong cửa hàng</p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                <div class="alert alert-success">Cập nhật sản phẩm thành công!</div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <div class="controls">
                    <div class="search-box">
                        <form method="GET" style="display: flex; gap: 10px;">
                            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </form>
                    </div>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="ti-plus"></i> Thêm sản phẩm
                    </button>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SP</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Kiểu</th>
                                <th>Nước SX</th>
                                <th>Giá</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                        Không có sản phẩm nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['masp']); ?></td>
                                        <td>
                                            <img src="../<?php echo htmlspecialchars($product['url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['tensp']); ?>" 
                                                 class="product-image" 
                                                 onerror="this.src='../accsets/images/no-image.png'">
                                        </td>
                                        <td><?php echo htmlspecialchars($product['tensp']); ?></td>
                                        <td><?php echo htmlspecialchars($product['danhmuc']); ?></td>
                                        <td><?php echo htmlspecialchars($product['kieu']); ?></td>
                                        <td><?php echo htmlspecialchars($product['nuocsx']); ?></td>
                                        <td class="price"><?php echo number_format($product['gia']); ?>đ</td>
                                        <td>
                                            <div class="actions">
                                                <a href="edit_product.php?id=<?php echo $product['masp']; ?>" class="btn btn-success">Sửa</a>
                                                <a href="product_sizes.php?id=<?php echo $product['masp']; ?>" class="btn" style="background: #9b59b6; color: white; font-size: 12px; padding: 5px 10px;">Size</a>
                                                <a href="?delete=<?php echo $product['masp']; ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
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
    
    <!-- Modal thêm sản phẩm -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Thêm sản phẩm mới</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="masp">Mã sản phẩm:</label>
                    <input type="text" id="masp" name="masp" required>
                </div>
                
                <div class="form-group">
                    <label for="tensp">Tên sản phẩm:</label>
                    <input type="text" id="tensp" name="tensp" required>
                </div>
                
                <div class="form-group">
                    <label for="danhmuc">Danh mục:</label>
                    <select id="danhmuc" name="danhmuc" required>
                        <option value="">Chọn danh mục</option>
                        <option value="Áo">Áo</option>
                        <option value="Quần">Quần</option>
                        <option value="Giày">Giày</option>
                        <option value="Mắt kính">Mắt kính</option>
                        <option value="Đồ bộ">Đồ bộ</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="kieu">Kiểu:</label>
                    <input type="text" id="kieu" name="kieu">
                </div>
                
                <div class="form-group">
                    <label for="nuocsx">Nước sản xuất:</label>
                    <input type="text" id="nuocsx" name="nuocsx">
                </div>
                
                <div class="form-group">
                    <label for="gia">Giá:</label>
                    <input type="number" id="gia" name="gia" required>
                </div>
                
                <div class="form-group">
                    <label for="mota">Mô tả:</label>
                    <textarea id="mota" name="mota"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            var modal = document.getElementById('addModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 