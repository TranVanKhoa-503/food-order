@extends('layouts.app')

@section('title', 'Quên Mật Khẩu - FoodOrder')

@section('content')
<div class="container" style="max-width: 480px; padding: 40px 20px;">
    <div class="auth-card" style="background: white; border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Quên Mật Khẩu?</h1>
            <p style="color: #64748B; font-size: 14px;">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu</p>
        </div>

        @if(session('status'))
            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px;">
                <ul style="margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf

            <div>
                <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 6px;">
                    Địa chỉ Email <span style="color: var(--primary);">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="example@gmail.com"
                           style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius-sm); border: 1px solid @error('email') #EF4444 @else var(--border-color) @enderror; font-size: 14px; font-family: inherit; outline: none; transition: var(--transition);">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; border: none; padding: 13px; border-radius: var(--radius-sm); font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25); transition: var(--transition); margin-top: 6px;">
                Gửi Liên Kết Đặt Lại Mật Khẩu
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 14px; color: #64748B;">
            Quay lại 
            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Đăng nhập</a>
        </div>
    </div>
</div>
@endsection
