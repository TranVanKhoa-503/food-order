# Thiết kế route và JSON API

## 1. Trạng thái

Đây là contract **dự kiến**, chưa phải danh sách route đã implement. Source hiện chỉ có:

| Method | URL | Kết quả |
| --- | --- | --- |
| GET | `/` | Render trang `home.blade.php` bằng `FoodController@index` |

Các endpoint dưới đây sẽ được triển khai theo sprint. Vì frontend Blade và backend cùng origin, API dùng session cookie + CSRF, không dùng token.

## 2. Quy ước

### Base URL

`/api/v1`

Trong phiên bản đầu, route group này được khai báo trong `routes/web.php` để dùng sẵn middleware `web` và session authentication. Project hiện chưa có `routes/api.php` hoặc token-auth package.

### Middleware

| Middleware | Ý nghĩa |
| --- | --- |
| `web` | Cookie, session, CSRF và share validation errors |
| `guest` | Chỉ người chưa đăng nhập |
| `auth` | Phải đăng nhập |
| `active` | Middleware dự kiến: user phải có `is_active=true` |
| `admin` | Middleware dự kiến: user phải có `role=admin` |
| `throttle` | Giới hạn login/reset để tránh spam |

Mọi POST/PUT/PATCH/DELETE dùng session phải gửi CSRF token. Layout hiện đã có `<meta name="csrf-token">`; JavaScript gửi giá trị này qua header `X-CSRF-TOKEN`.

### Header

```http
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: <token từ meta tag>
```

### Success response

Resource đơn:

```json
{
  "data": {}
}
```

Danh sách phân trang:

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 0
  }
}
```

Delete/logout thành công có thể trả HTTP 204 không có body.

### Error response

```json
{
  "message": "Dữ liệu không hợp lệ.",
  "errors": {
    "email": ["Email đã được sử dụng."]
  }
}
```

| HTTP | Ý nghĩa |
| ---: | --- |
| 400 | Request JSON sai định dạng |
| 401 | Chưa đăng nhập/thông tin đăng nhập sai |
| 403 | Đã đăng nhập nhưng không có quyền hoặc bị khóa |
| 404 | Resource không tồn tại/không thuộc user |
| 409 | Xung đột nghiệp vụ hoặc trạng thái |
| 419 | CSRF token thiếu/hết hạn |
| 422 | Validation fail |
| 429 | Quá giới hạn request |
| 500 | Lỗi server không dự kiến; không trả stack trace ở production |

### Resource rút gọn

`UserResource`:

```json
{
  "id": 1,
  "name": "Nguyen Van A",
  "email": "a@example.com",
  "phone": "0900000000",
  "address": "Quận 1, TP.HCM",
  "role": "user",
  "is_active": true
}
```

`CategoryResource`:

```json
{
  "id": 1,
  "name": "Món chính",
  "slug": "mon-chinh",
  "description": null,
  "icon": "fa-utensils",
  "foods_count": 8
}
```

`FoodResource`:

```json
{
  "id": 1,
  "category": {
    "id": 1,
    "name": "Món chính",
    "slug": "mon-chinh"
  },
  "name": "Phở bò",
  "description": "Mô tả món",
  "price": 65000,
  "image": "https://example.com/food.jpg",
  "is_available": true
}
```

`OrderResource`:

```json
{
  "id": 10,
  "order_code": "FO-20260828-000010",
  "status": "pending",
  "payment_method": "cod",
  "payment_status": "unpaid",
  "customer_name": "Nguyen Van A",
  "customer_phone": "0900000000",
  "delivery_address": "Quận 1, TP.HCM",
  "note": null,
  "subtotal": 130000,
  "shipping_fee": 0,
  "total_price": 130000,
  "items": [
    {
      "id": 1,
      "food_id": 2,
      "food_name": "Phở bò",
      "unit_price": 65000,
      "quantity": 2,
      "line_total": 130000,
      "note": null
    }
  ],
  "created_at": "2026-08-28T10:00:00+07:00"
}
```

## 3. Authentication

### POST /api/v1/auth/register

- Quyền: guest.
- Middleware: `web`, `guest`, throttle.
- Body:

```json
{
  "name": "Nguyen Van A",
  "email": "a@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

- Validation: name bắt buộc/max 255; email hợp lệ/unique; password tối thiểu 8 ký tự và confirmed.
- Response: HTTP 201, `data` là UserResource; backend đăng nhập session ngay sau register.
- Lỗi: 419, 422 email/password, 429.

### POST /api/v1/auth/login

- Quyền: guest.
- Middleware: `web`, `guest`, throttle.
- Body: `email`, `password`, `remember` boolean optional.
- Response: HTTP 200, UserResource; session ID phải được regenerate.
- Lỗi: 401 sai credentials, 403 account inactive, 419, 422, 429.

### POST /api/v1/auth/logout

- Quyền: user/admin đã đăng nhập.
- Middleware: `web`, `auth`.
- Body: không có.
- Response: HTTP 204; backend logout, invalidate session và regenerate CSRF token.
- Lỗi: 401, 419.

### GET /api/v1/auth/me

- Quyền: user/admin đã đăng nhập.
- Middleware: `web`, `auth`, `active`.
- Params: không có.
- Response: HTTP 200, UserResource.
- Lỗi: 401 hoặc 403 account inactive.

### POST /api/v1/auth/forgot-password

- Quyền: guest.
- Middleware: `web`, `guest`, throttle.
- Body: `email`.
- Response: HTTP 200 với message chung dù email có tồn tại hay không, tránh dò tài khoản.
- Lỗi: 419, 422 email sai định dạng, 429.
- Ghi chú: chỉ implement khi cấu hình password broker và mail flow trong Sprint 1; local mailer có thể ghi link vào log.

### POST /api/v1/auth/reset-password

- Quyền: guest.
- Middleware: `web`, `guest`, throttle.
- Body: `token`, `email`, `password`, `password_confirmation`.
- Response: HTTP 200 message reset thành công.
- Lỗi: 419, 422 token/email/password không hợp lệ, 429.

## 4. User

### GET /api/v1/user/profile

- Quyền: user/admin xem profile của mình.
- Middleware: `web`, `auth`, `active`.
- Params/body: không có.
- Response: HTTP 200, UserResource.
- Lỗi: 401, 403.

### PUT /api/v1/user/profile

- Quyền: user/admin sửa profile của mình.
- Middleware: `web`, `auth`, `active`.
- Body: `name` required, `phone` nullable/max 20, `address` nullable.
- Email và role không được nhận ở endpoint này.
- Response: HTTP 200, UserResource mới.
- Lỗi: 401, 403, 419, 422.

### PUT /api/v1/user/password

- Quyền: user/admin đổi password của mình.
- Middleware: `web`, `auth`, `active`.
- Body: `current_password`, `password`, `password_confirmation`.
- Response: HTTP 204; có thể invalidate các session khác nếu implementation hỗ trợ.
- Lỗi: 401, 403, 419, 422 current password/password rule.

## 5. Category

### GET /api/v1/categories

- Quyền: public.
- Middleware: `web`.
- Query: `with_foods_count=1` optional.
- Response: HTTP 200, danh sách CategoryResource có category được phép hiển thị.
- Lỗi: 422 query không hợp lệ.

### GET /api/v1/categories/{category:slug}

- Quyền: public.
- Middleware: `web`.
- Params: category slug.
- Response: HTTP 200, CategoryResource.
- Lỗi: 404.

### GET /api/v1/admin/categories

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Query: `search`, `page`, `per_page`.
- Response: HTTP 200, danh sách phân trang CategoryResource.
- Lỗi: 401, 403, 422.

### POST /api/v1/admin/categories

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body: `name` required, `slug` optional, `description` optional, `icon` optional.
- Backend sinh slug từ name nếu không gửi.
- Response: HTTP 201, CategoryResource.
- Lỗi: 401, 403, 419, 422 duplicate slug.

### PUT /api/v1/admin/categories/{category}

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body: `name`, `slug`, `description`, `icon`; name/slug required cho PUT.
- Response: HTTP 200, CategoryResource.
- Lỗi: 401, 403, 404, 419, 422 duplicate slug.

### DELETE /api/v1/admin/categories/{category}

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Params: category id; body không có.
- Response: HTTP 204.
- Lỗi: 401, 403, 404, 419; 409 nếu category còn food.

## 6. Food

### GET /api/v1/foods

- Quyền: public.
- Middleware: `web`.
- Query:
  - `category`: category id hoặc slug.
  - `search`: tối đa 100 ký tự.
  - `min_price`, `max_price`: số nguyên không âm.
  - `page`: mặc định 1.
  - `per_page`: mặc định 15, tối đa 50.
- Chỉ trả food có `is_available=true`.
- Response: HTTP 200, danh sách phân trang FoodResource.
- Lỗi: 422 category/price/pagination không hợp lệ.

### GET /api/v1/foods/{food}

- Quyền: public.
- Middleware: `web`.
- Params: food id; body không có.
- Response: HTTP 200, FoodResource nếu food còn bán.
- Lỗi: 404 nếu không tồn tại hoặc đã unavailable.

### GET /api/v1/admin/foods

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Query: giống public, thêm `is_available=0|1`; trả cả món unavailable.
- Response: HTTP 200, danh sách phân trang FoodResource.
- Lỗi: 401, 403, 422.

### POST /api/v1/admin/foods

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body:

```json
{
  "category_id": 1,
  "name": "Phở bò",
  "description": "Mô tả",
  "price": 65000,
  "image": "https://example.com/pho.jpg",
  "is_available": true
}
```

- Validation: category tồn tại; name required; price integer >= 0; image nullable URL/max 2048; is_available boolean.
- Response: HTTP 201, FoodResource.
- Lỗi: 401, 403, 404 category, 419, 422.

### PUT /api/v1/admin/foods/{food}

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body: cùng field create; required theo semantics PUT.
- Response: HTTP 200, FoodResource.
- Lỗi: 401, 403, 404 food/category, 419, 422.

### PATCH /api/v1/admin/foods/{food}/availability

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body: `is_available` boolean required.
- Response: HTTP 200, FoodResource.
- Lỗi: 401, 403, 404, 419, 422.

## 7. Cart

Không tạo cart API trong MVP.

- Cart hiện lưu ở `localStorage`.
- Không có `carts` hoặc `cart_items` table.
- Frontend gửi items khi tạo order.
- Backend không tin tên, giá hoặc tổng tiền trong cart; chỉ nhận `food_id`, `quantity`, `note`.

Nếu sau này có yêu cầu đồng bộ cart nhiều thiết bị mới thiết kế backend cart riêng.

## 8. Order của User

### POST /api/v1/orders

- Quyền: user/admin active.
- Middleware: `web`, `auth`, `active`.
- Body:

```json
{
  "customer_name": "Nguyen Van A",
  "customer_phone": "0900000000",
  "delivery_address": "Quận 1, TP.HCM",
  "note": "Gọi trước khi giao",
  "items": [
    {
      "food_id": 1,
      "quantity": 2,
      "note": "Không hành"
    }
  ]
}
```

- Validation:
  - Thông tin người nhận bắt buộc.
  - `items` có ít nhất một dòng.
  - `food_id` không lặp sau khi normalize hoặc được backend gộp.
  - `quantity` integer từ 1 đến 99.
  - Mỗi food phải tồn tại và available.
- Backend đọc lại giá, tính totals và tạo order/items trong một transaction.
- Response: HTTP 201, OrderResource trạng thái `pending`.
- Lỗi: 401, 403 account inactive, 419, 422; 409 nếu món vừa hết hàng/thay đổi không thể đặt.

### GET /api/v1/orders

- Quyền: user/admin active; chỉ trả order của chính account.
- Middleware: `web`, `auth`, `active`.
- Query: `status`, `page`, `per_page`.
- Response: HTTP 200, danh sách phân trang OrderResource rút gọn.
- Lỗi: 401, 403, 422 status/pagination.

### GET /api/v1/orders/{order}

- Quyền: chủ sở hữu order; admin cũng có thể xem nhưng admin UI nên dùng admin endpoint.
- Middleware: `web`, `auth`, `active`; OrderPolicy.
- Params: order id; body không có.
- Response: HTTP 200, OrderResource có items.
- Lỗi: 401, 403 account inactive, 404 không tồn tại/không thuộc user.

### PATCH /api/v1/orders/{order}/cancel

- Quyền: chủ sở hữu order.
- Middleware: `web`, `auth`, `active`; OrderPolicy.
- Body: `reason` nullable, tối đa 1000 ký tự.
- Chỉ cho phép khi current status là `pending`.
- Response: HTTP 200, OrderResource status `cancelled`.
- Lỗi: 401, 403, 404, 419, 422 reason; 409 nếu order không còn pending.

## 9. Admin

### GET /api/v1/admin/dashboard

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Query: `from`, `to` optional; khoảng ngày hợp lệ.
- Response: HTTP 200 gồm tổng user, food, order theo status và doanh thu từ order completed.
- Lỗi: 401, 403, 422 date range.

### GET /api/v1/admin/orders

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Query: `search` theo order_code/name/phone, `status`, `from`, `to`, `page`, `per_page`.
- Response: HTTP 200, danh sách order phân trang.
- Lỗi: 401, 403, 422.

### GET /api/v1/admin/orders/{order}

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Params: order id; body không có.
- Response: HTTP 200, OrderResource đầy đủ và UserResource rút gọn.
- Lỗi: 401, 403, 404.

### PATCH /api/v1/admin/orders/{order}/status

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body:

```json
{
  "status": "confirmed",
  "reason": null
}
```

- `reason` bắt buộc khi hủy từ confirmed/preparing/delivering.
- Backend enforce [ORDER_FLOW.md](ORDER_FLOW.md), khóa order trong transaction và chỉ cho đi một bước.
- Response: HTTP 200, OrderResource sau transition.
- Lỗi: 401, 403, 404, 419, 422 status/reason; 409 transition sai hoặc order đã đổi bởi request khác.

### GET /api/v1/admin/users

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Query: `search` theo name/email/phone, `role=user|admin`, `is_active=0|1`, `page`, `per_page`.
- Response: HTTP 200, danh sách UserResource phân trang.
- Lỗi: 401, 403, 422.

### GET /api/v1/admin/users/{user}

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Params: user id; body không có.
- Response: HTTP 200, UserResource và thống kê số order cơ bản.
- Lỗi: 401, 403, 404.

### PATCH /api/v1/admin/users/{user}/status

- Quyền: admin.
- Middleware: `web`, `auth`, `active`, `admin`.
- Body: `is_active` boolean required.
- Response: HTTP 200, UserResource.
- Lỗi: 401, 403, 404, 419, 422; 409 nếu admin cố tự khóa chính mình.
- Endpoint này không thay đổi role. Việc tạo admin thực hiện bằng seeder hoặc quy trình quản trị riêng, tránh privilege escalation.

## 10. Route model binding và policy

- Category public binding dùng slug; admin route có thể dùng id để thao tác ổn định.
- Food/order/user dùng numeric id.
- User order endpoint phải scope query theo `auth()->id()` hoặc OrderPolicy trước khi trả resource.
- Admin middleware kiểm tra cả `role=admin` và `is_active=true`.
- Không truyền Model trực tiếp ra JSON; dùng Resource/transformer để tránh lộ password, remember token hoặc field nội bộ.

## 11. Thứ tự triển khai contract

1. Sprint 0: route group, JSON error convention, middleware skeleton, Request/Resource conventions.
2. Sprint 1: Authentication + User.
3. Sprint 2: Category + Food.
4. Sprint 3: POST order checkout.
5. Sprint 4: Order query/cancel/admin transition.
6. Sprint 5: Admin dashboard/users.
7. Sprint 6: nối frontend hiện có.

Không đăng ký route placeholder trả success giả. Route chỉ được thêm khi controller, validation, authorization và test tương ứng đã tồn tại.
