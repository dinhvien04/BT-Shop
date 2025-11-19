# 🛍️ BT Shop - Hệ thống E-commerce Thời trang Nam

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)

**Website thương mại điện tử chuyên bán thời trang nam hiện đại và chuyên nghiệp**

[Demo](#) • [Báo cáo lỗi](https://github.com/issues) • [Yêu cầu tính năng](https://github.com/issues)

</div>

---

## 📋 Mục lục

- [✨ Tính năng chính](#-tính-năng-chính)
- [🚀 Hướng dẫn cài đặt](#-hướng-dẫn-cài-đặt)
- [📁 Cấu trúc dự án](#-cấu-trúc-dự-án)
- [💻 Công nghệ sử dụng](#-công-nghệ-sử-dụng)
- [🎯 Tài khoản demo](#-tài-khoản-demo)
- [📸 Screenshots](#-screenshots)
- [🤝 Đóng góp](#-đóng-góp)
- [📞 Liên hệ](#-liên-hệ)

---

## ✨ Tính năng chính

### 🛒 **Phần Khách hàng**
- **Trang chủ hiện đại**: Slider, sản phẩm nổi bật, phân loại theo danh mục
- **Quản lý tài khoản**: Đăng ký, đăng nhập, cập nhật thông tin cá nhân
- **Mua sắm thông minh**: Tìm kiếm, lọc sản phẩm, giỏ hàng, thanh toán
- **Theo dõi đơn hàng**: Lịch sử mua hàng, trạng thái đơn hàng
- **Responsive Design**: Tương thích mọi thiết bị

### 👨‍💼 **Phần Quản trị viên**
- **Dashboard tổng quan**: Thống kê doanh thu, đơn hàng, khách hàng
- **Quản lý sản phẩm**: Thêm, sửa, xóa sản phẩm, quản lý kho
- **Quản lý đơn hàng**: Xem, cập nhật trạng thái đơn hàng
- **Quản lý khách hàng**: Thông tin, lịch sử mua hàng, phân hạng
- **Báo cáo thống kê**: Biểu đồ doanh thu, sản phẩm bán chạy
- **Cài đặt hệ thống**: Cấu hình website, bảo mật, email

---

## 🚀 Hướng dẫn cài đặt

### 📋 Yêu cầu hệ thống
- **PHP**: >= 7.4
- **MySQL**: >= 5.7
- **Apache/Nginx**: Web server
- **XAMPP/WAMPP**: (Khuyến nghị cho development)

### 🔧 Các bước cài đặt

1. **Clone dự án**
   ```bash
   git clone https://github.com/username/bt-shop.git
   cd bt-shop
   ```

2. **Cài đặt XAMPP**
   - Tải và cài đặt [XAMPP](https://www.apachefriends.org/)
   - Khởi động Apache và MySQL

3. **Cấu hình Database**
   ```sql
   -- Tạo database
   CREATE DATABASE QLBH;
   
   -- Import file SQL
   mysql -u root -p QLBH < database/qlbh.sql
   ```

4. **Cấu hình kết nối**
   ```php
   // Chỉnh sửa file connect.php
   $host = 'localhost';
   $dbname = 'QLBH';
   $username = 'root';
   $password = '';
   ```

5. **Truy cập website**
   ```
   http://localhost/bt-shop
   ```

---

## 📁 Cấu trúc dự án

```
banhang/
├── 📁 accsets/              # Assets (CSS, JS, Images)
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   ├── images/              # Hình ảnh
│   └── fonts/               # Fonts
├── 📁 admin/                # Phần quản trị
│   ├── dashboard.php        # Trang chủ admin
│   ├── products.php         # Quản lý sản phẩm
│   ├── orders.php           # Quản lý đơn hàng
│   ├── customers.php        # Quản lý khách hàng
│   ├── reports.php          # Báo cáo thống kê
│   └── settings.php         # Cài đặt hệ thống
├── 📁 widget/               # Components
│   ├── header.php           # Header
│   ├── footer.php           # Footer
│   ├── modal.php            # Modal dialogs
│   └── sidebar.php          # Sidebar admin
├── 📁 database/             # Database files
│   └── qlbh.sql            # Database schema
├── 📄 index.php             # Trang chủ
├── 📄 login.php             # Đăng nhập
├── 📄 register.php          # Đăng ký
├── 📄 cart.php              # Giỏ hàng
├── 📄 checkout.php          # Thanh toán
├── 📄 myaccount.php         # Tài khoản cá nhân
├── 📄 connect.php           # Kết nối database
└── 📄 README.md             # File này
```

---

## 💻 Công nghệ sử dụng

| Công nghệ | Mô tả | Phiên bản |
|-----------|-------|-----------|
| **PHP** | Backend language | 7.4+ |
| **MySQL** | Database | 5.7+ |
| **HTML5** | Markup language | Latest |
| **CSS3** | Styling | Latest |
| **JavaScript** | Frontend interactivity | ES6+ |
| **Chart.js** | Data visualization | 3.x |
| **Bootstrap** | CSS Framework | 4.x |
| **jQuery** | JavaScript library | 3.x |

---

## 🎯 Tài khoản demo

### 👤 **Quản trị viên**
```
Username: admin
Password: admin123
Role: Administrator
```

```
Username: manager  
Password: manager123
Role: Manager
```

### 🛍️ **Khách hàng**
```
Username: vien
Password: 123
```


---

## 📸 Screenshots

<details>
<summary>🏠 <strong>Trang chủ</strong></summary>

- Slider hiện đại với sản phẩm nổi bật
- Danh mục sản phẩm được phân loại rõ ràng
- Giao diện responsive, thân thiện

</details>

<details>
<summary>👨‍💼 <strong>Admin Dashboard</strong></summary>

- Thống kê tổng quan với cards và charts
- Bảng sản phẩm bán chạy
- Menu điều hướng sidebar

</details>

<details>
<summary>📊 <strong>Báo cáo & Thống kê</strong></summary>

- Biểu đồ doanh thu theo thời gian
- Thống kê khách hàng và đơn hàng
- Phân tích sản phẩm bán chạy

</details>

---

## 🔒 Bảo mật

- ✅ **SQL Injection Protection**: Sử dụng prepared statements
- ✅ **XSS Prevention**: Escape output với htmlspecialchars()
- ✅ **Session Management**: Quản lý phiên đăng nhập an toàn
- ✅ **Password Hashing**: Mã hóa mật khẩu MD5 (khuyến nghị nâng cấp lên bcrypt)
- ✅ **CSRF Protection**: Token validation cho forms

---

## 🚀 Tính năng nâng cao

- 📱 **Responsive Design**: Tương thích mobile, tablet
- 🔍 **Smart Search**: Tìm kiếm thông minh với filter
- 📊 **Analytics**: Thống kê chi tiết với biểu đồ
- 💳 **Payment Integration**: Tích hợp thanh toán online
- 📧 **Email System**: Gửi email tự động
- 🎨 **Modern UI/UX**: Giao diện hiện đại, trải nghiệm tốt

---

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! 

1. **Fork** dự án
2. **Tạo branch** cho tính năng mới (`git checkout -b feature/AmazingFeature`)
3. **Commit** thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. **Push** lên branch (`git push origin feature/AmazingFeature`)
5. **Tạo Pull Request**

### 📋 Hướng dẫn đóng góp
- Tuân thủ coding standards
- Viết comments rõ ràng
- Test kỹ trước khi submit
- Cập nhật documentation nếu cần

---

## 📈 Roadmap

- [ ] **v2.0**: Tích hợp AI chatbot
- [ ] **v2.1**: Mobile App (React Native)
- [ ] **v2.2**: Multi-vendor support
- [ ] **v2.3**: Advanced analytics
- [ ] **v2.4**: Inventory management
- [ ] **v2.5**: Customer loyalty program

---

## 📞 Liên hệ

<div align="center">

**👨‍💻 Developer Team**

📧 **Email**: support@btshop.com  
🌐 **Website**: https://btshop.com  
📱 **Hotline**: (+84) 123-456-789

**Mạng xã hội**

[![Facebook](https://img.shields.io/badge/Facebook-1877F2?style=for-the-badge&logo=facebook&logoColor=white)](https://facebook.com/btshop)
[![Instagram](https://img.shields.io/badge/Instagram-E4405F?style=for-the-badge&logo=instagram&logoColor=white)](https://instagram.com/btshop)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/company/btshop)

</div>

---

## 📄 License

Dự án này được phân phối dưới giấy phép **MIT License**. Xem file `LICENSE` để biết thêm chi tiết.

---

<div align="center">

**⭐ Nếu dự án hữu ích, đừng quên để lại một star! ⭐**

Made with ❤️ by **BT Shop Team**

</div>