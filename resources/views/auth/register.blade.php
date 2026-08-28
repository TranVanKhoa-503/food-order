@extends('layouts.app')

@section('title', 'Đăng Ký Tài Khoản - FoodOrder')

@section('content')
<div class="container" style="max-width: 540px; padding: 40px 20px;">
    <div class="auth-card" style="background: white; border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Tạo Tài Khoản Mới</h1>
            <p style="color: #64748B; font-size: 14px;">Đăng ký để đặt món nhanh chóng và nhận nhiều ưu đãi</p>
        </div>

        @if($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px;">
                <div style="font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Thông tin chưa hợp lệ</span>
                </div>
                <ul style="margin-left: 20px; margin-top: 4px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label for="name" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Họ và tên <span style="color: var(--primary);">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Nguyễn Văn A"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('name') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <div>
                <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Địa chỉ Email <span style="color: var(--primary);">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="example@gmail.com"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('email') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                        Mật khẩu <span style="color: var(--primary);">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                        <input type="password" id="password" name="password" required
                               placeholder="Tối thiểu 8 ký tự"
                               style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('password') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                        Xác nhận mật khẩu <span style="color: var(--primary);">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-shield-halved" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Nhập lại mật khẩu"
                               style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                    </div>
                </div>
            </div>

            <div>
                <label for="phone" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Số điện thoại (tùy chọn)
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="0912 345 678"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('phone') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <div>
                <label for="address" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Địa chỉ giao hàng mặc định (tùy chọn)
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-location-dot" style="position: absolute; left: 16px; top: 16px; color: #94A3B8;"></i>
                    <textarea id="address" name="address" rows="2"
                              placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."
                              style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; font-family: inherit; outline: none; transition: var(--transition); resize: vertical;">{{ old('address') }}</textarea>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; border: none; padding: 13px; border-radius: var(--radius-sm); font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25); transition: var(--transition); margin-top: 6px;">
                Đăng Ký Tài Khoản
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 14px; color: #64748B;">
            Đã có tài khoản? 
            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Đăng nhập</a>
        </div>
    </div>
</div>
@endsection
