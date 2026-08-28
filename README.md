# Food Order

Food Order là website đặt món cho **một cửa hàng**. Source hiện tại đã có trang thực đơn công khai, tìm kiếm/lọc theo danh mục và giỏ hàng lưu trong trình duyệt. Phần xác thực, checkout thật, quản lý đơn hàng và trang quản trị chưa được triển khai.

Phạm vi mục tiêu của phiên bản đầu:

- Khách có thể xem món, đăng ký/đăng nhập, đặt món và xem đơn của mình.
- Quản trị viên quản lý danh mục, món ăn, người dùng và xử lý trạng thái đơn.
- Chỉ hỗ trợ một cửa hàng, hai vai trò `user` và `admin`.
- Thanh toán khi nhận hàng (COD); chưa tích hợp cổng thanh toán.

> Tài liệu trong thư mục `docs/` là source of truth. Sprint 0 đã áp dụng schema foundation; các route nghiệp vụ từ Sprint 1 trở đi vẫn chưa được implement.

## Trạng thái hiện tại

- Route đang hoạt động: `GET /`.
- Dữ liệu trang chủ lấy từ Eloquent models `Category` và `Food`.
- Giỏ hàng dùng `localStorage`; nút đặt món hiện chỉ hiển thị thông báo giả lập.
- Database local được chuẩn hóa sang MySQL; automated tests tiếp tục dùng SQLite in-memory.
- Schema đã chuẩn hóa thành `users`, `categories`, `foods`, `orders`, `order_items`.
- Test foundation dùng `RefreshDatabase` với SQLite in-memory và đang xanh.

## Công nghệ đang sử dụng

| Thành phần | Công nghệ thực tế trong source |
| --- | --- |
| Backend | PHP 8.3+, Laravel 13 |
| Giao diện | Blade, HTML, CSS và JavaScript thuần |
| CSS build | Tailwind CSS 4 qua Vite; trang chủ hiện chủ yếu dùng CSS inline |
| Frontend build | Vite 8, Laravel Vite Plugin |
| ORM | Laravel Eloquent |
| Database local/production | MySQL 8 qua Eloquent |
| Database automated test | SQLite in-memory |
| Authentication dự kiến | Laravel session authentication và CSRF |
| Test | PHPUnit 12 thông qua Laravel test runner |

Source không dùng microservices, Docker, Redis hoặc frontend framework JavaScript.

## Cấu trúc project

```text
app/
  Http/Controllers/      HTTP controllers
  Models/                Eloquent models
bootstrap/app.php        Khai báo route, middleware và exception handling
config/                  Cấu hình Laravel
database/
  factories/             Model factories
  migrations/            Database migrations
  seeders/               Dữ liệu mẫu
docs/                    Tài liệu kỹ thuật mục tiêu
public/                  Web document root
resources/
  css/                   CSS entry của Vite
  js/                    JavaScript entry của Vite
  views/                 Blade templates
routes/web.php           Web routes hiện tại
tests/                   PHPUnit unit/feature tests
```

## Yêu cầu môi trường

- PHP 8.3 trở lên với PDO MySQL và PDO SQLite cho automated tests.
- Composer.
- Node.js và npm.
- MySQL 8.

Trên máy đang phát triển project, PHP/Composer/Node được cài qua Laragon nhưng có thể chưa nằm trong `PATH`. Có thể mở terminal của Laragon hoặc thêm các executable tương ứng vào `PATH` trước khi chạy lệnh dưới đây.

## Cài đặt local

Các lệnh sau dùng PowerShell tại thư mục gốc project.

### 1. Cài PHP dependencies

```powershell
composer install
```

### 2. Tạo file môi trường

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Không commit file `.env`.

### 3. Chuẩn bị MySQL

`.env.example` mặc định dùng MySQL. Tạo database `food_order` bằng MySQL client hoặc công cụ quản lý database của Laragon, sau đó cập nhật credential trong `.env`.

```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS food_order CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Sau đó chạy migration và dữ liệu mẫu:

```powershell
php artisan migrate --seed
```

Seeders có thể chạy lại mà không tạo trùng admin/category/food. Nếu `DEMO_ADMIN_PASSWORD` để trống, admin demo nhận mật khẩu ngẫu nhiên không được hiển thị.

### 4. Cài frontend dependencies

```powershell
npm ci
```

Repo có `package-lock.json` để frontend dependency có thể được cài lặp lại.

## Chạy project

### Backend và trang Blade

```powershell
php artisan serve
```

Mở `http://127.0.0.1:8000`.

### Vite development server

Chạy ở terminal thứ hai khi chỉnh sửa `resources/css/app.css` hoặc `resources/js/app.js`:

```powershell
npm run dev
```

Trang chủ hiện tại chưa gọi `@vite` và vẫn chứa phần lớn CSS/JavaScript trực tiếp trong Blade. Vì vậy backend có thể hiển thị trang chủ mà không chạy Vite; việc nối trang chủ vào asset pipeline nằm trong sprint tích hợp frontend.

### Build frontend

```powershell
npm run build
```

## Biến môi trường cần thiết

| Biến | Local đề xuất | Ý nghĩa |
| --- | --- | --- |
| `APP_NAME` | `Food Order` | Tên ứng dụng |
| `APP_ENV` | `local` | Môi trường chạy |
| `APP_KEY` | sinh bằng Artisan | Khóa mã hóa Laravel |
| `APP_DEBUG` | `true` | Chỉ bật ở local |
| `APP_URL` | `http://127.0.0.1:8000` | URL gốc |
| `APP_LOCALE` | `vi` dự kiến | Ngôn ngữ mặc định |
| `DB_CONNECTION` | `mysql` | Driver database local/production |
| `DB_HOST` | `127.0.0.1` | MySQL host |
| `DB_PORT` | `3306` | MySQL port |
| `DB_DATABASE` | `food_order` | Tên database |
| `DB_USERNAME` | tùy môi trường | MySQL user |
| `DB_PASSWORD` | tùy môi trường | MySQL password, không commit |
| `SESSION_DRIVER` | `database` | Session hiện lưu trong bảng `sessions` |
| `CACHE_STORE` | `database` | Cache hiện lưu trong database |
| `QUEUE_CONNECTION` | `sync` đề xuất | Project chưa có background job/worker |
| `MAIL_MAILER` | `log` | Email chưa được gửi ra ngoài |
| `DEMO_ADMIN_EMAIL` | `admin@foodorder.test` | Email admin chỉ dùng cho development/demo seeder |
| `DEMO_ADMIN_PASSWORD` | để trống hoặc tự đặt | Không dùng làm credential production |

Automated tests dùng SQLite `:memory:` theo `phpunit.xml`. Migration phải được kiểm tra trên MySQL local để tránh phụ thuộc hành vi riêng của SQLite; xem [tài liệu deployment](docs/DEPLOYMENT.md).

## Kiểm tra

```powershell
php artisan test
```

Kết quả cuối Sprint 0: 9 tests, 32 assertions, tất cả đều pass.

## Tài liệu kỹ thuật

- [Kiến trúc](docs/ARCHITECTURE.md)
- [Database](docs/DATABASE.md)
- [Luồng trạng thái đơn hàng](docs/ORDER_FLOW.md)
- [Thiết kế route/API](docs/ROUTES.md)
- [Kế hoạch triển khai](docs/IMPLEMENTATION_PLAN.md)
- [Chiến lược testing](docs/TESTING.md)
- [Deployment](docs/DEPLOYMENT.md)

`PROJECT_SPEC.md` được giữ như bản mô tả ý tưởng ban đầu. Các phần merchant, shipper, voucher, review và roadmap cũ không còn là phạm vi của phiên bản một cửa hàng hiện tại.
