@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - FoodOrder')

@section('content')
<div class="container" style="max-width: 480px; padding: 40px 20px;">
    <div class="auth-card" style="background: white; border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Đặt Lại Mật Khẩu</h1>
            <p style="color: #64748B; font-size: 14px;">Nhập mật khẩu mới cho tài khoản của bạn</p>
        </div>

        @if($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px;">
                <ul style="margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Địa chỉ Email <span style="color: var(--primary);">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus
                           placeholder="example@gmail.com"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('email') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <div>
                <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Mật khẩu mới <span style="color: var(--primary);">*</span>
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
                    Xác nhận mật khẩu mới <span style="color: var(--primary);">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-shield-halved" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Nhập lại mật khẩu mới"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; border: none; padding: 13px; border-radius: var(--radius-sm); font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25); transition: var(--transition); margin-top: 6px;">
                Lưu Mật Khẩu Mới
            </button>
        </form>
    </div>
</div>
@endsection
