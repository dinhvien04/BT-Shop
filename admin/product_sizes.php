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

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM sanpham WHERE masp = '$masp'";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    header('Location: products.php');
    exit;
}
$product = $result->fetch_assoc();

// Xử lý thêm size
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $size = $conn->real_escape_string($_POST['size']);
    $soluong = intval($_POST['soluong']);
    $gia = floatval($_POST['gia']);
    
    $sql = "INSERT INTO size_sanpham (masp, size, soluong, gia) VALUES ('$masp', '$size', $soluong, $gia)";
    if ($conn->query($sql)) {
        $success = "Thêm size thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Xử lý xóa size
if (isset($_GET['delete_size'])) {
    $size = $conn->real_escape_string($_GET['delete_size']);
    $sql = "DELETE FROM size_sanpham WHERE masp = '$masp' AND size = '$size'";
    if ($conn->query($sql)) {
        $success = "Xóa size thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Xử lý cập nhật size
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $size = $conn->real_escape_string($_POST['size']);
    $soluong = intval($_POST['soluong']);
    $gia = floatval($_POST['gia']);
    
    $sql = "UPDATE size_sanpham SET soluong = $soluong, gia = $gia WHERE masp = '$masp' AND size = '$size'";
    if ($conn->query($sql)) {
        $success = "Cập nhật size thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách size
$sql = "SELECT * FROM size_sanpham WHERE masp = '$masp' ORDER BY size ASC";
$result = $conn->query($sql);
$sizes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sizes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Size - <?php echo htmlspecialchars($product['tensp']); ?></title>
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
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            color: #3498db;
            text-decoration: none;
            margin-bottom: 15px;
        }
        
        .back-link:hover {
            color: #2980b9;
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
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
        
        .price {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .stock {
            font-weight: bold;
        }
        
        .stock.low {
            color: #e74c3c;
        }
        
        .stock.medium {
            color: #f39c12;
        }
        
        .stock.high {
            color: #27ae60;
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
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
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
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="products.php" class="back-link">
                <i class="ti-arrow-left"></i>
                Quay lại danh sách sản phẩm
            </a>
            <h1>Quản lý Size & Số lượng</h1>
            
            <div class="product-info">
                <img src="../<?php echo htmlspecialchars($product['url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['tensp']); ?>" 
                     class="product-image"
                     onerror="this.src='../accsets/images/no-image.png'">
                <div>
                    <h3><?php echo htmlspecialchars($product['tensp']); ?></h3>
                    <p>Mã: <?php echo htmlspecialchars($product['masp']); ?> | Danh mục: <?php echo htmlspecialchars($product['danhmuc']); ?></p>
                </div>
            </div>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Form thêm size -->
        <div class="content-section">
            <h3>Thêm Size mới</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label for="size">Size:</label>
                        <input type="text" id="size" name="size" placeholder="VD: M, L, 40, 41..." required>
                    </div>
                    <div class="form-group">
                        <label for="soluong">Số lượng:</label>
                        <input type="number" id="soluong" name="soluong" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="gia">Giá:</label>
                        <input type="number" id="gia" name="gia" min="0" value="<?php echo $product['gia']; ?>" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Thêm Size</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Danh sách size -->
        <div class="content-section">
            <h3>Danh sách Size hiện có</h3>
            
            <?php if (empty($sizes)): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 40px;">
                    Chưa có size nào được thêm cho sản phẩm này
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sizes as $size): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($size['size']); ?></strong></td>
                                <td>
                                    <?php
                                    $stockClass = 'high';
                                    if ($size['soluong'] <= 5) $stockClass = 'low';
                                    elseif ($size['soluong'] <= 15) $stockClass = 'medium';
                                    ?>
                                    <span class="stock <?php echo $stockClass; ?>">
                                        <?php echo $size['soluong']; ?>
                                    </span>
                                </td>
                                <td class="price"><?php echo number_format($size['gia']); ?>đ</td>
                                <td>
                                    <?php if ($size['soluong'] <= 0): ?>
                                        <span style="color: #e74c3c;">Hết hàng</span>
                                    <?php elseif ($size['soluong'] <= 5): ?>
                                        <span style="color: #f39c12;">Sắp hết</span>
                                    <?php else: ?>
                                        <span style="color: #27ae60;">Còn hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="editSize('<?php echo $size['size']; ?>', <?php echo $size['soluong']; ?>, <?php echo $size['gia']; ?>)" 
                                            class="btn btn-success">Sửa</button>
                                    <a href="?id=<?php echo $masp; ?>&delete_size=<?php echo $size['size']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Bạn có chắc muốn xóa size này?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal sửa size -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Sửa thông tin Size</h3>
            
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_size" name="size">
                
                <div class="form-group">
                    <label>Size:</label>
                    <input type="text" id="edit_size_display" disabled>
                </div>
                
                <div class="form-group">
                    <label for="edit_soluong">Số lượng:</label>
                    <input type="number" id="edit_soluong" name="soluong" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_gia">Giá:</label>
                    <input type="number" id="edit_gia" name="gia" min="0" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
    
    <script>
        function editSize(size, soluong, gia) {
            document.getElementById('edit_size').value = size;
            document.getElementById('edit_size_display').value = size;
            document.getElementById('edit_soluong').value = soluong;
            document.getElementById('edit_gia').value = gia;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 