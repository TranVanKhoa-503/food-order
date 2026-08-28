# Deployment

## 1. Trạng thái sẵn sàng

Source hiện tại **chưa sẵn sàng production** vì Auth/Admin chưa có và checkout còn giả lập. Schema/test foundation đã hoàn thành trong Sprint 0. Tài liệu này là quy trình mục tiêu cho MVP sau Sprint 7, dùng đúng Laravel + Blade + Vite hiện có.

Không dùng Docker, Redis, microservices hoặc queue worker trong quy trình này.

## 2. Mô hình triển khai

Một deployment gồm:

- Một PHP/Laravel application.
- Blade templates và JSON endpoints cùng domain.
- Static assets do Vite build vào `public/build`.
- Một database.
- Web server trỏ document root vào thư mục `public/`.

Frontend không deploy thành service riêng; Blade và Vite assets đi cùng backend artifact.

## 3. Yêu cầu server

- PHP 8.3+.
- Composer.
- PHP extensions Laravel yêu cầu và PDO driver cho database đã chọn.
- Node.js/npm ở build machine hoặc server build.
- Web server/hosting chạy PHP, document root cấu hình tới `public/`.
- HTTPS cho production.
- Quyền ghi cho `storage/` và `bootstrap/cache/`.

Không dùng `php artisan serve` làm production web server.

## 4. Build dependencies

Tại thư mục release:

```powershell
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Repo đã có `package-lock.json`; deployment dùng `npm ci` để cài đúng dependency đã khóa.

`npm run build` tạo asset trong `public/build`. Trang chủ hiện chưa dùng `@vite`, nhưng sau Sprint 6 các Blade views sẽ tham chiếu build này.

Không đưa `node_modules` vào web root hoặc public artifact.

## 5. Environment variables

Tạo `.env` riêng trên server. Không copy `.env` production vào Git.

### Application

```dotenv
APP_NAME="Food Order"
APP_ENV=production
APP_KEY=<giá trị sinh an toàn>
APP_DEBUG=false
APP_URL=https://food-order.example.com
APP_LOCALE=vi
APP_FALLBACK_LOCALE=vi
```

`APP_KEY` phải được giữ ổn định giữa các release. Không chạy `key:generate` lại trên mỗi deployment vì sẽ làm mất khả năng giải mã cookie/data cũ.

`config/app.php` đọc `APP_TIMEZONE`; local/production mặc định dùng `Asia/Ho_Chi_Minh`.

### MySQL production

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=food_order
DB_USERNAME=<database-user>
DB_PASSWORD=<strong-password>
```

MySQL 8 là lựa chọn cho local và production. Automated tests có thể dùng SQLite in-memory, nhưng trước release vẫn phải chạy migration/test smoke trên MySQL staging để phát hiện khác biệt type/constraint.

Database user chỉ cần quyền trên database của ứng dụng; không dùng root.

### Session/cache/queue

```dotenv
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=sync
```

- Sessions/cache dùng các bảng migration đã có.
- Project chưa có background job, vì vậy `sync` đủ và không cần queue worker.
- `SESSION_SECURE_COOKIE=true` chỉ dùng khi site chạy HTTPS.

### Logging và mail

```dotenv
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="Food Order"
```

`MAIL_MAILER=log` phù hợp khi tính năng email chưa phát hành. Nếu Sprint 1 phát hành forgot-password cho người dùng thật, phải cấu hình mail transport thực tế trước production; không được giữ reset link chỉ trong log.

Không cần khai báo AWS/Redis khi project không dùng chúng.

## 6. Database production

### Trước migration

1. Backup database hiện tại.
2. Kiểm tra release notes của migrations.
3. Xác nhận migration không dùng `migrate:fresh`, `db:wipe` hoặc thao tác mất dữ liệu.
4. Chạy migration trên staging/copy dữ liệu trước.

### Chạy migration

```powershell
php artisan migrate --force
```

`migrate:fresh --seed` chỉ dành cho local/test, tuyệt đối không chạy production.

Production không chạy demo seeders. Admin đầu tiên phải được tạo bằng quy trình kiểm soát riêng hoặc seeder production-safe, không dùng mật khẩu mặc định.

## 7. Backend deployment

Trình tự một release:

1. Tạo database backup.
2. Bật maintenance mode nếu release có migration không tương thích ngược.
3. Đưa source/release artifact mới lên server.
4. Cài Composer dependencies production.
5. Bảo toàn `.env` và `APP_KEY`.
6. Chạy frontend build hoặc đưa `public/build` đã build lên.
7. Chạy `php artisan migrate --force`.
8. Bảo đảm quyền ghi cho `storage/` và `bootstrap/cache/`.
9. Clear/cache lại cấu hình:

```powershell
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

10. Tắt maintenance mode.
11. Chạy smoke test.

Nếu dùng maintenance mode, các lệnh Laravel tương ứng là:

```powershell
php artisan down
php artisan up
```

Không để ứng dụng ở maintenance mode nếu release fail trước bước migrate; rollback code hoặc khôi phục release trước theo kế hoạch.

## 8. Frontend deployment

Frontend hiện là Blade:

- Blade files nằm trong `resources/views`.
- Vite entry nằm ở `resources/css/app.css` và `resources/js/app.js`.
- Build output nằm trong `public/build`.
- Không cần Node.js chạy thường trực sau khi build.

Sau Sprint 6, kiểm tra mọi layout dùng `@vite` đúng entry. Không hardcode URL của dev Vite server ở production.

Trang chủ hiện phụ thuộc Google Fonts, Font Awesome CDN và ảnh Unsplash. Production cần:

- Cho phép outbound/browser truy cập các domain đó, hoặc chuyển asset quan trọng về local trong một sprint riêng.
- Có fallback khi ảnh remote hỏng.
- Không coi ảnh seed remote là media production lâu dài.

## 9. File storage

Source hiện chỉ lưu URL ảnh trong `foods.image`; chưa có upload flow. Vì vậy release hiện không bắt buộc `storage:link`.

Nếu Sprint sau thêm upload vào `storage/app/public`, lúc đó mới:

- Chạy `php artisan storage:link`.
- Backup uploaded files.
- Validate MIME/size và không tin filename của user.

Không thêm storage service ngoài khi chưa có yêu cầu.

## 10. Production hardening

- `APP_DEBUG=false`.
- HTTPS và secure session cookie.
- Web root bắt buộc là `public/`, không phải project root.
- Không expose `.env`, logs, database SQLite hoặc source private.
- CSRF cho toàn bộ write endpoints.
- Session regenerate khi login/logout.
- Authorization backend cho admin/ownership.
- Validation payload và giới hạn pagination.
- Không log password, reset token hoặc database credential.
- Không tin tổng tiền từ frontend.
- Backup database định kỳ và kiểm tra restore.
- Theo dõi `storage/logs/laravel.log` bằng cơ chế của hosting.
- Dọn/cấu hình log rotation theo server.

## 11. Smoke test sau deploy

1. `GET /up` trả thành công.
2. `GET /` trả 200 và load catalog.
3. Register/login/logout hoạt động.
4. User thường không vào admin API.
5. Admin tạo/tắt một food thử nghiệm.
6. Tạo order test COD.
7. Order đi đúng state machine tới completed.
8. Validation trả JSON, không redirect HTML ngoài dự kiến.
9. `APP_DEBUG=false` không lộ stack trace khi tạo lỗi.
10. CSS/JS `public/build` load không 404.

Xóa hoặc hủy dữ liệu smoke test theo nghiệp vụ; không dùng lệnh xóa database.

## 12. Rollback

- Giữ release artifact trước để đổi code lại nhanh.
- Backup DB trước migration.
- Ưu tiên migration tiến về phía trước; không tự động chạy `migrate:rollback` nếu migration down có thể mất dữ liệu.
- Nếu code mới lỗi nhưng migration tương thích, rollback code và giữ database.
- Nếu migration gây lỗi dữ liệu, khôi phục từ backup đã xác minh.

## 13. Release gate

Chỉ deploy production khi:

- Sprint 0–7 acceptance criteria hoàn thành.
- `php artisan test` xanh.
- `npm run build` xanh.
- Migration chạy từ DB rỗng và trên MySQL staging.
- Có backup/restore plan.
- Không còn checkout alert giả.
- Không còn password/default admin credential không an toàn.
- README và tài liệu này khớp source release.
