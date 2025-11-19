<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$makh = $_SESSION['username'];

// Kết nối CSDL
require 'connect.php';

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn đơn hàng và sản phẩm
$sql = "
    SELECT hd.soHD, hd.ngayHD, hd.trangthai, hd.trigia, sp.tensp, sp.url, cthd.size, cthd.soluong,
           cthd.giaban, sp.masp
    FROM hoadon hd
    JOIN chitiethoadon cthd ON hd.soHD = cthd.soHD
    JOIN sanpham sp ON cthd.masp = sp.masp
    WHERE hd.makh = ?
    ORDER BY hd.soHD DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $makh);
$stmt->execute();
$result = $stmt->get_result();

// Gom đơn hàng
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['soHD']]['ngayHD'] = $row['ngayHD'];
    $orders[$row['soHD']]['trangthai'] = $row['trangthai'];
    $orders[$row['soHD']]['trigia'] = $row['trigia'];
    $orders[$row['soHD']]['items'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn hàng của tôi</title>
    <link rel="stylesheet" href="./accsets/css/base.css">
    <link rel="stylesheet" href="./accsets/css/table.css">
    <link rel="stylesheet" href="./accsets/css/main.css">
    <link rel="stylesheet" href="./accsets/fonts/themify-icons/themify-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .status-success {
            background: #28a745;
        }
        
        .status-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .status-danger {
            background: #dc3545;
        }
        
        .status-info {
            background: #17a2b8;
        }
        
        .status-processing {
            background: #6f42c1;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .order-header span:first-child {
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <?php 
    require 'site.php'; 
    load_top();
    load_backbtn(); 
    ?>
    <div class="orders-container">
        <h2>Đơn hàng của tôi</h2>

        <?php if (empty($orders)): ?>
            <p>Bạn chưa có sản phẩm nào được mua.</p>
            <?php else: ?>
            <?php foreach ($orders as $soHD => $order): ?>
                <div class="order-card">
                                    <div class="order-header">
                    <span>Đơn hàng #<?= $soHD ?> | Ngày: <?= $order['ngayHD'] ?></span>
                    <?php
                    $statusClass = 'status-info';
                    $trangthai = $order['trangthai'];
                    if (strpos($trangthai, 'Đã giao') !== false) {
                        $statusClass = 'status-success';
                    } elseif (strpos($trangthai, 'Hủy') !== false) {
                        $statusClass = 'status-danger';
                    } elseif (strpos($trangthai, 'Đã đặt') !== false) {
                        $statusClass = 'status-warning';
                    } elseif (strpos($trangthai, 'Đang') !== false) {
                        $statusClass = 'status-processing';
                    }
                    ?>
                    <span class="status <?= $statusClass ?>"><?= htmlspecialchars($trangthai) ?></span>
                </div>

                    <?php $tong = 0; ?>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="item">
                            <img src="<?= $item['url'] ?>" alt="<?= $item['tensp'] ?>">
                            <div class="info">
                                <div><?= $item['tensp'] ?></div>
                                <div>Phân loại: Size <?= $item['size'] ?> x <?= $item['soluong'] ?></div>
                                <div>Đơn giá: <?= number_format($item['giaban'], 0, ',', '.') ?>₫</div>                            
                            </div>
                            <div class="price"><?= number_format($item['giaban'] * $item['soluong'], 0, ',', '.') ?>₫</div>
                        </div>
                        <?php $tong += $item['giaban'] * $item['soluong']; ?>
                    <?php endforeach; ?>

                    <div class="footer">
                        <span>Thành tiền: <strong><?= number_format($tong, 0, ',', '.') ?>₫</strong></span>
                        <?php 
                        // Chỉ hiển thị nút hủy nếu đơn hàng chưa giao và chưa hủy
                        if (strpos($order['trangthai'], 'Đã giao') === false && strpos($order['trangthai'], 'Hủy') === false): 
                        ?>
                            <form method="GET" action="remove-order.php">
                                <input type="hidden" name="soHD" value="<?= $soHD ?>">
                                <button class="btn" type="submit">Hủy đơn hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
    $stmt->close();
    $conn->close();
    load_footer();
    ?>
</body>
</html>

