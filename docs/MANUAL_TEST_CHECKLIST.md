# Danh Mục Kiểm Thử Thủ Công & Hồi Quy (Manual Test Checklist)
# Food Order System — Single Store Model

Tài liệu này tổng hợp toàn bộ các ca kiểm thử (Test Cases) đã được thực hiện và kiểm chứng trong đợt kiểm thử chất lượng toàn diện của dự án.

---

## 1. Authentication & Security (Xác thực & Bảo mật)

### Đăng ký tài khoản (Register)
- [x] Đăng ký tài khoản thành công với thông tin hợp lệ
- [x] Từ chối đăng ký khi email đã tồn tại trong hệ thống (Duplicate email)
- [x] Từ chối đăng ký khi email sai định dạng (thiếu @, thiếu domain)
- [x] Từ chối đăng ký khi mật khẩu dưới 8 ký tự
- [x] Từ chối đăng ký khi mật khẩu xác nhận không khớp
- [x] Từ chối đăng ký khi các trường bắt buộc để trống (họ tên, email, mật khẩu)
- [x] Kiểm tra đăng ký thành công với họ tên có dấu Tiếng Việt và ký tự Unicode
- [x] Ngăn chặn leo thang quyền hạn: không thể gán `role = admin` hoặc `is_active = false` qua form đăng ký

### Đăng nhập (Login)
- [x] Đăng nhập thành công với email và mật khẩu chính xác
- [x] Từ chối đăng nhập khi nhập sai mật khẩu
- [x] Từ chối đăng nhập khi email không tồn tại trong hệ thống
- [x] Chặn đăng nhập đối với tài khoản đã bị vô hiệu hóa (`is_active = false`) và trả về thông báo lỗi thích hợp
- [x] Tự động làm mới Session ID (`session()->regenerate()`) ngay sau khi đăng nhập thành công
- [x] Kiểm tra tính năng ghi nhớ đăng nhập (Remember me)

### Đăng xuất (Logout)
- [x] Đăng xuất thành công qua phương thức POST kèm CSRF token
- [x] Hủy bỏ session (`session()->invalidate()`) và sinh mới CSRF token sau khi đăng xuất
- [x] Ngăn chặn truy cập trở lại các route được bảo vệ sau khi đã đăng xuất

### Quên & Đặt lại mật khẩu (Password Reset)
- [x] Gửi yêu cầu đặt lại mật khẩu với email tồn tại trong hệ thống
- [x] Gửi yêu cầu với email không tồn tại nhưng vẫn hiển thị thông báo chung an toàn (Chống lộ email)
- [x] Đặt lại mật khẩu thành công khi cung cấp token hợp lệ và mật khẩu mới từ 8 ký tự
- [x] Từ chối đặt lại mật khẩu khi token không hợp lệ hoặc đã qua sử dụng
- [x] Từ chối đặt lại mật khẩu khi mật khẩu xác nhận không trùng khớp
- [x] Đăng nhập thành công bằng mật khẩu mới và từ chối mật khẩu cũ sau khi đã đổi

---

## 2. Phân Quyền & Kiểm Soát Truy Cập (Authorization & Access Control)

### Quyền Khách Vãng Lai (Guest)
- [x] Khách chưa đăng nhập có thể xem trang chủ thực đơn và trang chi tiết món ăn
- [x] Khách chưa đăng nhập bị chuyển hướng (hoặc nhận lỗi 401 JSON) khi truy cập `/profile`
- [x] Khách chưa đăng nhập bị chuyển hướng (hoặc nhận lỗi 401 JSON) khi truy cập `/orders`
- [x] Khách chưa đăng nhập bị chặn tạo đơn hàng (`POST /api/v1/orders`) và nhận mã lỗi 401
- [x] Khách chưa đăng nhập bị chặn tuyệt đối khi truy cập bất kỳ route nào thuộc `/admin/*`

### Quyền Người Dùng Thông Thường (Customer / Role: User)
- [x] Người dùng chỉ có thể xem danh sách đơn hàng của chính mình
- [x] Người dùng chỉ có thể xem chi tiết đơn hàng thuộc quyền sở hữu của mình
- [x] Người dùng bị trả về lỗi 404 khi cố tình xem đơn hàng của người dùng khác (IDOR Prevention)
- [x] Người dùng chỉ được phép hủy đơn hàng của chính mình khi đơn còn ở trạng thái `pending`
- [x] Người dùng bị chặn khi cố hủy đơn hàng của người khác
- [x] Người dùng bị từ chối truy cập (HTTP 403) vào tất cả các màn hình và API của Admin (`/admin/*`, `/api/v1/admin/*`)

### Quyền Quản Trị Viên (Role: Admin)
- [x] Admin có quyền truy cập toàn bộ các route quản trị `/admin/*`
- [x] Admin có quyền xem chi tiết đơn hàng của bất kỳ khách hàng nào
- [x] Admin có thể cập nhật trạng thái đơn hàng theo đúng quy trình máy trạng thái
- [x] Admin bị chặn không được tự vô hiệu hóa / khóa tài khoản của chính mình

---

## 3. Thực Đơn & Tìm Kiếm Món Ăn (Catalog & Search)

- [x] Hiển thị danh sách món ăn kèm hình ảnh, giá niêm yết và danh mục
- [x] Chỉ hiển thị các món ăn đang mở bán (`is_available = true`) cho khách hàng
- [x] Thanh danh mục (Pills bar) hiển thị đúng số lượng món ăn khả dụng của từng nhóm
- [x] Lọc danh sách món ăn chính xác theo từng danh mục
- [x] Tìm kiếm món ăn theo từ khóa trong tên hoặc mô tả món
- [x] Tìm kiếm món ăn với ký tự Tiếng Việt có dấu
- [x] Tìm kiếm với từ khóa không tồn tại hiển thị đúng giao diện trạng thái trống (Empty state)
- [x] Lọc món ăn theo khoảng giá: giá tối thiểu (`min_price`), giá tối đa (`max_price`)
- [x] Kiểm tra khoảng giá ngược (`min_price > max_price`) trả về danh sách rỗng an toàn
- [x] Chặn nhập giá trị âm cho bộ lọc khoảng giá thông qua validation (`min:0`)
- [x] Xem chi tiết món ăn khả dụng (`/foods/{id}`)
- [x] Truy cập món ăn tạm ngưng bán hoặc không tồn tại trả về lỗi HTTP 404 Not Found

---

## 4. Trợ Lý Gợi Ý Món Ăn (Food Recommendation Assistant)

- [x] Mở và đóng bảng trợ lý gợi ý món ăn từ trang chủ
- [x] Tự động tải danh sách danh mục vào bộ chọn của trợ lý
- [x] Gợi ý món ăn theo ngân sách tối đa
- [x] Gợi ý món ăn theo tiêu chí ăn chay (`vegetarian`)
- [x] Gợi ý món ăn theo tiêu chí không cay (`not_spicy`)
- [x] Kết hợp nhiều điều kiện lọc đồng thời (Danh mục + Ngân sách + Khẩu vị)
- [x] Hiển thị thông điệp hướng dẫn khi không tìm thấy món ăn nào thỏa mãn tiêu chí
- [x] Đảm bảo kết quả gợi ý không bao giờ chứa món ăn đã tạm ngưng bán (`is_available = false`)
- [x] Đảm bảo giới hạn số lượng món gợi ý tối đa không vượt quá tham số quy định

---

## 5. Giỏ Hàng (Shopping Cart)

- [x] Mở và đóng Drawer giỏ hàng từ thanh điều hướng
- [x] Thêm món ăn mới vào giỏ hàng từ trang chủ và hiển thị thông báo Toast
- [x] Thêm món ăn đã có trong giỏ sẽ tự động tăng số lượng món
- [x] Tăng số lượng món ăn trong giỏ bằng nút `+`
- [x] Giảm số lượng món ăn trong giỏ bằng nút `-`
- [x] Giảm số lượng về 0 tự động xóa món ăn đó khỏi giỏ
- [x] Xóa món ăn khỏi giỏ hàng bằng nút biểu tượng thùng rác
- [x] Giỏ hàng rỗng hiển thị thông báo và biểu tượng giỏ hàng trống, ẩn chân trang thanh toán
- [x] Lưu trữ trạng thái giỏ hàng vào `localStorage` bền vững qua các lần tải lại trang
- [x] Đồng bộ số lượng hiển thị trên biểu tượng huy hiệu giỏ hàng ở thanh điều hướng
- [x] Chặn tăng số lượng món vượt quá 99 phần/món ở cả giao diện và phía server

---

## 6. Đặt Hàng & Thanh Toán (Checkout Engine)

- [x] Đặt hàng thành công với thông tin giao hàng hợp lệ (Họ tên, SĐT, Địa chỉ)
- [x] Từ chối đặt hàng khi giỏ hàng trống (Mã lỗi 422)
- [x] Từ chối đặt hàng khi thiếu các trường bắt buộc người nhận
- [x] Server tự động truy vấn lại giá từ Database trong DB Transaction với `lockForUpdate`
- [x] Ngăn chặn gian lận giá: client cố tình gửi giá tiền bị sửa đổi sẽ bị bỏ qua và tính lại theo DB
- [x] Tự động gộp các phần tử trùng `food_id` và giới hạn tối đa 99 phần/món
- [x] Kiểm tra và hủy giao dịch (Rollback) nếu có bất kỳ món ăn nào trong giỏ đã hết hàng hoặc ngưng bán (Mã lỗi 409)
- [x] Từ chối đặt hàng nếu có mã món ăn không tồn tại trong hệ thống (Mã lỗi 422)
- [x] Kiểm tra trạng thái đóng cửa của cửa hàng: từ chối đặt món khi `is_open = false` (Mã lỗi 409)
- [x] Kiểm tra khung giờ hoạt động: từ chối đặt món ngoài giờ mở cửa (Mã lỗi 409)
- [x] Kiểm tra khung giờ hoạt động xuyên đêm (Overnight schedule ví dụ: 20:00 đến 04:00 sáng)
- [x] Kiểm tra giá trị đơn hàng tối thiểu: từ chối đặt đơn khi tổng tiền món nhỏ hơn `min_order_value` (Mã lỗi 422)
- [x] Sinh mã đơn hàng duy nhất theo định dạng `FO-YYYYMMDD-XXXXXX` không trùng lặp
- [x] Lưu vết đầy đủ dòng hàng trong bảng `order_items` với đơn giá và tên món tại thời điểm đặt
- [x] Xóa giỏ hàng trong `localStorage` ngay sau khi đặt hàng thành công

---

## 7. Khuyến Mãi & Voucher (Voucher Engine)

- [x] Áp dụng thành công mã voucher giảm theo phần trăm (Ví dụ: Giảm 20%)
- [x] Áp dụng thành công mã voucher giảm theo số tiền cố định (Ví dụ: Giảm 30.000 ₫)
- [x] Giới hạn mức giảm tối đa của voucher phần trăm (`max_discount_amount`)
- [x] Đảm bảo số tiền giảm giá không bao giờ vượt quá tổng tiền món ăn (Không âm tiền hàng)
- [x] Từ chối áp dụng voucher khi chưa đạt giá trị đơn hàng tối thiểu (`min_order_value`)
- [x] Từ chối áp dụng voucher không tồn tại trong hệ thống (Mã lỗi 422)
- [x] Từ chối áp dụng voucher đã bị vô hiệu hóa (`is_active = false`)
- [x] Từ chối áp dụng voucher đã hết hạn sử dụng (`ends_at < now()`)
- [x] Từ chối áp dụng voucher chưa đến ngày bắt đầu hiệu lực (`starts_at > now()`)
- [x] Từ chối áp dụng voucher đã hết số lượt sử dụng tối đa (`used_count >= usage_limit`)
- [x] Hỗ trợ nhập mã voucher không phân biệt chữ hoa, chữ thường
- [x] Tự động tăng số lượt đã dùng (`used_count`) của voucher ngay khi đặt đơn thành công
- [x] Tự động hoàn trả số lượt đã dùng của voucher khi đơn hàng bị hủy bỏ

---

## 8. Khu Vực Giao Hàng & Phí Vận Chuyển (Delivery Zones)

- [x] Tải danh sách các khu vực giao hàng đang hoạt động vào form thanh toán
- [x] Bắt buộc người dùng chọn khu vực giao hàng khi hệ thống có khu vực đang hoạt động
- [x] Tự động áp mức phí ship tương ứng của khu vực được chọn vào tổng đơn hàng
- [x] Lưu snapshot tên khu vực giao hàng (`delivery_zone_name`) vào bản ghi đơn hàng
- [x] Từ chối đơn hàng khi gửi mã khu vực giao hàng không còn hoạt động hoặc không tồn tại
- [x] Sử dụng phí giao hàng mặc định của cửa hàng khi không có khu vực giao hàng riêng biệt
- [x] Khóa ngoại `delivery_zone_id` tự động đặt thành `null` khi khu vực giao hàng bị xóa mà không làm mất thông tin snapshot trên đơn hàng cũ

---

## 9. Quản Lý Tiến Trình Đơn Hàng (Order Lifecycle & State Machine)

- [x] Khách hàng xem danh sách lịch sử đơn hàng của mình kèm bộ lọc trạng thái
- [x] Khách hàng xem chi tiết đơn hàng kèm timeline tiến độ trực quan
- [x] Khách hàng tự hủy đơn hàng khi trạng thái đơn đang là `pending`
- [x] Từ chối khách hàng tự hủy đơn hàng khi đơn đã ở trạng thái `confirmed`, `preparing`, `delivering` hoặc `completed`
- [x] Admin chuyển trạng thái tuần tự hợp lệ: `pending` $\rightarrow$ `confirmed` $\rightarrow$ `preparing` $\rightarrow$ `delivering` $\rightarrow$ `completed`
- [x] Admin hủy đơn hàng từ trạng thái `pending`
- [x] Admin hủy đơn hàng từ trạng thái `confirmed` (bắt buộc nhập lý do hủy)
- [x] Chặn admin nhảy cóc trạng thái (Ví dụ: `pending` nhảy thẳng lên `delivering` hoặc `completed`)
- [x] Chặn admin quay ngược trạng thái (Ví dụ: `delivering` quay về `confirmed`)
- [x] Chặn admin mở lại hoặc thay đổi trạng thái của đơn hàng đã kết thúc (`completed` hoặc `cancelled`)
- [x] Tự động cập nhật trạng thái thanh toán sang `paid` khi đơn hàng COD chuyển sang `completed`
- [x] Ghi nhận đầy đủ nhật ký chuyển trạng thái vào bảng `order_status_histories`

---

## 10. Thông Báo Trong Ứng Dụng (In-App Notifications)

- [x] Tự động tạo bản ghi thông báo khi đơn hàng được cập nhật trạng thái
- [x] Hiển thị số lượng thông báo chưa đọc trên thanh điều hướng
- [x] Tải danh sách thông báo mới nhất qua API kèm thông tin đơn hàng liên kết
- [x] Đánh dấu một thông báo cụ thể là đã đọc
- [x] Đánh dấu tất cả thông báo là đã đọc
- [x] Ngăn chặn người dùng A đánh dấu hoặc xem thông báo thuộc quyền sở hữu của người dùng B

---

## 11. Bảng Quản Trị & Báo Cáo (Admin Panel)

### Dashboard Thống Kê
- [x] Thống kê tổng doanh thu chỉ tính từ các đơn hàng giao thành công (`completed`)
- [x] Thống kê tổng số lượng đơn hàng, số đơn chờ xử lý, số lượng món ăn và số khách hàng
- [x] Lọc dữ liệu thống kê theo khoảng thời gian (`from` đến `to`)
- [x] Thống kê danh sách Top món ăn bán chạy nhất dựa trên các đơn đã hoàn tất
- [x] Biểu đồ doanh thu theo từng ngày trong khoảng thời gian được chọn

### Quản Lý Thực Đơn & Danh Mục
- [x] Thêm món ăn mới kèm tải ảnh trực tiếp lên server
- [x] Kiểm tra tính hợp lệ của tệp ảnh tải lên (định dạng JPG, PNG, WEBP; dung lượng $\le$ 2MB)
- [x] Cập nhật thông tin món ăn, giá bán và danh mục trực thuộc
- [x] Bật/tắt nhanh trạng thái còn hàng / hết món (`is_available`)
- [x] Thêm danh mục món ăn mới với slug tự động sinh không trùng lặp
- [x] Ngăn chặn xóa danh mục khi đang có món ăn liên kết và trả về mã lỗi 409

### Quản Lý Khách Hàng
- [x] Xem danh sách khách hàng có phân trang
- [x] Tìm kiếm khách hàng theo tên, email hoặc số điện thoại
- [x] Khóa tài khoản khách hàng vi phạm
- [x] Mở khóa tài khoản khách hàng
- [x] Ngăn chặn quản trị viên tự khóa tài khoản của chính mình

### Quản Lý Voucher
- [x] Thêm mới mã voucher giảm theo phần trăm hoặc số tiền cố định
- [x] Kiểm tra phần trăm giảm giá không được vượt quá 100%
- [x] Cập nhật thông tin, thời hạn và số lượt sử dụng của voucher
- [x] Ngăn chặn xóa voucher đã từng phát sinh đơn hàng trong hệ thống (Mã lỗi 409)

### Quản Lý Khu Vực & Cài Đặt
- [x] Thêm mới và cập nhật khu vực giao hàng kèm định mức phí ship
- [x] Cập nhật cài đặt cửa hàng: Trạng thái mở cửa, giờ nhận đơn, mức đơn tối thiểu và phí ship cơ bản

### Xuất Báo Cáo Đơn Hàng (CSV Export)
- [x] Xuất danh sách đơn hàng đã lọc ra file CSV định dạng UTF-8 kèm ký tự BOM
- [x] Xuất đúng các cột thông tin: Mã đơn, Khách hàng, SĐT, Địa chỉ, Món ăn, Tạm tính, Giảm giá, Phí ship, Tổng tiền, Trạng thái
- [x] Dữ liệu tiếng Việt hiển thị chính xác không bị lỗi font khi mở bằng Microsoft Excel
- [x] Triệt tiêu hoàn toàn lỗ hổng CSV Formula Injection (CWE-1236): tự động escape bằng dấu nháy đơn `'` với các ký tự nguy hiểm (`=`, `+`, `-`, `@`, `\t`, `\r`) ngay cả khi có khoảng trắng phía trước

---

## 12. Hồi Quy & Khắc Phục Lỗi (Remediation & Regression Verification)

### BUG-01: CSV Formula Injection Mitigation
- [x] Kiểm tra ký tự `=`, `+`, `-`, `@` được trung hòa an toàn khi xuất file CSV
- [x] Kiểm tra ký tự công thức có khoảng trắng/tab phía trước (ví dụ `   =SUM(...)`) được trung hòa an toàn
- [x] Dữ liệu tiếng Việt bình thường không bị ảnh hưởng hay gán thừa tiền tố nháy đơn
- [x] Chỉ Admin mới được phép xuất file CSV; Guest và User thường bị chặn (Redirect/403)

### BUG-02: Chống lộ mã đơn hàng (Order ID Enumeration)
- [x] User B yêu cầu hủy đơn hàng của User A (`PATCH /api/v1/orders/{user_a_order}/cancel`) nhận mã lỗi **HTTP 404 Not Found**
- [x] Yêu cầu hủy đơn hàng với ID không tồn tại trong hệ thống nhận mã lỗi **HTTP 404 Not Found**
- [x] User tự hủy đơn hàng hợp lệ ở trạng thái `pending` của chính mình hoạt động bình thường

### BUG-03: Dọn dẹp tệp tin ảnh cũ khi cập nhật món ăn (Storage Leak)
- [x] Tải lên ảnh mới thay thế cho món ăn sẽ lưu ảnh mới và tự động xóa ảnh cũ trên disk `public`
- [x] Xóa ảnh chỉ thực hiện sau khi cập nhật cơ sở dữ liệu thành công
- [x] Cập nhật thông tin món ăn mà không thay đổi ảnh sẽ giữ nguyên tệp ảnh hiện có
- [x] Khi dữ liệu validate không hợp lệ (lỗi 422), tệp ảnh hiện có không bị xóa nhầm
- [x] URL ảnh ngoài hoặc ảnh mặc định không bị xóa nhầm

### BUG-04: Cấu hình Storage Link
- [x] Lệnh `php artisan storage:link` được tài liệu hóa rõ ràng là bước bắt buộc trong `README.md` và `docs/DEPLOYMENT.md`
- [x] Đã tạo symbolic link từ `public/storage` tới `storage/app/public`
- [x] Ảnh món ăn tải lên hiển thị bình thường, không bị lỗi HTTP 404

### Xử lý Giao hàng thất bại (Failed Delivery Transition)
- [x] Admin có thể chuyển trạng thái đơn hàng từ `delivering` sang `cancelled` (Giao hàng thất bại) kèm lý do bắt buộc
- [x] Từ chối chuyển sang `cancelled` nếu để trống lý do hủy
- [x] Khách hàng nhận được thông báo trạng thái kèm lý do hủy giao hàng thất bại
- [x] Trạng thái `preparing` không cho phép hủy trực tiếp (bảo toàn quy tắc chế biến)

### Rate Limiting (Chống Brute-Force & Spam Request)
- [x] Đăng nhập: Giới hạn 10 lần/phút theo Email + IP (`auth-login`), vượt quá trả về HTTP 429
- [x] Đăng ký: Giới hạn 5 lần/phút theo IP (`auth-register`), vượt quá trả về HTTP 429
- [x] Quên mật khẩu: Giới hạn 5 lần/phút theo Email + IP (`auth-forgot-password`), vượt quá trả về HTTP 429
- [x] Đặt hàng (Checkout): Giới hạn 10 lần/phút theo User/IP (`order-checkout`), vượt quá trả về HTTP 429

### Xác thực số điện thoại Việt Nam
- [x] Chấp nhận định dạng chuẩn 10 chữ số đầu `0` (ví dụ `0912345678`)
- [x] Chấp nhận định dạng quốc tế `+84` (ví dụ `+84912345678`)
- [x] Từ chối số điện thoại chứa chữ cái, ký tự đặc biệt, không đủ hoặc quá độ dài quy định tại Checkout, Cập nhật hồ sơ và Đăng ký

### Tối ưu hóa hiệu năng Dashboard
- [x] Thay thế truy vấn tính doanh thu lặp 7–31 lần bằng 1 truy vấn tổng hợp `GROUP BY DATE(created_at)`
- [x] Thay thế 6 truy vấn đếm trạng thái bằng 1 truy vấn tổng hợp `GROUP BY status`
- [x] Giữ nguyên chuỗi ngày liên tục với doanh thu 0 cho các ngày không phát sinh đơn hàng
