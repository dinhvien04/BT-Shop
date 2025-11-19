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

// Lấy mã sản phẩm từ URL
$masp = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
if (empty($masp)) {
    header('Location: products.php');
    exit;
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tensp = $conn->real_escape_string($_POST['tensp']);
    $danhmuc = $conn->real_escape_string($_POST['danhmuc']);
    $kieu = $conn->real_escape_string($_POST['kieu']);
    $nuocsx = $conn->real_escape_string($_POST['nuocsx']);
    $gia = floatval($_POST['gia']);
    $mota = $conn->real_escape_string($_POST['mota']);
    
    $sql = "UPDATE sanpham SET 
            tensp = '$tensp',
            danhmuc = '$danhmuc',
            kieu = '$kieu',
            nuocsx = '$nuocsx',
            gia = $gia,
            mota = '$mota'
            WHERE masp = '$masp'";
    
    if ($conn->query($sql)) {
        $success = "Cập nhật sản phẩm thành công!";
        header('Location: products.php?success=1');
        exit;
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Lấy thông tin sản phẩm hiện tại
$sql = "SELECT * FROM sanpham WHERE masp = '$masp'";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    header('Location: products.php');
    exit;
}
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm - BT Shop Admin</title>
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
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
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s;
            margin-right: 10px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
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
        
        .back-link {
            display: inline-flex;
            align-items: center;
            color: #3498db;
            text-decoration: none;
            margin-bottom: 20px;
        }
        
        .back-link:hover {
            color: #2980b9;
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .product-image-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
            margin-top: 10px;
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
                <a href="products.php" class="back-link">
                    <i class="ti-arrow-left"></i>
                    Quay lại danh sách sản phẩm
                </a>
                <h1>Sửa sản phẩm: <?php echo htmlspecialchars($product['tensp']); ?></h1>
                <p>Chỉnh sửa thông tin sản phẩm</p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-section">
                <form method="POST">
                    <div class="form-group">
                        <label>Mã sản phẩm:</label>
                        <input type="text" value="<?php echo htmlspecialchars($product['masp']); ?>" disabled>
                        <small style="color: #6c757d;">Mã sản phẩm không thể thay đổi</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="tensp">Tên sản phẩm: <span style="color: red;">*</span></label>
                        <input type="text" id="tensp" name="tensp" value="<?php echo htmlspecialchars($product['tensp']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="danhmuc">Danh mục: <span style="color: red;">*</span></label>
                            <select id="danhmuc" name="danhmuc" required>
                                <option value="">Chọn danh mục</option>
                                <option value="Áo" <?php echo $product['danhmuc'] == 'Áo' ? 'selected' : ''; ?>>Áo</option>
                                <option value="Quần" <?php echo $product['danhmuc'] == 'Quần' ? 'selected' : ''; ?>>Quần</option>
                                <option value="Giày" <?php echo $product['danhmuc'] == 'Giày' ? 'selected' : ''; ?>>Giày</option>
                                <option value="Mắt kính" <?php echo $product['danhmuc'] == 'Mắt kính' ? 'selected' : ''; ?>>Mắt kính</option>
                                <option value="Đồ bộ" <?php echo $product['danhmuc'] == 'Đồ bộ' ? 'selected' : ''; ?>>Đồ bộ</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="kieu">Kiểu:</label>
                            <input type="text" id="kieu" name="kieu" value="<?php echo htmlspecialchars($product['kieu']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nuocsx">Nước sản xuất:</label>
                            <input type="text" id="nuocsx" name="nuocsx" value="<?php echo htmlspecialchars($product['nuocsx']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="gia">Giá: <span style="color: red;">*</span></label>
                            <input type="number" id="gia" name="gia" value="<?php echo $product['gia']; ?>" required min="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mota">Mô tả:</label>
                        <textarea id="mota" name="mota" placeholder="Mô tả chi tiết về sản phẩm..."><?php echo htmlspecialchars($product['mota']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Hình ảnh hiện tại:</label>
                        <?php if (!empty($product['url'])): ?>
                            <br>
                            <img src="../<?php echo htmlspecialchars($product['url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['tensp']); ?>" 
                                 class="product-image-preview"
                                 onerror="this.src='../accsets/images/no-image.png'">
                        <?php else: ?>
                            <p style="color: #6c757d;">Chưa có hình ảnh</p>
                        <?php endif; ?>
                        <small style="color: #6c757d; display: block; margin-top: 5px;">
                            Để thay đổi hình ảnh, vui lòng thêm file ảnh có tên "<?php echo $product['masp']; ?>.png" vào thư mục accsets/images/products/
                        </small>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti-save"></i> Cập nhật sản phẩm
                        </button>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="ti-close"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 