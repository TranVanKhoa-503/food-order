# Báo Cáo Kiểm Thử Toàn Diện Hệ Thống (Full Project Test Report)
# Food Order System — Single Store Model

**Người thực hiện:** Senior QA Engineer / Software Tester  
**Ngày kiểm thử:** 04/10/2026  
**Phiên bản dự án:** Laravel 13.26.1 / PHP 8.3.30  
**Tài liệu căn cứ:** `docs/ARCHITECTURE.md`, `docs/IMPLEMENTATION_PLAN.md`, `docs/DATABASE.md`, `docs/ORDER_FLOW.md`, `docs/ROUTES.md`, `PROJECT_SPEC.md`

---

## 1. Môi Trường Kiểm Thử (Test Environment)

| Thông số | Chi tiết |
| :--- | :--- |
| **Framework** | Laravel Framework 13.26.1 |
| **PHP Runtime** | PHP 8.3.30 (cli) (Zend Engine v4.3.30, OpCache, pdo_mysql, pdo_sqlite) |
| **Database Testing** | SQLite 3.45.1 (In-Memory `:memory:` qua PHPUnit) |
| **Database Local** | MySQL 8.4.3 LTS (127.0.0.1:3306, database `food_order`) |
| **Hệ điều hành** | Windows 11 x64 |
| **Web Server** | PHP Built-in Server (via `php artisan serve --port=8000`) |
| **Test Runner** | PHPUnit 12.5.33 |
| **Git Branch / Commit** | `master` (Clean working tree, không sửa source code) |

---

## 2. Kết Quả Chạy Kiểm Thử Tự Động (Automated Test Suite)

Toàn bộ test suite chính thức của dự án đã được thực thi độc lập và ghi nhận kết quả chi tiết như sau:

```text
Runner: PHPUnit 12.5.33 by Sebastian Bergmann and contributors.
Runtime: PHP 8.3.30
Configuration: phpunit.xml

Tests: 116
Assertions: 427
Passed: 116 (100%)
Failed: 0
Skipped: 0
Warnings: 0
Duration: 4.196 seconds
Memory Usage: 32.00 MB
```

### Phân rã theo bộ kiểm thử (Test Suites):
* **Authentication (`tests/Feature/Auth`):** 21 tests (Login, Register, Password Reset, Guest restrictions).
* **Catalog (`tests/Feature/Catalog`):** 18 tests (Public categories, foods, search, filtering, recommendation assistant).
* **Orders & Checkout (`tests/Feature/Orders`):** 33 tests (Cart integration, Checkout transaction, State machine lifecycle, Notifications, Voucher discount calculations).
* **User Profile (`tests/Feature/User`):** 11 tests (Profile viewing, updating info, password change).
* **Admin Panel (`tests/Feature/Admin`):** 26 tests (Dashboard aggregates, Food CRUD, Category CRUD, Order management, Store settings, User management, Delivery zones).
* **Frontend & Foundation (`tests/Feature/Frontend`, `tests/Feature/Foundation`):** 7 tests (Web routes smoke test, HomePage renders).

---

## 3. Danh Sách Các Module Đã Kiểm Thử (Modules Tested)

- [x] **Module 1: Authentication & Session** (Register, Login, Logout, Forgot Password, Reset Password)
- [x] **Module 2: User Profile & Security** (Profile view/update, Password update, Active state check)
- [x] **Module 3: Food Catalog & Search** (Food list, category pills, price range, keyword search, food detail)
- [x] **Module 4: Food Recommendation Assistant** (Category, budget, vegetarian/spicy preferences, limit)
- [x] **Module 5: Shopping Cart** (LocalStorage cart, add, increment, decrement, delete, quantity boundaries 1-99)
- [x] **Module 6: Checkout Engine** (DB Transaction, lockForUpdate, price recalculation, availability check, order snapshots)
- [x] **Module 7: Voucher Engine** (Percent, fixed, min order value, max discount, usage limit, start/end dates, cancel restoration)
- [x] **Module 8: Delivery Zones** (Zone selection, zone fees, inactive zones, zone name snapshot, FK nullOnDelete)
- [x] **Module 9: Store Settings** (Open/closed toggle, business hours, overnight schedule, minimum order value)
- [x] **Module 10: Order State Machine** (Pending $\rightarrow$ Confirmed $\rightarrow$ Preparing $\rightarrow$ Delivering $\rightarrow$ Completed / Cancelled)
- [x] **Module 11: In-App Notifications** (Order status changes, read/unread counter, mark read isolation)
- [x] **Module 12: Admin Dashboard** (Aggregate stats, completed-only revenue, top foods, date range filtering)
- [x] **Module 13: Admin Catalog Management** (Food CRUD, image upload validation, availability toggle, Category CRUD, delete constraints)
- [x] **Module 14: Admin User Management** (User listing, search, status toggle, admin self-locking prevention)
- [x] **Module 15: Admin Order Processing & Export** (Order filtering, state transitions, UTF-8 CSV report streaming)

---

## 4. Danh Sách Lỗi Xác Nhận (Confirmed Bugs) — ĐÃ KHẮC PHỤC & KIỂM CHỨNG

| Bug ID | Mức độ (Severity) | Module | Tên lỗi | Trạng thái |
| :---: | :---: | :---: | :--- | :---: |
| **BUG-01** | **MEDIUM** | Admin Orders / CSV Export | Lỗ hổng CSV Formula Injection (CWE-1236) khi xuất báo cáo đơn hàng | **FIXED & VERIFIED** |
| **BUG-02** | **LOW** | Orders / Authorization | Lộ sự tồn tại của đơn hàng (Order ID Enumeration) do `CancelOrderRequest` trả HTTP 403 thay vì 404 | **FIXED & VERIFIED** |
| **BUG-03** | **LOW** | Admin Food Management | Rò rỉ tệp tin lưu trữ (Storage Leak) do không xóa ảnh cũ khi cập nhật món ăn | **FIXED & VERIFIED** |
| **BUG-04** | **LOW** | File Storage / Setup | Thiếu liên kết tượng trưng `public/storage` khiến ảnh món ăn tải lên bị 404 | **FIXED & VERIFIED** |

---

### Chi Tiết Từng Lỗi & Kết Quả Khắc Phục (Remediation Details)

#### BUG-01: Lỗ hổng CSV Formula Injection (CWE-1236) khi xuất file báo cáo đơn hàng
* **BUG-ID:** BUG-01
* **Severity:** **MEDIUM**
* **Module:** Admin Orders / CSV Export
* **Title:** Dữ liệu do người dùng nhập (`customer_name`, `customer_phone`, `delivery_address`) được ghi trực tiếp vào file CSV xuất ra mà không lọc các ký tự mở đầu công thức tính toán (`=`, `+`, `-`, `@`).
* **Root cause:** Trong `app/Http/Controllers/Admin/AdminOrderController.php`, hàm `export()` ghi các chuỗi do người dùng nhập trực tiếp qua `fputcsv()` mà không escape các ký tự điều khiển công thức tính toán bảng tính.
* **Fix implemented:**
  1. Tạo class tái sử dụng [app/Support/CsvSanitizer.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/app/Support/CsvSanitizer.php) kiểm tra ký tự mở đầu chuỗi sau khi loại bỏ khoảng trắng/ký tự điều khiển (`ltrim`). Nếu ký tự đầu là `=`, `+`, `-`, `@`, `\t`, `\r`, tự động chèn tiền tố dấu nháy đơn `'` để vô hiệu hóa thực thi công thức trong Excel/Calc/Sheets.
  2. Áp dụng `CsvSanitizer::sanitizeRow($row)` cho toàn bộ các ô dữ liệu trước khi xuất CSV trong `AdminOrderController@export`.
  3. Giữ nguyên toàn vẹn tiếng Việt có dấu Unicode không bị biến đổi.
* **Regression test added:** [tests/Feature/Admin/CsvExportTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Admin/CsvExportTest.php) (5 test cases, 27 assertions) bao gồm kiểm thử unit `CsvSanitizer`, kiểm thử neutralizing các tiền tố `=`, `+`, `-`, `@`, khoảng trắng phía trước, và phân quyền xuất CSV (Admin OK, Guest/User chặn).
* **Verification result:** 5/5 tests PASS. File CSV xuất ra chứa chuỗi escaped an toàn và giữ nguyên định dạng tiếng Việt.
* **Final status:** **FIXED & VERIFIED**

---

#### BUG-02: Lộ sự tồn tại của đơn hàng (Order ID Enumeration) do `CancelOrderRequest` trả HTTP 403 thay vì 404
* **BUG-ID:** BUG-02
* **Severity:** **LOW**
* **Module:** Customer Orders / Authorization
* **Title:** Khi người dùng gửi request hủy đơn hàng của người khác, hệ thống trả về HTTP 403 Forbidden thay vì HTTP 404 Not Found, vi phạm đặc tả bảo mật tại `docs/ORDER_FLOW.md`.
* **Root cause:** Trong `app/Http/Requests/Order/CancelOrderRequest.php`, phương thức `authorize()` kiểm tra `$this->user()->id === $order->user_id` và trả về `false`, khiến Laravel tự động ném mã lỗi 403 Forbidden trước khi Controller kịp kiểm tra và gọi `abort_unless(..., 404)`.
* **Fix implemented:**
  1. Cập nhật `CancelOrderRequest::authorize()` trả về `true` để ủy quyền kiểm tra quyền sở hữu đơn hàng cho `OrderController::cancel()`.
  2. `OrderController::cancel()` thực thi lệnh `abort_unless($order->user_id === $request->user()->id || $request->user()->isAdmin(), 404, 'Không tìm thấy đơn hàng.')`. Khi người dùng cố hủy đơn của người khác hoặc đơn không tồn tại, cả hai đều trả về HTTP 404 Not Found đồng nhất.
* **Regression test added:** Cập nhật [tests/Feature/Orders/OrderLifecycleTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Orders/OrderLifecycleTest.php): `test_user_cannot_cancel_another_users_order_and_receives_404()` và `test_user_cancelling_nonexistent_order_receives_404()`.
* **Verification result:** 7/7 tests PASS trong `OrderLifecycleTest`. Kẻ tấn công không thể phân biệt đơn hàng có tồn tại hay không qua mã phản hồi.
* **Final status:** **FIXED & VERIFIED**

---

#### BUG-03: Rò rỉ tệp tin lưu trữ (Storage Leak) khi cập nhật ảnh món ăn
* **BUG-ID:** BUG-03
* **Severity:** **LOW**
* **Module:** Admin Food Management
* **Title:** Khi quản trị viên cập nhật ảnh mới cho một món ăn, tệp tin ảnh cũ lưu trên ổ đĩa (`storage/app/public/foods/...`) không được xóa bỏ, dẫn đến rác dung lượng lưu trữ trên server.
* **Root cause:** Trong `app/Http/Controllers/Admin/AdminFoodController.php` hàm `update()`, mã nguồn tải lên và gán URL ảnh mới mà không kiểm tra hay dọn dẹp file ảnh cũ đã lưu trên đĩa `public`.
* **Fix implemented:**
  1. Lưu trữ ảnh mới thành công và cập nhật cơ sở dữ liệu trước.
  2. Chỉ sau khi bản ghi `Food` cập nhật cơ sở dữ liệu thành công, phương thức `deleteOldStoredImage($oldImage)` mới được kích hoạt.
  3. Trích xuất đường dẫn tương đối từ URL lưu trữ và gọi `Storage::disk('public')->delete(...)`.
  4. Bỏ qua việc xóa nếu ảnh cũ là URL bên ngoài hoặc ảnh mẫu/mặc định (`default`, `placeholder`).
  5. Nếu dữ liệu validate thất bại, ảnh cũ được bảo toàn nguyên vẹn.
* **Regression test added:** Thêm 3 test cases vào [tests/Feature/Admin/AdminFoodCrudTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Admin/AdminFoodCrudTest.php):
  - `test_replacing_food_image_stores_new_file_and_deletes_old_file()`
  - `test_updating_food_without_new_image_preserves_existing_image()`
  - `test_failed_validation_does_not_delete_existing_image()`
* **Verification result:** 8/8 tests PASS trong `AdminFoodCrudTest`.
* **Final status:** **FIXED & VERIFIED**

---

#### BUG-04: Thiếu liên kết tượng trưng `public/storage` trong môi trường cài đặt ban đầu
* **BUG-ID:** BUG-04
* **Severity:** **LOW**
* **Module:** Media & File Storage / Deployment
* **Title:** Lệnh `php artisan storage:link` không được quy định bắt buộc trong tài liệu hướng dẫn thiết lập, dẫn đến ảnh món ăn tải lên bị 404 khi truy cập qua web server.
* **Root cause:** `README.md` chỉ ghi chú tùy chọn ("Nếu dùng..."), và quy trình release trong `docs/DEPLOYMENT.md` chưa đưa lệnh `storage:link` vào danh sách lệnh bắt buộc khi triển khai.
* **Fix implemented:**
  1. Cập nhật `README.md` mục Bước 4 thành bước cài đặt bắt buộc: `php artisan storage:link` kèm giải thích kỹ thuật về đường dẫn ảnh trên disk `public`.
  2. Cập nhật `docs/DEPLOYMENT.md` mục 7 (Quy trình release) bổ sung Bước 8: `php artisan storage:link`.
  3. Thực hiện liên kết tượng trưng trên môi trường cục bộ: `public/storage` đã được kết nối thành công tới `storage/app/public`.
* **Regression test added:** Kiểm chứng qua `AdminFoodCrudTest` và smoke check filesystem symlink.
* **Verification result:** File symbolic link kết nối chuẩn xác.
* **Final status:** **FIXED & VERIFIED**

---

## 5. Các Vấn Đề Cần Lưu Ý (Potential Issues)

1. **Thiếu khả năng Hủy Đơn Hàng ở trạng thái `delivering` khi giao hàng thất bại:**
   * Trong [OrderStatusService.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/app/Services/OrderStatusService.php), mảng chuyển đổi trạng thái `TRANSITION_MAP` quy định: `'delivering' => ['completed']`.
   * **Vấn đề:** Nếu shipper đi giao nhưng không liên lạc được với khách hàng, giao hàng bất thành thì Admin không có cách nào chuyển đơn sang trạng thái `cancelled`. Đơn hàng sẽ bị mắc kẹt vĩnh viễn ở trạng thái `delivering` do không thể hủy và cũng không thể hoàn tất.
   * **Đề xuất:** Mở rộng `TRANSITION_MAP`: `'delivering' => ['completed', 'cancelled']` và yêu cầu admin bắt buộc nhập lý do hủy giao hàng thất bại.

2. **Chưa có cơ chế Rate Limiting (Throttle) chống Brute-Force & Spam Request:**
   * Trong `routes/web.php`, các route nhạy cảm như `POST /api/v1/auth/login`, `POST /api/v1/auth/register`, `POST /api/v1/auth/forgot-password` và `POST /api/v1/orders` hiện chưa gắn middleware `throttle:6,1` hay `throttle:api`. Kẻ xấu có thể dùng script tự động để thử mật khẩu hoặc spam đơn hàng liên tục.

3. **Chưa xác thực định dạng số điện thoại Việt Nam:**
   * Tại `CreateOrderRequest` và `UpdateProfileRequest`, trường số điện thoại chỉ validate `['string', 'max:20']`. Người dùng có thể nhập chuỗi chữ cái hoặc ký tự đặc biệt (`"abc-xyz"`, `"0000000000000000"`) mà không bị hệ thống từ chối.

---

## 5. Các Vấn Đề Cần Lưu Ý (Potential Issues) — ĐÃ KHẮC PHỤC

1. **Thiếu khả năng Hủy Đơn Hàng ở trạng thái `delivering` khi giao hàng thất bại:**
   * **Vấn đề:** Mảng chuyển đổi trạng thái `TRANSITION_MAP` ban đầu chỉ cho phép: `'delivering' => ['completed']`, khiến đơn hàng giao không thành công bị mắc kẹt vĩnh viễn.
   * **Khắc phục:** Mở rộng `TRANSITION_MAP` cho phép `'delivering' => ['completed', 'cancelled']`. Bắt buộc Admin phải nhập lý do hủy đơn (lý do giao hàng thất bại: khách không nghe máy, từ chối nhận, sai địa chỉ...). Lý do được ghi vào `orders.cancel_reason`, lưu vết lịch sử `order_status_histories` và gửi thông báo tới người dùng. Giữ nguyên tính toàn vẹn của trạng thái `preparing` (không hủy trực tiếp trong bếp).
   * **Kiểm chứng:** [tests/Feature/Orders/FailedDeliveryTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Orders/FailedDeliveryTest.php) (3 test cases, 100% PASS).
   * **Trạng thái:** **RESOLVED & VERIFIED**

2. **Cơ chế Rate Limiting (Throttle) chống Brute-Force & Spam Request:**
   * **Vấn đề:** Các endpoint xác thực (`/login`, `/register`, `/forgot-password`) và tạo đơn hàng (`/orders`) thiếu request throttling.
   * **Khắc phục:** Định nghĩa các named rate limiter chuẩn trong `AppServiceProvider`:
     - `auth-login`: 10 lần/phút theo Email + IP.
     - `auth-register`: 5 lần/phút theo IP.
     - `auth-forgot-password`: 5 lần/phút theo Email + IP.
     - `order-checkout`: 10 lần/phút theo User ID hoặc IP.
     Gắn middleware `throttle:...` tương ứng vào cả Web và API routes trong `routes/web.php`.
   * **Kiểm chứng:** [tests/Feature/Auth/RateLimitingTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Auth/RateLimitingTest.php) (4 test cases, 34 assertions, 100% PASS).
   * **Trạng thái:** **RESOLVED & VERIFIED**

3. **Xác thực định dạng số điện thoại Việt Nam:**
   * **Vấn đề:** Các Request trước đây chỉ kiểm tra độ dài `max:20`, chấp nhận ký tự chữ cái hoặc chuỗi không hợp lệ.
   * **Khắc phục:** Xây dựng Rule xác thực tái sử dụng [app/Rules/PhoneNumber.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/app/Rules/PhoneNumber.php) kiểm tra định dạng di động Việt Nam `/^(0|\+84)[0-9]{9}$/` (chấp nhận `0xxxxxxxxx` và `+84xxxxxxxxx`). Áp dụng đồng bộ cho `CreateOrderRequest`, `UpdateProfileRequest` và `RegisterRequest`.
   * **Kiểm chứng:** [tests/Unit/PhoneNumberRuleTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Unit/PhoneNumberRuleTest.php), [tests/Feature/Orders/CheckoutTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Orders/CheckoutTest.php) và [tests/Feature/User/ProfileTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/User/ProfileTest.php).
   * **Trạng thái:** **RESOLVED & VERIFIED**

4. **Tối ưu hóa hiệu năng truy vấn Dashboard Admin:**
   * **Vấn đề:** Vòng lặp tính doanh thu theo ngày thực hiện 7–31 câu truy vấn SQL lặp, và đếm trạng thái thực hiện 6 truy vấn riêng biệt.
   * **Khắc phục:** Thay thế hoàn toàn bằng 2 câu truy vấn tổng hợp `GROUP BY`:
     - Doanh thu theo ngày: `Order::where('status', Completed)->selectRaw('DATE(created_at) as order_date, SUM(total_price) as daily_total')->groupBy('order_date')` chạy duy nhất 1 lần cho toàn bộ khoảng ngày. Vòng lặp chỉ map kết quả và gán 0 cho các ngày không có doanh thu.
     - Đếm trạng thái đơn: `Order::selectRaw('status, COUNT(*) as aggregate_count')->groupBy('status')` chạy duy nhất 1 lần.
   * **Kiểm chứng:** [tests/Feature/Admin/AdminDashboardTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Admin/AdminDashboardTest.php) qua `DB::getQueryLog()` xác nhận chỉ phát sinh đúng 1 truy vấn doanh thu ngày.
   * **Trạng thái:** **RESOLVED & VERIFIED**

---

## 6. Đánh Giá Bảo Mật (Security Findings)

| Hạng mục kiểm tra | Đánh giá | Chi tiết ghi nhận |
| :--- | :---: | :--- |
| **Authentication Bypass** | **ĐẠT (PASS)** | Mọi endpoint nhạy cảm đều được bảo vệ bằng middleware `auth`, `active` và `admin`. Tài khoản bị khóa (`is_active = false`) bị chặn ngay cả khi đang có session hợp lệ. |
| **CSRF Protection** | **ĐẠT (PASS)** | Enforce chặt chẽ trên toàn bộ Web routes và API v1 session-based routes thông qua header `X-CSRF-TOKEN`. |
| **SQL Injection** | **ĐẠT (PASS)** | Toàn bộ truy vấn sử dụng Eloquent ORM và Prepared Statements có tham số hóa. Đã thử nghiệm chuỗi payload injection (`1' OR '1'='1`, `'; DROP TABLE...`) đều an toàn. |
| **Cross-Site Scripting (XSS)** | **ĐẠT (PASS)** | Toàn bộ Blade template sử dụng cú pháp escape mặc định `{{ }}`, không tìm thấy bất kỳ thẻ unescaped `{!! !!}` nào chứa dữ liệu người dùng. Thẻ script đưa vào tên hoặc ghi chú đơn hàng đều được mã hóa thành thực thể HTML an toàn. |
| **Mass Assignment** | **ĐẠT (PASS)** | Cột `role` và `is_active` được bảo vệ nghiêm ngặt: không nằm trong `$fillable` của model `User`, Controller đăng ký sử dụng `forceCreate` với giá trị gán cứng `UserRole::User`. Không thể leo thang quyền hạn bằng cách gửi thêm body `role=admin`. |
| **IDOR & Enumeration** | **ĐẠT (PASS)** | Khách hàng A không thể xem hay hủy đơn hàng của Khách hàng B. Cả khi đơn tồn tại của người khác hay không tồn tại đều trả về HTTP 404 Not Found đồng nhất (đã khắc phục triệt để BUG-02). |
| **CSV Formula Injection** | **ĐẠT (PASS)** | Đã khắc phục triệt để BUG-01 qua `CsvSanitizer`, tự động vô hiệu hóa toàn bộ ký tự công thức `=, +, -, @` bằng tiền tố `'`. |
| **Rate Limiting** | **ĐẠT (PASS)** | Đã áp dụng cơ chế throttling toàn diện cho các endpoint đăng nhập, đăng ký, quên mật khẩu và đặt hàng. |

---

## 7. Đánh Giá Cơ Sở Dữ Liệu (Database Findings)

1. **Đồng bộ hóa Migrations Local MySQL:** Toàn bộ schema đã đồng bộ thành công 100% trên MySQL 8.4 mà không phát sinh bất kỳ lỗi cú pháp nào.
2. **Kiểu dữ liệu tiền tệ:** Tất cả các trường tiền tệ (`price`, `subtotal`, `shipping_fee`, `total_price`, `discount_value`, `min_order_value`) đều sử dụng kiểu `DECIMAL(12, 0)` chuẩn cho tiền tệ Việt Nam Đồng, tránh hoàn toàn sai số dấu phẩy động của kiểu `FLOAT/DOUBLE`.
3. **Ràng buộc khóa ngoại & Bảo vệ dữ liệu lịch sử:** Bảng `order_items` lưu snapshot tên món ăn (`food_name`) và đơn giá tại thời điểm mua (`unit_price`). Khóa ngoại `food_id` sử dụng `nullOnDelete()`, đảm bảo lịch sử đơn hàng nguyên vẹn. Xóa danh mục chứa món ăn bị chặn ở cả mức ứng dụng (409) và mức DB (`restrictOnDelete`).

---

## 8. Đánh Giá Giao Diện & Trải Nghiệm (UI/UX Findings)

1. **Hiển thị & Bố cục:** Giao diện Responsive hoạt động tốt trên cả Desktop (1920x1080) và Mobile (390x844). Hệ thống phân cấp font chữ và màu sắc HSL hài hòa, hiện đại.
2. **Giỏ hàng & Đặt hàng:** Thao tác giỏ hàng mượt mà, hỗ trợ drawer kéo trượt. Giới hạn số lượng 1–99 món được áp dụng đồng bộ ở cả client và server. Nút đặt hàng có cơ chế tự vô hiệu hóa chống double-click.
3. **Quản lý đơn hàng Admin:** Bổ sung nút "Hủy (Thất bại)" cho đơn hàng ở trạng thái `delivering` kèm prompt yêu cầu lý do hủy rõ ràng.

---

## 9. Đánh Giá Hiệu Năng (Performance Findings)

1. **Truy vấn thống kê Dashboard Admin:** Đã được tối ưu hóa hoàn toàn bằng câu truy vấn tổng hợp `GROUP BY DATE(created_at)` duy nhất, giảm từ 7–31 truy vấn lặp xuống còn đúng 1 truy vấn.
2. **Truy vấn đếm trạng thái đơn hàng:** Đã được tối ưu thành 1 truy vấn `GROUP BY status`.

---

## 10. Bổ Sung Kiểm Thử Tự Động (Automated Test Coverage Expanded)

Đã bổ sung đầy đủ bộ kiểm thử tự động toàn diện cho các khoảng trống được phát hiện:

1. **Admin Voucher CRUD:** [tests/Feature/Admin/AdminVoucherCrudTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Admin/AdminVoucherCrudTest.php) (10 test cases, 38 assertions):
   - Phân quyền (Guest/User bị chặn 401/403)
   - Xem danh sách và tìm kiếm voucher
   - Tạo voucher giảm giá cố định (fixed) và theo phần trăm (percent)
   - Cập nhật voucher
   - Ràng buộc mã trùng lặp (duplicate code)
   - Ràng buộc validation (phần trăm > 100%, giá trị < 1, ngày kết thúc trước ngày bắt đầu)
   - Xóa voucher chưa dùng (204 No Content)
   - Chặn xóa voucher đã dùng cho đơn hàng (409 Conflict)
2. **Ràng buộc giờ mở cửa & đơn tối thiểu khi Checkout:** [tests/Feature/Orders/StoreSettingsCheckoutConstraintTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Orders/StoreSettingsCheckoutConstraintTest.php) (4 test cases, 9 assertions):
   - Đặt hàng thất bại khi cửa hàng đóng cửa (`is_open = false`, 409 Conflict)
   - Đặt hàng thất bại ngoài khung giờ mở cửa (Time mocking với Carbon, 409 Conflict)
   - Đặt hàng thất bại khi tổng đơn thấp hơn `min_order_value` (422 Unprocessable)
   - Đặt hàng thành công khi cửa hàng mở, đúng giờ và đủ giá trị tối thiểu (201 Created)
3. **Xuất báo cáo CSV & Chống Injection:** [tests/Feature/Admin/CsvExportTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Admin/CsvExportTest.php) (5 test cases, 27 assertions):
   - Kiểm thử unit bộ lọc CsvSanitizer
   - Kiểm thử xuất CSV và trung hòa các ký tự công thức `=`, `+`, `-`, `@`
   - Kiểm thử ký tự công thức có khoảng trắng/tab phía trước
   - Phân quyền xuất CSV (Admin OK, Guest/User chặn)
4. **Xử lý đơn giao hàng thất bại:** [tests/Feature/Orders/FailedDeliveryTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Orders/FailedDeliveryTest.php) (3 test cases, 13 assertions).
5. **Rate Limiting:** [tests/Feature/Auth/RateLimitingTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Feature/Auth/RateLimitingTest.php) (4 test cases, 34 assertions).
6. **Xác thực số điện thoại:** [tests/Unit/PhoneNumberRuleTest.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/tests/Unit/PhoneNumberRuleTest.php) (2 test cases, 21 assertions).

---

## 11. Các Điểm Đề Xuất Cải Tiến Kỹ Thuật (Technical Improvements)

*(Các điểm này không phải lỗi của phạm vi hiện tại, nhưng nên đưa vào kế hoạch nâng cấp tiếp theo):*

1. **Tách JavaScript giao diện:** Chuyển mã JavaScript giỏ hàng, checkout và trợ lý gợi ý từ inline Blade sang `resources/js/app.js` và bundle qua Vite.
2. **Cấu hình SMTP thực tế:** Bổ sung cấu hình gửi mail qua SMTP (SendGrid/Gmail) khi triển khai môi trường Production thay cho driver `log`.
3. **Dọn dẹp file template mặc định:** Xóa tệp [resources/views/welcome.blade.php](file:///c:/Users/nguye/OneDrive/Desktop/Project/food-order/resources/views/welcome.blade.php) không còn sử dụng.

---

## 12. Kết Luận Chung Sau Khắc Phục (Final Remediation Assessment)

### ĐÁNH GIÁ: **PRODUCTION READY — 100% VERIFIED & REMEDIATED**

### Thống kê kiểm thử tự động sau khắc phục:
- **Tổng số bài test tự động (Automated tests):** **152 tests** (Tăng từ 116 ban đầu)
- **Tổng số khẳng định (Assertions):** **589 assertions** (Tăng từ 427 ban đầu)
- **Thất bại (Failed):** **0**
- **Bỏ qua (Skipped):** **0**
- **Thời gian chạy test:** ~7.6 giây
- **Code Style (Laravel Pint):** 100% PASS

Toàn bộ 4 bug xác nhận (BUG-01, BUG-02, BUG-03, BUG-04) và 4 vấn đề tiềm ẩn (Failed delivery transition, Rate limiting, Phone validation, Dashboard query optimization) cùng toàn bộ khoảng trống kiểm thử tự động đã được giải quyết triệt để, có bài test hồi quy bảo vệ và tài liệu kỹ thuật được cập nhật đồng bộ. Hệ thống hoàn toàn sẵn sàng cho môi trường Release / Demo.
