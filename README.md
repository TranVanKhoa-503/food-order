# Food Order

Food Order là hệ thống website đặt món ăn trực tuyến hoàn chỉnh dành cho **một cửa hàng**, xây dựng trên nền tảng **Laravel 13 monolith + Blade templates + JavaScript thuần**.

Hệ thống đã hoàn thành trọn vẹn từ Sprint 0 đến Sprint 7 theo đúng thiết kế và quy chuẩn trong thư mục `docs/`.

---

## 🌟 Tính Năng Chính

### 1. Khách Hàng (Customer)
* **Khám phá thực đơn:** Xem danh sách món ăn từ database, phân loại theo danh mục, tìm kiếm theo tên/mô tả, lọc theo khoảng giá.
* **Xác thực an toàn:** Đăng ký tài khoản (`role = user`), Đăng nhập session, Đăng xuất (invalidate session + token CSRF), Quên/Đặt lại mật khẩu.
* **Quản lý tài khoản:** Xem hồ sơ cá nhân, cập nhật tên/số điện thoại/địa chỉ giao hàng, đổi mật khẩu.
* **Giỏ hàng & Checkout:**
  * Giỏ hàng lưu trữ ở trình duyệt (`localStorage`).
  * Backend **tự động truy vấn lại giá và tính toán từ Database** trong Database Transaction (chống gian lận giá từ frontend).
  * Kiểm tra trạng thái món ăn (`is_available`).
  * Tạo đơn hàng COD và snapshot thông tin món ăn tại thời điểm mua (`OrderItem`).
* **Theo dõi đơn hàng:**
  * Xem danh sách lịch sử đơn hàng của chính mình (chặn xem đơn của người khác).
  * Xem chi tiết từng đơn hàng và trạng thái vận chuyển.
  * Tự hủy đơn hàng khi đơn đang ở trạng thái chờ xác nhận (`pending`).

### 2. Quản Trị Viên (Admin Panel)
* **Dashboard Thống Kê:** Tổng doanh thu đơn hoàn thành, tổng số đơn, số đơn hôm nay, đơn chờ xử lý, số lượng khách hàng, số lượng món ăn và danh sách 5 đơn mới nhất.
* **Quản Lý Đơn Hàng:** Xem danh sách, lọc theo trạng thái, tìm kiếm, cập nhật trạng thái đơn theo State Machine nghiêm ngặt (`pending -> confirmed -> preparing -> delivering -> completed` hoặc `cancelled`), tự động đánh dấu đã thanh toán COD khi hoàn tất.
* **Quản Lý Món Ăn:** Thêm món mới, cập nhật giá/thông tin/ảnh, bật/tắt trạng thái mở bán (`is_available`). Không hard-delete món ăn trong luồng nghiệp vụ.
* **Quản Lý Danh Mục:** Thêm/sửa danh mục, tự động tạo slug, chặn xóa danh mục khi đang có món ăn.
* **Quản Lý Người Dùng:** Tìm kiếm khách hàng, khóa/mở khóa tài khoản (ngăn admin tự khóa tài khoản của chính mình).

---

## 🛠️ Công Nghệ Sử Dụng

| Thành phần | Công nghệ |
| :--- | :--- |
| **Backend** | PHP 8.3+, Laravel 13 |
| **Frontend** | Blade Templates, HTML5, CSS3, JavaScript ES6 thuần |
| **Styling & Assets** | Plus Jakarta Sans, FontAwesome 6, Vite 8, Laravel Vite Plugin |
| **Database** | MySQL 8.4 (Local & Production), SQLite In-Memory (Automated Testing) |
| **Authentication** | Laravel Session Authentication + CSRF Protection |
| **Quality & Linting** | Laravel Pint, PHPUnit 12 |

---

## 📁 Cấu Trúc Thư Mục

```text
app/
  Enums/                 UserRole, OrderStatus, PaymentMethod, PaymentStatus
  Http/
    Controllers/         Public & Customer Controllers
      Admin/             Admin Controllers (Dashboard, Order, Food, Category, User)
      Auth/              Authentication Controllers (Login, Register, Password Reset)
    Middleware/          EnsureUserIsActive, EnsureUserIsAdmin
    Requests/            Form Requests validate từng tác vụ (Admin, Auth, Order, User)
    Resources/           JSON API Resources (User, Category, Food, Order, OrderItem)
  Models/                User, Category, Food, Order, OrderItem
  Policies/              OrderPolicy
  Services/              CheckoutService, OrderStatusService
database/
  factories/             Model Factories
  migrations/            Database Migrations
  seeders/               Database Seeders (Admin, Category, Food)
docs/                    Tài liệu kỹ thuật hệ thống (Architecture, Database, Routes, Order Flow...)
resources/
  views/                 Giao diện Blade (layouts, auth, user, orders, admin, home)
routes/web.php           Toàn bộ Web và REST API v1 routes
tests/                   98 Automated Feature & Unit Tests (100% PASS)
```

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Local

### 1. Cài đặt Dependencies
```powershell
composer install
npm ci
```

### 2. Cấu hình Môi Trường
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 3. Khởi tạo Cơ Sở Dữ Liệu
Khởi động MySQL (qua Laragon/XAMPP), tạo database `food_order`, sau đó chạy:
```powershell
php artisan migrate --seed
```
> Tài khoản Admin mặc định tạo sẵn từ Seeder:
> - **Email:** `admin@foodorder.test`
> - **Mật khẩu:** Thiết lập qua biến `DEMO_ADMIN_PASSWORD` hoặc chỉnh sửa trực tiếp.

### 4. Build Assets Frontend
```powershell
npm run build
```

### 5. Khởi động Web Server
```powershell
php artisan serve
```
Truy cập ứng dụng tại: `http://127.0.0.1:8000`

---

## 🧪 Kiểm Thử Tự Động (Automated Testing)

Chạy toàn bộ bộ kiểm thử:
```powershell
php artisan test
```
* **Kết quả hiện tại:** **98 tests**, **354 assertions**, **100% PASS**.

Kiểm tra chuẩn code (Pint):
```powershell
php vendor/bin/pint --test
```

---

## 📚 Tài Liệu Kỹ Thuật Chi Tiết

* [Kiến Trúc Hệ Thống](docs/ARCHITECTURE.md)
* [Thiết Kế Cơ Sở Dữ Liệu](docs/DATABASE.md)
* [Danh Sách Route & API Contract](docs/ROUTES.md)
* [Quy Trình & State Machine Đơn Hàng](docs/ORDER_FLOW.md)
* [Kế Hoạch & Tiến Độ Implementation](docs/IMPLEMENTATION_PLAN.md)
* [Chiến Lược Kiểm Thử](docs/TESTING.md)
* [Hướng Dẫn Triển Khai Production](docs/DEPLOYMENT.md)
