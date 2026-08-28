# Chiến lược testing

## 1. Hiện trạng

Project đang dùng PHPUnit 12 qua Laravel test runner.

- `phpunit.xml` dùng SQLite `:memory:`, session array, cache array và queue sync.
- Test foundation dùng `RefreshDatabase`.
- Đã có test schema, model relationships/casts, bảo toàn order item khi xóa food, middleware admin và trang chủ DB rỗng.
- Kết quả cuối Sprint 0: 9 tests, 32 assertions, tất cả pass.
- Chưa có browser testing framework.

## 2. Nguyên tắc

1. Ưu tiên feature tests cho HTTP/API, validation, authentication và database.
2. Unit test chỉ dùng cho logic thuần như state transition map hoặc tính toán nếu được tách khỏi framework.
3. Mỗi test tự tạo dữ liệu cần thiết; không phụ thuộc database local hoặc thứ tự test.
4. Dùng `RefreshDatabase` cho tests truy cập DB.
5. Dùng factories cho user/order và seeder nhỏ khi cần catalog cố định.
6. Không gọi URL ảnh/CDN hoặc service bên ngoài trong automated tests.
7. Test cả happy path, authorization, validation và business conflict.
8. Mỗi bug quan trọng phải có regression test trước/đồng thời với fix.

## 3. Cấu trúc tests dự kiến

```text
tests/
  Feature/
    Auth/
    User/
    Catalog/
    Orders/
    Admin/
    HomePageTest.php
  Unit/
    OrderStatusTransitionTest.php
  TestCase.php
```

Không cần tạo đủ thư mục/class rỗng từ đầu. Tạo test cùng sprint với endpoint tương ứng.

## 4. Môi trường test

`phpunit.xml` tiếp tục dùng:

- `APP_ENV=testing`.
- `DB_CONNECTION=sqlite`.
- `DB_DATABASE=:memory:`.
- `SESSION_DRIVER=array`.
- `CACHE_STORE=array`.
- `QUEUE_CONNECTION=sync`.
- `MAIL_MAILER=array`.

Mỗi feature test có DB dùng trait:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;
}
```

Chỉ seed dữ liệu mà test cần. Không seed 13 món ở tất cả test nếu chỉ cần một category và một food.

## 5. Chạy test

```powershell
php artisan test
```

Chạy một file:

```powershell
php artisan test tests/Feature/Orders/CreateOrderTest.php
```

Chạy theo filter:

```powershell
php artisan test --filter=customer_can_create_order
```

Trước khi merge/release phải chạy toàn bộ suite, không chỉ filter.

## 6. Authentication tests

| ID | Test case | Kết quả mong đợi |
| --- | --- | --- |
| AUTH-01 | Register với dữ liệu hợp lệ | 201, user được tạo, password hash, session authenticated |
| AUTH-02 | Register trùng email | 422, không tạo user mới |
| AUTH-03 | Register password không confirmed/quá ngắn | 422 |
| AUTH-04 | Login đúng | 200, session được regenerate |
| AUTH-05 | Login sai password | 401, không authenticated |
| AUTH-06 | Login user inactive | 403 |
| AUTH-07 | Logout | 204, session invalidated |
| AUTH-08 | `auth/me` khi chưa login | 401 |
| AUTH-09 | Forgot password email hợp lệ/không tồn tại | Cùng response chung, không lộ account |
| AUTH-10 | Reset token sai/hết hạn | 422 |
| AUTH-11 | Đổi password với current password sai | 422 |
| AUTH-12 | State-changing request thiếu CSRF | 419 trong web/session flow |

Không assert plaintext password ở bất kỳ output nào.

## 7. Authorization tests

| ID | Test case | Kết quả mong đợi |
| --- | --- | --- |
| AUTHZ-01 | Guest tạo order | 401 |
| AUTHZ-02 | User thường gọi admin category CRUD | 403 |
| AUTHZ-03 | User xem order của user khác | 404 |
| AUTHZ-04 | User hủy order của user khác | 404 |
| AUTHZ-05 | User tự chuyển order sang confirmed | Route không tồn tại hoặc 403 |
| AUTHZ-06 | Admin xem/cập nhật mọi order | Thành công theo state rule |
| AUTHZ-07 | Inactive account gọi protected endpoint | 403 |
| AUTHZ-08 | Admin cố tự khóa chính mình | 409 |
| AUTHZ-09 | Request cố mass-assign role qua profile | Role không thay đổi/field bị reject |

Mỗi write route phải có ít nhất một test “user không đủ quyền”.

## 8. Category và Food CRUD tests

### Category

- Admin tạo category hợp lệ: 201.
- Slug tự sinh từ name khi không gửi.
- Duplicate slug: 422.
- User thường create/update/delete: 403.
- Update category không tồn tại: 404.
- Xóa category còn food: 409 và dữ liệu không mất.
- Public list trả category đúng.

### Food

- Admin tạo food với category tồn tại: 201.
- Category không tồn tại: 422/404 theo contract đã chọn.
- Giá âm, price dạng float/string không hợp lệ: 422.
- Admin update/toggle availability: 200.
- Public list chỉ trả `is_available=true`.
- Admin list trả được cả available/unavailable.
- Search/category/min/max price hoạt động.
- Pagination giới hạn `per_page <= 50`.
- Admin deactivate food bằng `is_available=false`; không có hard-delete API.
- Test ở mức model/database xác nhận nếu food bị xóa do bảo trì thì `order_items.food_id` thành null và snapshot vẫn còn.

## 9. Checkout tests

| ID | Test case | Kết quả mong đợi |
| --- | --- | --- |
| CHK-01 | Một item hợp lệ | 201; order pending; totals đúng |
| CHK-02 | Nhiều item hợp lệ | Items và subtotal đúng |
| CHK-03 | Cart rỗng | 422; không tạo order |
| CHK-04 | Food không tồn tại | 422/409; rollback |
| CHK-05 | Food unavailable | 409; rollback |
| CHK-06 | Quantity 0, âm, >99, không phải integer | 422 |
| CHK-07 | Frontend gửi giá/tổng giả | Field bị ignore/reject; backend dùng giá DB |
| CHK-08 | Duplicate food_id | Backend gộp đúng hoặc 422 theo contract chốt |
| CHK-09 | Thiếu tên/phone/address | 422 |
| CHK-10 | Một item lỗi trong cart nhiều item | Không tạo order hoặc item nào |
| CHK-11 | Order code | Unique và đúng format đã chọn |
| CHK-12 | Snapshot | food_name/unit_price lấy từ DB |

Kiểm tra database bằng `assertDatabaseHas`, `assertDatabaseCount` và assert quan hệ, không chỉ assert response.

## 10. Order flow tests

### Transition hợp lệ

- `pending → confirmed`.
- `confirmed → preparing`.
- `preparing → delivering`.
- `delivering → completed`.
- Mỗi trạng thái cho phép → `cancelled` theo quyền.

### Transition không hợp lệ

- `pending → preparing/completed`.
- `confirmed → delivering/completed`.
- `preparing → completed`.
- Mọi transition đi lùi.
- `completed → *`.
- `cancelled → *`.
- Gửi lại status hiện tại.

### Side effects

- User chỉ cancel own pending.
- Admin cancel confirmed trở đi thiếu reason: 422.
- Cancel ghi `cancel_reason/cancelled_at`.
- Complete ghi `completed_at`.
- Complete COD đặt `payment_status=paid`.
- Transition sai trả 409 và DB không đổi.
- Hai request dùng status cũ: chỉ một request thành công; request còn lại trả conflict/current state.

Order transition tests phải phản ánh đúng bảng trong [ORDER_FLOW.md](ORDER_FLOW.md).

## 11. Validation và API error tests

Mỗi Form Request cần test:

- Required fields.
- Kiểu dữ liệu.
- Min/max length/value.
- Foreign key tồn tại.
- Boolean/status values.
- Unknown/mass-assignment sensitive fields.
- Payload nested `items.*`.

Response errors:

- Luôn có `message`.
- Validation có `errors.<field>`.
- API không redirect HTML khi `Accept: application/json`.
- 500 production không lộ stack trace, SQL hoặc environment secret.
- 404 order khác user không lộ resource.
- 409 dùng cho state/business conflict, không dùng 422 tùy tiện.

## 12. Trang chủ và frontend integration

### Feature tests phía Laravel

- `GET /` trả 200 khi DB trống.
- Trang chủ render seeded categories/foods.
- Không render unavailable food.
- Search và category filter đúng.
- Query category không tồn tại không gây 500.
- View không hiển thị rating giả khi review chưa được implement.

### Browser/manual integration

Project chưa cài Dusk, Playwright hoặc Cypress, nên Sprint 6–7 dùng checklist manual; không ghi nhận automated browser test là đã có.

Checklist:

1. Register/login/logout.
2. Tìm/lọc món.
3. Thêm/tăng/giảm/xóa cart.
4. Reload trang vẫn còn localStorage cart.
5. Checkout lỗi không xóa cart.
6. Checkout thành công mới xóa cart.
7. Xem order list/detail/progress.
8. User không thấy/truy cập admin.
9. Admin CRUD catalog và xử lý đúng trạng thái.
10. Responsive desktop/mobile.
11. Ảnh/CDN lỗi có fallback hợp lý.
12. Nội dung tên/ảnh/note đặc biệt không gây DOM XSS.

## 13. Test data

Factories mục tiêu:

- User factory mặc định role user, active.
- State/helper admin tạo role admin.
- Category factory.
- Food factory với category.
- Order factory có user và state rõ ràng.
- OrderItem factory gắn order/food snapshot.

Không dùng ID cố định trừ seeder demo. Test dùng model instance/factory để tránh phụ thuộc thứ tự auto increment.

## 14. Gate trước khi hoàn thành sprint

Mỗi sprint:

1. Tests mới cover acceptance criteria.
2. Full `php artisan test` xanh.
3. Không có skipped/disabled test không có lý do.
4. Migration chạy được từ database rỗng.
5. Nếu sửa frontend asset, `npm run build` xanh.
6. Manual smoke test phần UI bị ảnh hưởng.

Không đánh dấu sprint hoàn thành chỉ vì happy path chạy thủ công.
