@extends('layouts.app')

@section('title', 'Thông Tin Tài Khoản - FoodOrder')

@section('content')
<div class="container" style="max-width: 900px; padding: 40px 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--dark);">Hồ Sơ Của Tôi</h1>
            <p style="color: #64748B; font-size: 14px; margin-top: 4px;">Quản lý thông tin cá nhân và bảo mật tài khoản</p>
        </div>
        <div>
            @if($user->isAdmin())
                <span style="background: #EDE9FE; color: #6D28D9; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-shield"></i> Quản Trị Viên (Admin)
                </span>
            @else
                <span style="background: #E0F2FE; color: #0369A1; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-user"></i> Thành Viên
                </span>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Card 1: Thông tin cá nhân -->
        <div style="background: white; border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
                <div style="width: 36px; height: 36px; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-address-card"></i>
                </div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--dark);">Thông Tin Giao Hàng</h2>
            </div>

            @if(session('status'))
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->updateProfile->any())
                <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px;">
                    <ul style="margin-left: 16px;">
                        @foreach($errors->updateProfile->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                @csrf
                @method('PUT')

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748B; margin-bottom: 4px;">Email (Không thể thay đổi)</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: #F1F5F9; font-size: 14px; color: #64748B; cursor: not-allowed;">
                </div>

                <div>
                    <label for="name" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">
                        Họ và tên <span style="color: var(--primary);">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition);">
                </div>

                <div>
                    <label for="phone" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="0912 345 678"
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition);">
                </div>

                <div>
                    <label for="address" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">Địa chỉ giao hàng mặc định</label>
                    <textarea id="address" name="address" rows="3"
                              placeholder="Số nhà, đường, phường, quận..."
                              style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition); resize: vertical;">{{ old('address', $user->address) }}</textarea>
                </div>

                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 11px 18px; border-radius: var(--radius-sm); font-weight: 700; font-size: 14px; cursor: pointer; transition: var(--transition); align-self: flex-start; margin-top: 4px;">
                    Lưu Thay Đổi
                </button>
            </form>
        </div>

        <!-- Card 2: Đổi mật khẩu -->
        <div style="background: white; border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
                <div style="width: 36px; height: 36px; background: #FEF3C7; color: #D97706; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--dark);">Đổi Mật Khẩu</h2>
            </div>

            @if(session('password_status'))
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('password_status') }}</span>
                </div>
            @endif

            @if($errors->updatePassword->any() || ($errors->any() && !$errors->updateProfile->any() && !session('status')))
                <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px;">
                    <ul style="margin-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.password') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">
                        Mật khẩu hiện tại <span style="color: var(--primary);">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" required
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition);">
                </div>

                <div>
                    <label for="password" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">
                        Mật khẩu mới <span style="color: var(--primary);">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                           placeholder="Tối thiểu 8 ký tự"
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition);">
                </div>

                <div>
                    <label for="password_confirmation" style="display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px;">
                        Xác nhận mật khẩu mới <span style="color: var(--primary);">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: var(--transition);">
                </div>

                <button type="submit" style="background: #0F172A; color: white; border: none; padding: 11px 18px; border-radius: var(--radius-sm); font-weight: 700; font-size: 14px; cursor: pointer; transition: var(--transition); align-self: flex-start; margin-top: 4px;">
                    Cập Nhật Mật Khẩu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
