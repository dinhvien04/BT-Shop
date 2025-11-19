-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 09, 2025 lúc 05:20 PM
-- Phiên bản máy phục vụ: 8.0.42
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlbh`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiethoadon`
--

DROP TABLE IF EXISTS `chitiethoadon`;
CREATE TABLE `chitiethoadon` (
  `soHD` char(10) NOT NULL,
  `masp` char(10) NOT NULL,
  `size` varchar(5) NOT NULL,
  `soluong` float DEFAULT NULL,
  `giaban` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitiethoadon`
--

INSERT INTO `chitiethoadon` (`soHD`, `masp`, `size`, `soluong`, `giaban`) VALUES
('HD014', 'SP001', 'L', 1, 199000),
('HD039', 'SP004', 'L', 1, 289000),
('HD042', 'SP013', 'L', 1, 899000),
('HD044', 'SP208', '40', 1, 970000),
('HD047', 'SP001', 'L', 2, 199000),
('HD048', 'SP013', 'L', 1, 899000),
('HD049', 'SP001', 'L', 1, 199000),
('HD050', 'SP001', 'L', 1, 199000),
('HD052', 'SP014', 'L', 1, 499000),
('HD053', 'SP001', 'L', 1, 199000),
('HD054', 'sp300', 'L', 1, 320000),
('HD055', 'SP015', 'L', 1, 759000),
('HD056', 'SP001', 'L', 1, 199000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

DROP TABLE IF EXISTS `giohang`;
CREATE TABLE `giohang` (
  `makh` char(50) NOT NULL,
  `masp` char(10) NOT NULL,
  `size` varchar(5) NOT NULL,
  `soluong` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giohang`
--

INSERT INTO `giohang` (`makh`, `masp`, `size`, `soluong`) VALUES
('vuong', 'SP003', 'M', 1),
('vuong', 'SP301', 'L', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

DROP TABLE IF EXISTS `hoadon`;
CREATE TABLE `hoadon` (
  `soHD` char(10) NOT NULL,
  `ngayHD` date DEFAULT NULL,
  `makh` char(50) DEFAULT NULL,
  `manv` char(10) DEFAULT NULL,
  `trigia` float DEFAULT NULL,
  `trangthai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`soHD`, `ngayHD`, `makh`, `manv`, `trigia`, `trangthai`) VALUES
('HD001', '2025-05-21', 'vuong', 'NV01', 750000, 'Đã giao'),
('HD002', '2025-05-26', 'vuong', 'NV01', 289000, 'Hủy'),
('HD003', '2025-05-26', 'vuong', 'NV01', 149000, 'Đã đặt hàng'),
('HD004', '2025-05-26', 'vuong', 'NV01', 179000, 'Đã đặt hàng'),
('HD005', '2025-05-27', 'vuong', 'NV01', 199000, 'Hủy'),
('HD006', '2025-05-27', 'vuong', 'NV01', 199000, 'Hủy'),
('HD007', '2025-05-28', 'vuong', 'NV01', 199000, 'Đã đặt hàng'),
('HD008', '2025-05-28', 'vuong', 'NV01', 189000, 'Hủy'),
('HD009', '2025-05-28', 'vuong', 'NV01', 899000, 'Hủy'),
('HD010', '2025-05-28', 'vuong', 'NV01', 229000, 'Hủy'),
('HD011', '2025-05-28', 'vuong', 'NV01', 629000, 'Hủy'),
('HD012', '2025-05-28', 'vuong', 'NV01', 199000, 'Hủy'),
('HD013', '2025-05-28', 'vuong', 'NV01', 199000, 'Hủy'),
('HD014', '2025-05-28', 'vuong', 'NV01', 199000, 'Đã đặt hàng'),
('HD016', '2025-06-03', 'vuong', 'NV01', 0, 'Mua bằng giỏ hàng (Hủy)'),
('HD019', '2025-06-05', 'vuong', 'NV01', 0, 'Mua bằng giỏ hàng (Hủy)'),
('HD023', '2025-06-08', 'vuong', 'NV01', 0, 'Mua bằng giỏ hàng (Hủy)'),
('HD024', '2025-06-08', 'vuong', 'NV01', 0, 'Mua bằng giỏ hàng (Hủy)'),
('HD026', '2025-06-08', 'vuong', 'NV01', 0, 'Mua ngay (Hủy)'),
('HD027', '2025-06-08', 'vuong', 'NV01', 867000, 'Mua ngay (Hủy)'),
('HD028', '2025-06-08', 'vuong', 'NV01', 867000, 'Mua ngay (Hủy)'),
('HD029', '2025-06-08', 'vuong', 'NV01', 867000, 'Mua ngay (Hủy)'),
('HD030', '2025-06-08', 'vuong', 'NV01', 867000, 'Mua ngay (Hủy)'),
('HD032', '2025-06-09', 'vuong', 'NV01', 199000, 'Mua bằng giỏ hàng (Hủy)'),
('HD034', '2025-06-09', 'vuong', 'NV01', 199000, 'Mua bằng giỏ hàng (Hủy)'),
('HD035', '2025-06-09', 'vuong', 'NV01', 199000, 'Mua ngay (Hủy)'),
('HD036', '2025-06-09', 'vuong', 'NV01', 1614000, 'Mua ngay (Hủy)'),
('HD037', '2025-06-09', 'vuong', 'NV01', 1614000, 'Mua ngay (Hủy)'),
('HD038', '2025-06-09', 'vuong', 'NV01', 289000, 'Mua ngay (Hủy)'),
('HD039', '2025-06-09', 'vuong', 'NV01', 289000, 'Đã đặt hàng'),
('HD040', '2025-06-11', 'vuong', 'NV01', 899000, 'Mua ngay (Hủy)'),
('HD041', '2025-06-11', 'vuong', 'NV01', 899000, 'Mua ngay (Hủy)'),
('HD042', '2025-06-11', 'vuong', 'NV01', 899000, 'Đã đặt hàng'),
('HD043', '2025-06-18', 'vuong', 'NV01', 970000, 'Mua bằng giỏ hàng (Hủy)'),
('HD044', '2025-06-18', 'vuong', 'NV01', 970000, 'Đã đặt hàng'),
('HD045', '2025-07-09', 'vuong', 'NV01', 199000, 'Mua ngay (Hủy)'),
('HD046', '2025-07-09', 'vuong', 'NV01', 398000, 'Mua bằng giỏ hàng (Hủy)'),
('HD047', '2025-07-09', 'vuong', 'NV01', 398000, 'Đã đặt hàng'),
('HD048', '2025-07-09', 'vuong', 'NV01', 899000, 'Đã đặt hàng'),
('HD049', '2025-07-09', 'vuong', 'NV01', 199000, 'Đã đặt hàng'),
('HD050', '2025-07-09', 'vuong', 'NV01', 199000, 'Đã đặt hàng'),
('HD051', '2025-07-09', 'vuong', 'NV01', 199000, 'Mua ngay (Hủy)'),
('HD052', '2025-07-09', 'vuong', 'NV01', 499000, 'Đã đặt hàng'),
('HD053', '2025-07-09', 'vuong', 'NV01', 199000, 'Đã đặt hàng'),
('HD054', '2025-07-09', 'vuong', 'NV01', 320000, 'Đã đặt hàng'),
('HD055', '2025-07-09', 'vuong', 'NV01', 759000, 'Đã đặt hàng'),
('HD056', '2025-07-09', 'vuong', 'NV01', 199000, 'Đã đặt hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE `khachhang` (
  `makh` char(50) NOT NULL,
  `hoten` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thongtin_lienhe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `doanhso` float DEFAULT NULL,
  `ngaysinh` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`makh`, `hoten`, `thongtin_lienhe`, `doanhso`, `ngaysinh`) VALUES
('admin', 'Quản trị viên', NULL, 0, '1990-01-01'),
('manager', 'Quản lý cửa hàng', NULL, 0, '1990-01-01'),
('san24', 'SAN', NULL, 0, '0005-02-24'),
('vuong', 'Vương', NULL, 0, '2005-10-27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE `nhanvien` (
  `manv` char(10) NOT NULL,
  `hoten` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngaysinh` date DEFAULT NULL,
  `gioitinh` bit(1) DEFAULT NULL,
  `ngaylamviec` date DEFAULT NULL,
  `sodt` char(11) DEFAULT NULL,
  `email` char(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`manv`, `hoten`, `ngaysinh`, `gioitinh`, `ngaylamviec`, `sodt`, `email`) VALUES
('NV01', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE `sanpham` (
  `masp` char(10) NOT NULL,
  `tensp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `danhmuc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kieu` varchar(50) DEFAULT NULL,
  `nuocsx` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia` float DEFAULT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`masp`, `tensp`, `danhmuc`, `kieu`, `nuocsx`, `gia`, `mota`, `url`) VALUES
('SP001', 'Áo Polo dài tay basic (Nâu nhạt)', 'Áo', 'Áo polo dài tay', NULL, 199000, 'Chất vải Waffle hiệu ứng sang trọng mà vẫn thoáng mát, mang lại trải nghiệm thoải mái cho người mặc. Cùng thiết kế đơn giản với gam màu trung tính dễ dàng phối được nhiều trang phục cho mọi hoàn cảnh.', 'accsets/images/products/SP001.png'),
('SP002', 'Áo polo dài tay BOOLAAB layer phối tay', 'Áo', 'Áo polo dài tay', NULL, 229000, '100% Cotton thoáng mát, dễ chịu, thấm hút mồ hôi tốt. Thoải mái khi vận động trong thời tiết nóng', 'accsets/images/products/SP002.png'),
('SP003', 'Áo polo dài tay bé trai', 'Áo', 'Áo polo dài tay', NULL, 179000, 'Áo polo dài tay bé trai có thiết kế bo gấu và hình in chữ năng động, phù hợp mặc đi học, đi chơi. Chất liệu vải cao cấp, co giãn tốt cho bé luôn thoải mái và ấm áp trong những ngày đông về.', 'accsets/images/products/SP003.png'),
('SP004', 'Áo Polo Dài Tay Lucian dệt kim', 'Áo', 'Áo polo dài tay', NULL, 289000, 'Áo polo dệt kim LUCIAN dài tay là sự kết hợp hoàn hảo giữa chất liệu cao cấp và thiết kế thanh lịch. Với kiểu dáng classic sang trọng, sản phẩm này giúp quý ông nổi bật trong mọi hoàn cảnh, từ công sở đến những buổi gặp gỡ bạn bè.', 'accsets/images/products/SP004.png'),
('SP005', 'Áo thun dài tay nam (Xanh dương)', 'Áo', 'Áo thun dài tay', NULL, 189000, 'Sự kết hợp tinh tế giữa 63% sợi Polyester cao cấp và 25% sợi Viscose mang đến sự bền chắc đáng tin cậy và bề mặt vải đanh mịn.', 'accsets/images/products/SP005.png'),
('SP006', 'Áo thun dài tay FOG Essentials', 'Áo', 'Áo thun dài tay', NULL, 390000, 'BẢO HIỂM HÀNG CHÍNH HÃNG 100%. Cam kết chính hãng 100% trọn đời sản phẩm. Phát hiện hàng giả đền FULL 100% GIÁ TRỊ.', 'accsets/images/products/SP006.png'),
('SP007', 'ÁO THUN DÀI TAY CỔ TRÒN', 'Áo', 'Áo thun dài tay', NULL, 149000, 'Các sản phẩm đều là hàng Local brand Trung Quốc được tuyển chọn có chất lượng và phong cách phù hợp tiêu chí Streetstyle.', 'accsets/images/products/SP007.png'),
('SP008', 'Áo Thun Dài Tay Nam Lacoste Basic', 'Áo', 'Áo thun dài tay', NULL, 450000, 'Mẫu áo nam đến từ thương hiệu Lacoste nổi tiếng. Mẫu áo thiết kế trẻ trung cùng chất liệu cao cấp vừa thời trang vừa giữ ấm cho người dùng.', 'accsets/images/products/SP008.png'),
('SP013', 'Áo sơ mi nam dài tay Uniqlo', 'Áo', 'Áo sơ mi', 'Nhật Bản', 899000, 'Áo được làm từ 100% cotton siêu dài. Chất vải mềm mịn, có độ bóng sang trọng. Được giặt và hoàn thiện đặc biệt để đạt được vẻ ngoài cao cấp, giản dị. Vải đã được hoàn thiện đặc biệt để tăng độ mềm mại. Quy trình may đặc biệt giúp giảm nếp nhăn sau khi giặt. Đường cắt rộng dễ dàng di chuyển. Thiết kế tay và ngực kiểu dáng đẹp. Được thiết kế để cử động cánh tay dễ dàng.', 'accsets/images/products/SP013.png'),
('SP014', 'Áo sơ mi Design DNST', 'Áo', 'Áo sơ mi', 'Việt Nam', 499000, 'Kiểu dáng vừa vặn hiện đại với cổ áo cài khuy phù hợp với những dịp trang trọng hoặc giản dị hơn. Có thể mặc riêng hoặc kết hợp cùng các trang phục khác.', 'accsets/images/products/SP014.png'),
('SP015', 'Áo Sơ Mi Nam Dài Tay Linen', 'Áo', 'Áo sơ mi', 'Ý', 759000, 'Sử dụng chất liệu linen nhập khẩu cao cấp, bề mặt vải mềm nhẹ, có độ thô tự nhiên và thoáng khí, giúp người mặc luôn cảm thấy mát mẻ và dễ chịu trong tiết trời nóng. Linen còn mang lại hiệu ứng đổ vải nhẹ nhàng, tạo cảm giác thanh thoát cho tổng thể trang phục.', 'accsets/images/products/SP015.png'),
('SP016', 'Áo Sơ Mi Nam Dài Tay Kẻ Sọc', 'Áo', 'Áo sơ mi', 'Hàn Quốc', 629000, 'Được làm từ cotton đũi xốp cao cấp, mang lại cảm giác mềm mại và thoáng mát khi mặc. Vải cotton đũi giúp hút ẩm tốt, tạo sự khô thoáng cho cơ thể, đặc biệt là trong những ngày nóng bức. Áo có form regular fit vừa vặn, dễ dàng tôn lên vẻ đẹp cơ thể mà vẫn tạo cảm giác thoải mái suốt cả ngày dài.', 'accsets/images/products/SP016.png'),
('SP017', 'Áo Thun Nam Lacoste Daniil Medvedev', 'Áo', 'Áo Tshirt', 'Pháp', 1190000, 'Áo Thun Nam Lacoste Daniil Medvedev Ultra T-Shirt TH7538-737 Màu Xanh Trắng được sản xuất bằng chất liệu 100% cotton co dãn, thoáng mát, thấm hút mồ hôi, chiếc áo mang đến sự dễ chịu, thoải mái di chuyển, vận động hàng ngày, giúp bạn tự tin tham gia các hoạt động thể thao.', 'accsets/images/products/SP017.png'),
('SP018', 'Áo Thun Nam Puma Graphics Sneaker', 'Áo', 'Áo Tshirt', 'Đức', 549000, 'Công nghệ hút ẩm giúp bạn luôn khô ráo và thoải mái. Chất vải thấm hút tốt, co giãn và mềm mại. Gam màu hiện đại dễ dàng phối với nhiều trang phục và phụ kiện. Chi tiết hình in nổi bật ở ngực áo. Form áo: Ôm nhẹ nhàng, thoải mái. Thích hợp mặc trong các dịp: Đi làm, đi chơi,...', 'accsets/images/products/SP018.png'),
('SP019', 'Áo Phông Vàng', 'Áo', 'Áo Tshirt', 'Việt Nam', 329000, 'Chất liệu: 95% Cotton và 5% Spandex. Kiểu dáng: Phom slimfit ôm vừa người, tôn dáng. Thiết kế: Cổ tròn cơ bản dễ phối đồ. Ưu điểm: Ứng dụng công nghệ DRI-AIR với khả năng bền màu, dễ làm sạch, thân thiện với da và môi trường.', 'accsets/images/products/SP019.png'),
('SP020', 'Áo T-shirt Cotton Unisex Việt Nam', 'Áo', 'Áo Tshirt', 'Việt Nam', 289000, 'Chiếc áo được làm từ 100% cotton cao cấp, mềm mại, thoáng khí. Thiết kế với độ bền màu vượt trội sau nhiều lần giặt. Sản phẩm thấm hút mồ hôi tốt, không gây kích ứng da, phù hợp với khí hậu nhiệt đới và an toàn cho mọi đối tượng.', 'accsets/images/products/SP020.png'),
('SP021', 'Trofeo Cashmere Double Pants', 'Quần', 'Quần âu', 'Ý', 8390000, 'Self: 95% wool, 5% cashmere. Pocket Lining: 100% cotton. Made in Italy. Dry clean only. Zip fly with hook and bar closure.', 'accsets/images/products/SP021.png'),
('SP022', 'New Metal Detail Pants', 'Quần', 'Quần âu', 'Ý', 7190000, 'Self: 76% wool, 24% cotton. Lining 1: 53% cupro, 47% polybutylene. Lining 2: 100% cotton. Elastic waistband with drawstring. Side pockets. Pleated detail. Made in Italy. Dry clean only.', 'accsets/images/products/SP022.png'),
('SP023', 'Trousers', 'Quần', 'Quần âu', 'Romania', 1890000, '98% cotton, 2% elastane. Made in Romania. Machine wash. Zip fly with button closure. 5-pocket design. Midweight twill fabric.', 'accsets/images/products/SP023.png'),
('SP024', 'Single Pleat Eternal Trousers', 'Quần', 'Quần âu', 'Ý', 6490000, 'Self: 100% wool. Lining 1: 53% cupro, 47% polybutylene. Lining 2: 100% cotton. Made in Italy. Dry clean only. Zip fly with hook and bar closure. 4-pocket design. Pleated detail. Lightweight suiting fabric.', 'accsets/images/products/SP024.png'),
('SP025', 'Quần short nam dây rút LADOS', 'Quần', 'Quần short', 'Việt Nam', 259000, 'Quần đùi được làm từ vải cotton nỉ dày dặn. Quần có dây rút dễ dàng điều chỉnh. Form Regular fit mặc cực thoải mái. Quần đùi ngắn năng động, trẻ trung.', 'accsets/images/products/SP025.png'),
('SP026', 'Quần short Pandax polime', 'Quần', 'Quần short', 'Việt Nam', 289000, 'Pandax Polime là thương hiệu thời trang unisex tập trung vào thời trang đường phố. Sản phẩm thời trang độc đáo, tính ứng dụng cao, phù hợp với nhiều đối tượng.', 'accsets/images/products/SP026.png'),
('SP027', 'Quần đùi cargo túi hộp', 'Quần', 'Quần short', 'Việt Nam', 319000, 'Sản phẩm thời trang unisex theo phong cách đường phố. Cargo túi hộp mang tính ứng dụng cao, thiết kế độc đáo phù hợp nhiều đối tượng.', 'accsets/images/products/SP027.png'),
('SP028', 'Quần short basic Hafos', 'Quần', 'Quần short', 'Việt Nam', 269000, 'Chất vải co giãn tốt, thấm hút, chóng nhăn, bền màu. Thiết kế form regular fit, mắt vải thoáng, không bị xù lông khi giặt. May tỉ mỉ, không chỉ thừa.', 'accsets/images/products/SP028.png'),
('SP029', 'Face Jeans', 'Quần', 'Quần jeans', 'Trung Quốc', 449000, '100% cotton. Machine wash. Zip fly with button closure. 5-pocket styling. Bedazzled design. Embroidered logo detail at pocket. Structured denim. Made in China.', 'accsets/images/products/SP029.png'),
('SP030', '5 Pocket Straight Leg', 'Quần', 'Quần jeans', 'Nhật Bản', 599000, '100% cotton. Made in Japan. Machine wash. Button fly closure. 5-pocket styling. Rigid denim fabric.', 'accsets/images/products/SP030.png'),
('SP031', 'Checkered Jacquard Form Straight', 'Quần', 'Quần jeans', 'Việt Nam', 689000, 'Denim bền bỉ, có độ dày vừa phải, giữ form tốt, ít nhăn, chịu lực cao. Phom suông thẳng, phù hợp nhiều dáng người. Họa tiết jacquard ô vuông độc đáo tạo hiệu ứng thị giác hiện đại.', 'accsets/images/products/SP031.png'),
('SP032', 'Quần jeans nam xanh đậm ống đứng', 'Quần', 'Quần jeans', 'Việt Nam', 419000, 'Chất liệu denim dệt từ sợi màu và trắng, tạo cảm giác dày dặn, bền, giữ form tốt. Tuy hơi bí khi mặc lâu nhưng form đứng rất thời trang và chắc chắn.', 'accsets/images/products/SP032.png'),
('SP034', 'Mắt Kính LV Glide', 'Mắt kính', '', 'Pháp', 8990000, 'Mắt kính LV Glide là sự kết hợp giữa sự sang trọng cổ điển và thiết kế hiện đại. Được chế tác từ chất liệu cao cấp, bảo vệ mắt khỏi tia UV. Gọng kim loại mạ vàng, tròng kính polycarbonate chống tia UV400. Thiết kế siêu nhẹ, thoải mái khi đeo lâu, phù hợp nhiều khuôn mặt. Đi kèm hộp, khăn lau và thẻ bảo hành chính hãng.', 'accsets/images/products/SP034.png'),
('SP035', 'Kính Mắt Christian Dior', 'Mắt kính', '', 'Pháp', 10250000, 'Kính mắt Dior có thiết kế đa dạng, phù hợp với nhiều phong cách khác nhau, từ kiểu dáng cổ điển đến hiện đại, từ màu sắc tối đơn giản đến những họa tiết phức tạp, từ kích thước nhỏ nhắn cho đến kích thước lớn và ấn tượng. Các mẫu kính mắt Dior được trang trí bằng các chi tiết kim loại quý, họa tiết hoa văn, hoặc logo Dior rất đặc trưng.', 'accsets/images/products/SP035.png'),
('SP036', 'Kính Mắt Chanel', 'Mắt kính', '', 'Pháp', 9750000, 'Mắt kính Chanel thể hiện phong cách sang trọng, tinh tế và nữ tính đặc trưng của thương hiệu thời trang cao cấp Pháp. Thiết kế đa dạng từ cổ điển đến hiện đại, phù hợp với nhiều độ tuổi và phong cách thời trang. Gọng kính được chế tác từ chất liệu acetate cao cấp hoặc kim loại mạ vàng, với điểm nhấn là logo Chanel khéo léo tích hợp trên gọng. Tròng kính cao cấp chống tia UV, đem lại sự bảo vệ toàn diện cho đôi mắt trong khi vẫn giữ được nét thời trang.', 'accsets/images/products/SP036.png'),
('SP037', 'Kính Chống Tia UV400', 'Mắt kính', '', 'Hàn Quốc', 450000, 'Kính chống tia UV là sản phẩm thiết yếu giúp bảo vệ mắt khỏi tác hại của tia cực tím từ ánh nắng mặt trời. Với tròng kính UV400, kính giúp ngăn ngừa các vấn đề về mắt như đục thủy tinh thể, thoái hóa điểm vàng. Thiết kế thời trang, nhẹ, phù hợp với nhiều kiểu khuôn mặt. Gọng kính làm từ nhựa dẻo hoặc kim loại chống gỉ, đem lại cảm giác thoải mái khi đeo lâu.', 'accsets/images/products/SP037.png'),
('SP038', 'Kính Mắt Cận Off-White Optical Style 29 Màu Đen', 'Mắt kính', '', 'Ý', 3500000, 'Kính mắt cận Off-White Optical với thiết kế gọng đen hiện đại, chất liệu hợp kim nhẹ, phù hợp cả nam và nữ. Tròng kính có thể thay đổi độ theo yêu cầu, mang lại sự thoải mái và thời trang.', 'accsets/images/products/SP038.png'),
('sp150', 'Bộ quần áo nam', 'Đồ bộ', '', 'Việt Nam', 450000, 'Set quần áo nam được làm bằng chất liệu waffle, thiết kế trẻ trung năng động phù hợp với nhiều hoàn cảnh mặc khác nhau. Bo cổ dệt kẻ phối màu, quần thiết kế can phối phong cách thể thao khỏe khoắn.', 'accsets/images/products/sp150.png'),
('SP151', 'Bộ quần áo nam (số 97)', 'Đồ bộ', '', 'Việt Nam', 460000, 'Set quần áo nam được làm bằng chất liệu bánh quế, thiết kế trẻ trung năng động phù hợp với nhiều hoàn cảnh khác nhau. Bo cổ dệt người phối màu, Quần thiết kế có thể phân phối phong cách thể thao lành mạnh.', 'accsets/images/products/SP151.png'),
('SP152', 'Adidas Training Set Sports FM6312', 'Đồ bộ', '', 'Trung Quốc', 950000, 'Bộ thể thao nam Adidas Trainning Set Sports FM6312 được may từ vải có chất liệu cao cấp mặt mịn thoáng khí, thiết kế áo khoác full Zip ấm áp, quần dài cạp chun co giãn thoải mái, form vừa vặn phù hợp cho mọi hoạt động thường ngày, đi tập luyện thể thao trong những ngày đông lạnh.', 'accsets/images/products/SP152.png'),
('SP153', 'Bộ áo sơ mi Cuban & quần jogger cotton jersey', 'Đồ bộ', '', 'Việt Nam', 520000, '►CHẤT THUN COTTON JERSEY ĐỨNG FORM VÀ ÍT NHĂN\nThun cotton jersey dệt ô vuông nổi bật với bề mặt vải có họa tiết tinh tế, tạo hiệu ứng thị giác độc đáo. Kết cấu giúp vải đứng form hơn, hạn chế nhăn và tăng độ bền.\n\n► FORM DÁNG REGULAR THOẢI MÁI CHO MỌI HOẠT ĐỘNG TRONG NGÀY\nCả áo và quần đều có form regular linh hoạt cho mọi vóc dáng. Thiết kế rộng rãi thoải mái vận động cả ngày, tạo vẻ ngoài trẻ trung, hiện đại.\n\n► ĐIỂM NHẤN VỚI TÚI THÊU LOGO TINH TẾ, THANH LỊCH\nÁo sơ mi Cuban và quần jogger có chi tiết túi thêu logo ý nghĩa. Nút đồi mồi cùng tông vải tạo vẻ ngoài thanh lịch, phù hợp nhiều hoàn cảnh.', 'accsets/images/products/SP153.png'),
('SP154', 'Set đồ thể thao cảm hứng bóng rổ - Số 10', 'Đồ bộ', '', 'Việt Nam', 580000, '►CHẤT LIỆU DOUBLE FACE ĐỨNG FORM, MỀM MẠI\nChất Double Face - interlock CVC cá sấu co giãn 2 chiều bền bỉ và linh hoạt, mang lại cảm giác thoải mái và vừa vặn. Bề mặt vải cá sấu đứng form giúp giữ độ bền đẹp sau nhiều lần sử dụng và giặt.\n\n► FORM DÁNG THOẢI MÁI CHO MỌI HOẠT ĐỘNG TRONG NGÀY\nÁo form Oversize kết hợp quần form Regular, linh hoạt cho mọi vóc dáng. Thiết kế rộng rãi giúp thoải mái vận động, tạo vẻ ngoài trẻ trung, hiện đại.\n\n► THIẾT KẾ MANG CẢM HỨNG BÓNG RỔ ĐẦY NĂNG ĐỘNG, CÁ TÍNH\nSet đồ nổi bật với họa tiết in số 10, lấy cảm hứng từ bóng rổ. Họa tiết monogram và tone màu Be trung tính mang đến phong cách năng động, cá tính.', 'accsets/images/products/SP154.png'),
('SP155', 'Quần short jean Jacquard lưng thun', 'Đồ bộ', '', 'Việt Nam', 450000, '►CHẤT LIỆU JACQUARD JEAN TẠO CHIỀU SÂU THỊ GIÁC\nJacquard jean với họa tiết dệt trực tiếp, tạo chiều sâu và điểm nhấn mà không cần in hay thêu. Bền chắc, giữ form tốt, mềm mại và thoải mái khi mặc.\n\n► FORM DÁNG REGULAR THOẢI MÁI CHO MỌI HOẠT ĐỘNG TRONG NGÀY\nForm regular cân đối, linh hoạt cho mọi vóc dáng. Thiết kế vừa vặn, không quá ôm hay rộng, dễ dàng vận động.\n\n► THIẾT KẾ SANG TRỌNG DỄ MẶC, \r\n PHỐI\nQuần short lưng thun thoải mái, túi xéo tinh tế. Hai màu light blue và blue tạo phong cách sang trọng, khác biệt trên nền vải jacquard jean.', 'accsets/images/products/SP155.png'),
('SP156', 'Bộ đồ thun cá sấu Interlock phối viền', 'Đồ bộ', '', 'Việt Nam', 450000, '► CHẤT LIỆU MỀM MẠI VÀ THOÁNG MÁT\nThun cá sấu Interlock mang đến cảm giác mềm mại và thoáng mát. Khả năng co giãn tốt giúp người mặc thoải mái trong mọi hoạt động.\n\n► FORM DÁNG NĂNG ĐỘNG VÀ GỌN GÀNG\nForm regular đảm bảo sự thoải mái, tự do trong từng chuyển động và phù hợp với nhiều dáng người.\n\n► THIẾT KẾ PHỐI VIỀN TINH TẾ VÀ TỈ MỈ\nÁo cổ bâu kiểu thẳng nhọn tạo vẻ lịch lãm, vai áo liền tay với viền may đắp xương cá thẩm mỹ. Quần lưng thun lót và túi đắp tiện dụng, phù hợp nhiều hoàn cảnh.', 'accsets/images/products/SP156.png'),
('SP157', 'Bộ áo polo tay dài và quần nhung gân Earth Tone', 'Đồ bộ', '', 'Việt Nam', 520000, 'Áo polo tay dài và quần dài nhung gân, với thiết kế đơn giản nhưng không kém phần tinh tế, mang lại vẻ ngoài lịch lãm và thoải mái. Các gam màu trung tính như kem hay rêu giúp dễ phối đồ, phản ánh phong cách thời trang Earth Tone hiện đại, phù hợp cho mọi dịp từ đi làm đến dạo phố.', 'accsets/images/products/SP157.png'),
('SP158', 'Set đồ nỉ Tici cao cấp – Sweater & Jogger', 'Đồ bộ', '', 'Việt Nam', 490000, 'Sử dụng chất liệu nỉ Tici cao cấp, set đồ đảm bảo độ mềm mại và giữ ấm hiệu quả. Vải mềm mại tạo cảm giác thoải mái và giữ nhiệt tốt trong nhiều điều kiện thời tiết. Áo sweater và quần jogger đều có form regular fit, vừa vặn, tôn dáng mà vẫn linh hoạt, giúp bạn tự tin và thoải mái trong mọi hoạt động.', 'accsets/images/products/SP158.png'),
('SP159', 'Set Bộ Sọc Gân Plain Fabric', 'Đồ bộ', '', 'Việt Nam', 420000, 'Set Bộ Sọc Gân Plain Fabric được chế tạo từ chất liệu thun sọc gân cao cấp, mang lại cảm giác mềm mại, thoải mái và co giãn tốt. Với form regular giữ sự phong cách, áo thun và quần short được thiết kế vừa vặn, tôn dáng và năng động. Đây là lựa chọn lý tưởng cho hẹn hò ngoài trời, thể thao nhẹ hoặc dạo phố.', 'accsets/images/products/SP159.png'),
('SP200', 'Giày da nam cao cấp AG0315', 'Giày', '', 'Việt Nam', 1500000, 'Mẫu giày da nam cao cấp AG0315 có thiết kế đơn giản nhưng vô cùng tinh tế, tạo điểm nhấn với các chi tiết nhỏ như đường may và phụ kiện kim loại.', 'accsets/images/products/SP200.png'),
('SP201', 'Giày nam thể thao', 'Giày', '', 'Việt Nam', 950000, 'Thiết kế trẻ trung, năng động\nChất liệu cao cấp bền đẹp\nKiểu dáng thời trang, dễ phối đồ\nMàu sắc tinh tế, thanh lịch', 'accsets/images/products/SP201.png'),
('SP202', 'Giày Nam Cổ Cao G99 Kiểu Boot Martin', 'Giày', '', 'Việt Nam', 1250000, 'Giày Nam Cổ Cao G99 kết hợp phong cách mạnh mẽ và thời trang hiện đại, hoàn hảo cho cả nam và nữ. Sở hữu ngay một đôi boot cổ thấp cá tính, không chỉ bền đẹp mà còn mang lại sự thoải mái tuyệt đối.\n\nGiày sử dụng chất liệu da xám đen cao cấp, cổ thun gân mềm mại, tạo cảm giác ôm sát chân nhưng vẫn thoải mái khi di chuyển. Đế giày dày 4cm giúp tôn dáng, chắc chắn mà không gây cảm giác nặng nề. Thiết kế mang phong cách boot Martin với đế răng cưa mạnh mẽ, phù hợp cho mọi hoạt động từ dạo phố đến đi phượt.', 'accsets/images/products/SP202.png'),
('SP203', 'Giày Bóng Bàn Asics Hyperbeat 4', 'Giày', '', 'Nhật Bản', 1490000, 'Giày Bóng Bàn Asics Hyperbeat 4 hàng chính hãng Nhật Bản, thiết kế nhẹ, bám sân tốt, lý tưởng cho luyện tập và thi đấu.', 'accsets/images/products/SP203.png'),
('SP204', 'Giày adidas thể thao đa năng', 'Giày', '', 'Việt Nam', 1290000, 'Chất liệu: Da tự nhiên 100% + Sợi tổng hợp + TPU + Textile. Công nghệ Light Foam. Đế cao su & TPU & EVA dày dặn, chống sốc, chống trơn trượt, chống mài mòn. Sự lựa chọn hoàn hảo cho phong cách thể thao năng động. Thiết kế đa dạng, màu sắc tươi sáng, chất liệu cao cấp mang lại sự thoải mái, bền bỉ và thời trang.', 'accsets/images/products/SP204.png'),
('SP205', 'Nike Air Force 1 thể thao', 'Giày', '', 'Việt Nam', 2890000, 'Nike Air Force 1 là đôi giày mang tính biểu tượng vượt thời gian trong cộng đồng sneaker. Được thiết kế bởi Bruce Kilgore, lấy cảm hứng từ giày đi bộ đường dài Nike Approach, Nhà thờ Đức Bà và máy bay tổng thống Mỹ “Air Force One”. Mẫu giày đầu tiên sử dụng công nghệ Nike Air, chất liệu da và lưới, mang đến sự bền bỉ và phong cách. Tinker Hatfield cũng từng là người thử nghiệm giày cho dòng sản phẩm này.', 'accsets/images/products/SP205.png'),
('SP206', 'Giày Nam CONXEGN Da Bò Màu Nâu', 'Giày', '', 'Ý', 1150000, 'Giày da CONXEGN dành cho nam giới với chất liệu da bò lớp 2 được chọn lọc kỹ càng, mềm mại và bóng tự nhiên. Đường may thẳng, đều, tạo vẻ bền đẹp. Lớp trong bằng sợi siêu mềm, thoáng khí. Dây giày thiết kế đẹp mắt, dễ mang. Đế cao su tự nhiên siêu đàn hồi, chống mài mòn và trơn trượt. Màu sắc: nâu – thanh lịch và dễ phối đồ.', 'accsets/images/products/SP206.png'),
('SP207', 'Giày Bóng Đá Nam YSTU063-1V', 'Giày', '', 'Trung Quốc', 890000, 'Giày bóng đá YSTU063-1V sử dụng chất liệu TPU và da tổng hợp cao cấp, nhẹ và bền. Thiết kế chuyên dụng hỗ trợ sút bóng mạnh, kiểm soát bóng tốt và bám sân tối ưu. Đế giày có các gai nhỏ phù hợp cả cỏ tự nhiên lẫn nhân tạo, đảm bảo độ ổn định và hiệu suất trong mọi điều kiện thời tiết.', 'accsets/images/products/SP207.png'),
('SP208', 'Giày Bóng Chuyền Beyono Eagle 8', 'Giày', '', 'Việt Nam', 970000, 'Beyono Eagle 8 - biểu tượng cho sức mạnh, tinh thần và phong cách không thể cưỡng lại trên sân bóng chuyền! Lấy cảm hứng từ đại bàng – đại diện cho sự kiêu hãnh, sức mạnh và khát vọng vươn xa. Eagle 8 là người bạn đồng hành lý tưởng của những vận động viên dám ước mơ và quyết tâm chinh phục mọi đỉnh cao trong thể thao.', 'accsets/images/products/SP208.png'),
('SP209', 'Giày Cầu Lông Yonex SHB 65Z3 2023', 'Giày', '', 'Nhật Bản', 2150000, 'Giày cầu lông Yonex SHB 65Z3 2023 là dòng sản phẩm cao cấp với thiết kế form giày thon gọn, ôm chân, hỗ trợ tối ưu các bước di chuyển linh hoạt. Phiên bản xanh trắng mang vẻ ngoài trẻ trung, năng động, còn bản trắng cam lại thể thao và hiện đại. Cả hai đều đáp ứng tiêu chuẩn về độ bám, độ bền và sự thoải mái, phù hợp cho nữ vận động viên làm chủ trận đấu.', 'accsets/images/products/SP209.png'),
('sp300', 'Áo Polo Cotton Spandex Regular Fit', 'Áo', 'Áo polo dài tay', 'Việt Nam', 320000, 'Chất liệu: 95% cotton, 5% spandex. Áo Polo kiểu dáng Regular fit, tone màu nam tính và sang trọng. Mang lại cảm giác mặc mát, không nhăn, thân thiện với môi trường. Dễ giặt ủi và độ bền cao. Sản phẩm đạt tiêu chuẩn hàng Việt Nam xuất khẩu.', 'accsets/images/products/sp300.png'),
('SP301', 'Áo Polo Dài Tay Nam Cao Cấp', 'Áo', 'Áo polo dài tay', 'Việt Nam', 350000, 'Áo polo dài tay là lựa chọn hoàn hảo cho những ai yêu thích phong cách thanh lịch nhưng vẫn muốn đảm bảo sự thoải mái trong trang phục hàng ngày. Với chất liệu cao cấp và thiết kế tỉ mỉ, áo không chỉ mang lại vẻ ngoài nam tính mà còn đáp ứng tối đa nhu cầu sử dụng hàng ngày.', 'accsets/images/products/SP301.png');

--
-- Bẫy `sanpham`
--
DROP TRIGGER IF EXISTS `trg_set_url_before_insert`;
DELIMITER $$
CREATE TRIGGER `trg_set_url_before_insert` BEFORE INSERT ON `sanpham` FOR EACH ROW BEGIN
  SET NEW.url = CONCAT('accsets/images/products/', NEW.masp, '.png');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `size_sanpham`
--

DROP TABLE IF EXISTS `size_sanpham`;
CREATE TABLE `size_sanpham` (
  `masp` char(10) NOT NULL,
  `size` varchar(5) NOT NULL,
  `soluong` float DEFAULT NULL,
  `gia` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `size_sanpham`
--

INSERT INTO `size_sanpham` (`masp`, `size`, `soluong`, `gia`) VALUES
('SP001', 'L', 12, 199000),
('SP001', 'M', 10, 199000),
('SP002', 'L', 10, 229000),
('SP002', 'M', 8, 229000),
('SP003', 'M', 10, 179000),
('SP003', 'S', 15, 179000),
('SP004', 'L', 7, 289000),
('SP004', 'M', 5, 289000),
('SP005', 'L', 10, 189000),
('SP005', 'M', 14, 189000),
('SP005', 'S', 12, 189000),
('SP005', 'XL', 6, 189000),
('SP006', 'L', 8, 390000),
('SP006', 'M', 10, 390000),
('SP006', 'XL', 5, 390000),
('SP006', 'XXL', 3, 390000),
('SP007', 'L', 10, 149000),
('SP007', 'M', 15, 149000),
('SP007', 'S', 20, 149000),
('SP007', 'XL', 6, 149000),
('SP007', 'XXL', 2, 149000),
('SP008', 'L', 6, 450000),
('SP008', 'M', 8, 450000),
('SP008', 'XL', 4, 450000),
('SP008', 'XXL', 2, 450000),
('SP013', 'L', 12, 899000),
('SP013', 'M', 15, 899000),
('SP013', 'S', 10, 899000),
('SP013', 'XL', 8, 899000),
('SP014', 'L', 10, 499000),
('SP014', 'M', 18, 499000),
('SP014', 'S', 14, 499000),
('SP014', 'XL', 7, 499000),
('SP015', 'L', 11, 759000),
('SP015', 'M', 13, 759000),
('SP015', 'S', 9, 759000),
('SP015', 'XL', 6, 759000),
('SP016', 'L', 12, 629000),
('SP016', 'M', 16, 629000),
('SP016', 'S', 11, 629000),
('SP016', 'XL', 5, 629000),
('SP017', 'L', 12, 1190000),
('SP017', 'M', 15, 1190000),
('SP017', 'S', 10, 1190000),
('SP017', 'XL', 6, 1190000),
('SP018', 'L', 11, 549000),
('SP018', 'M', 14, 549000),
('SP018', 'S', 9, 549000),
('SP018', 'XL', 5, 549000),
('SP019', 'L', 8, 329000),
('SP019', 'M', 12, 329000),
('SP019', 'S', 10, 329000),
('SP019', 'XL', 4, 329000),
('SP020', 'L', 13, 289000),
('SP020', 'M', 17, 289000),
('SP020', 'S', 15, 289000),
('SP020', 'XL', 6, 289000),
('SP021', 'L', 6, 8390000),
('SP021', 'M', 8, 8390000),
('SP021', 'S', 5, 8390000),
('SP021', 'XL', 3, 8390000),
('SP022', 'L', 7, 7190000),
('SP022', 'M', 9, 7190000),
('SP022', 'S', 6, 7190000),
('SP022', 'XL', 4, 7190000),
('SP023', 'L', 9, 1890000),
('SP023', 'M', 12, 1890000),
('SP023', 'S', 10, 1890000),
('SP023', 'XL', 5, 1890000),
('SP024', 'L', 5, 6490000),
('SP024', 'M', 7, 6490000),
('SP024', 'S', 4, 6490000),
('SP024', 'XL', 2, 6490000),
('SP025', 'L', 9, 259000),
('SP025', 'M', 12, 259000),
('SP025', 'S', 10, 259000),
('SP025', 'XL', 6, 259000),
('SP026', 'L', 7, 289000),
('SP026', 'M', 10, 289000),
('SP026', 'S', 8, 289000),
('SP026', 'XL', 5, 289000),
('SP027', 'L', 8, 319000),
('SP027', 'M', 9, 319000),
('SP027', 'S', 6, 319000),
('SP027', 'XL', 4, 319000),
('SP028', 'L', 10, 269000),
('SP028', 'M', 13, 269000),
('SP028', 'S', 11, 269000),
('SP028', 'XL', 7, 269000),
('SP029', 'L', 8, 449000),
('SP029', 'M', 10, 449000),
('SP029', 'S', 6, 449000),
('SP029', 'XL', 5, 449000),
('SP030', 'L', 6, 599000),
('SP030', 'M', 7, 599000),
('SP030', 'S', 4, 599000),
('SP030', 'XL', 3, 599000),
('SP031', 'L', 10, 689000),
('SP031', 'M', 9, 689000),
('SP031', 'S', 5, 689000),
('SP031', 'XL', 6, 689000),
('SP032', 'L', 11, 419000),
('SP032', 'M', 12, 419000),
('SP032', 'S', 7, 419000),
('SP032', 'XL', 8, 419000),
('SP034', 'E', 8, 8990000),
('SP034', 'W', 10, 8990000),
('SP035', 'E', 9, 10250000),
('SP035', 'W', 12, 10250000),
('SP036', 'E', 11, 9750000),
('SP036', 'W', 14, 9750000),
('SP037', 'E', 18, 450000),
('SP037', 'W', 20, 450000),
('SP038', 'L', 15, 3500000),
('SP038', 'M', 20, 3500000),
('sp150', 'L', 12, 450000),
('sp150', 'M', 15, 450000),
('sp150', 'S', 10, 450000),
('sp150', 'XL', 8, 450000),
('sp151', 'L', 12, 460000),
('sp151', 'M', 15, 460000),
('sp151', 'S', 10, 460000),
('sp151', 'XL', 8, 460000),
('sp152', 'L', 12, 950000),
('sp152', 'M', 15, 950000),
('sp152', 'S', 10, 950000),
('sp152', 'XL', 8, 950000),
('sp153', 'L', 12, 520000),
('sp153', 'M', 15, 520000),
('sp153', 'S', 10, 520000),
('sp153', 'XL', 8, 520000),
('sp154', 'L', 12, 580000),
('sp154', 'M', 15, 580000),
('sp154', 'S', 10, 580000),
('sp154', 'XL', 8, 580000),
('sp155', 'L', 12, 450000),
('sp155', 'M', 15, 450000),
('sp155', 'S', 10, 450000),
('sp155', 'XL', 8, 450000),
('sp156', 'L', 12, 450000),
('sp156', 'M', 15, 450000),
('sp156', 'S', 10, 450000),
('sp156', 'XL', 8, 450000),
('sp157', 'L', 12, 520000),
('sp157', 'M', 15, 520000),
('sp157', 'S', 10, 520000),
('sp157', 'XL', 8, 520000),
('sp158', 'L', 12, 490000),
('sp158', 'M', 15, 490000),
('sp158', 'S', 10, 490000),
('sp158', 'XL', 8, 490000),
('sp159', 'L', 12, 420000),
('sp159', 'M', 15, 420000),
('sp159', 'S', 10, 420000),
('sp159', 'XL', 8, 420000),
('SP200', '39', 10, 1500000),
('SP200', '40', 8, 1500000),
('SP200', '41', 5, 1500000),
('SP201', '40', 12, 950000),
('SP201', '41', 9, 950000),
('SP201', '42', 6, 950000),
('SP202', '41', 10, 1250000),
('SP202', '42', 7, 1250000),
('SP202', '43', 5, 1250000),
('SP203', '39', 10, 1490000),
('SP203', '40', 15, 1490000),
('SP203', '41', 12, 1490000),
('SP203', '42', 8, 1490000),
('SP204', '39', 8, 1290000),
('SP204', '40', 10, 1290000),
('SP204', '41', 12, 1290000),
('SP204', '42', 7, 1290000),
('SP205', '40', 10, 2890000),
('SP205', '41', 12, 2890000),
('SP205', '42', 9, 2890000),
('SP205', '43', 5, 2890000),
('SP206', '40', 6, 1150000),
('SP206', '41', 10, 1150000),
('SP206', '42', 8, 1150000),
('SP206', '43', 4, 1150000),
('SP207', '39', 10, 890000),
('SP207', '40', 12, 890000),
('SP207', '41', 15, 890000),
('SP207', '42', 9, 890000),
('SP208', '40', 8, 970000),
('SP208', '41', 10, 970000),
('SP208', '42', 12, 970000),
('SP208', '43', 6, 970000),
('SP209', '36', 6, 2150000),
('SP209', '37', 8, 2150000),
('SP209', '38', 10, 2150000),
('SP209', '39', 5, 2150000),
('sp300', 'L', 12, 320000),
('sp300', 'M', 15, 320000),
('sp300', 'S', 10, 320000),
('sp300', 'XL', 8, 320000),
('sp301', 'L', 12, 350000),
('sp301', 'M', 15, 350000),
('sp301', 'S', 10, 350000),
('sp301', 'XL', 8, 350000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE `taikhoan` (
  `username` char(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `vaitro` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`username`, `password`, `vaitro`) VALUES
('admin', 'admin123', 'admin'),
('manager', 'manager123', 'admin'),
('vien', '123', 'user'),


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongtin_lienhe`
--

DROP TABLE IF EXISTS `thongtin_lienhe`;
CREATE TABLE `thongtin_lienhe` (
  `makh` char(50) NOT NULL,
  `sodt` char(10) DEFAULT NULL,
  `captinh` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caphuyen` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capxa` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sonha` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thongtin_lienhe`
--

INSERT INTO `thongtin_lienhe` (`makh`, `sodt`, `captinh`, `caphuyen`, `capxa`, `sonha`, `email`) VALUES
('admin', '0123456789', 'Hà Nội', 'Ba Đình', 'Phúc Xá', '123 Admin Street', 'admin@btshop.com'),
('manager', '0987654321', 'TP.HCM', 'Quận 1', 'Bến Nghé', '456 Manager Avenue', 'manager@btshop.com'),
('san24', '0814225862', 'Bình Định', 'Quy Nhơn', 'Ngô Mây', '316/b5 Nguyễn Thái Học', 'vuongvo271005@gmail.com'),
('vuong', '0814225862', 'Bình Định', 'Quy Nhơn', 'Ngô Mây', '316/b5 Nguyễn Thái Học', 'vuongvo271005@gmail.com');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD PRIMARY KEY (`soHD`,`masp`,`size`),
  ADD KEY `masp` (`masp`,`size`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`makh`,`masp`,`size`),
  ADD KEY `masp` (`masp`,`size`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`soHD`),
  ADD KEY `makh` (`makh`),
  ADD KEY `manv` (`manv`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`makh`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`manv`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`masp`);

--
-- Chỉ mục cho bảng `size_sanpham`
--
ALTER TABLE `size_sanpham`
  ADD PRIMARY KEY (`masp`,`size`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `thongtin_lienhe`
--
ALTER TABLE `thongtin_lienhe`
  ADD PRIMARY KEY (`makh`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD CONSTRAINT `chitiethoadon_ibfk_1` FOREIGN KEY (`soHD`) REFERENCES `hoadon` (`soHD`),
  ADD CONSTRAINT `chitiethoadon_ibfk_2` FOREIGN KEY (`masp`,`size`) REFERENCES `size_sanpham` (`masp`, `size`);

--
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `khachhang` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`masp`,`size`) REFERENCES `size_sanpham` (`masp`, `size`);

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `khachhang` (`makh`),
  ADD CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`manv`) REFERENCES `nhanvien` (`manv`);

--
-- Các ràng buộc cho bảng `size_sanpham`
--
ALTER TABLE `size_sanpham`
  ADD CONSTRAINT `size_sanpham_ibfk_1` FOREIGN KEY (`masp`) REFERENCES `sanpham` (`masp`);

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`username`) REFERENCES `khachhang` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thongtin_lienhe`
--
ALTER TABLE `thongtin_lienhe`
  ADD CONSTRAINT `thongtin_lienhe_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `khachhang` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;



SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
