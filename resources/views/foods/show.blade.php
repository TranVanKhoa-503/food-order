@extends('layouts.app')

@section('title', $food->name.' - FoodOrder')

@section('content')
<div class="container" style="max-width:1000px;padding:40px 20px;">
    <a href="{{ route('home') }}" style="color:#64748B;text-decoration:none;font-size:13px;"><i class="fa-solid fa-arrow-left"></i> Quay lại thực đơn</a>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;background:white;border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:24px;margin-top:18px;">
        <div>
            <img src="{{ $food->image }}" alt="{{ $food->name }}" style="width:100%;height:360px;object-fit:cover;border-radius:var(--radius-lg);" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80'">
        </div>
        <div style="display:flex;flex-direction:column;justify-content:center;">
            @if($food->category)<span style="color:var(--primary);font-size:13px;font-weight:800;text-transform:uppercase;">{{ $food->category->name }}</span>@endif
            <h1 style="font-size:32px;font-weight:800;margin:10px 0;">{{ $food->name }}</h1>
            <p style="color:#64748B;line-height:1.7;margin-bottom:20px;">{{ $food->description ?: 'Món ăn thơm ngon được chuẩn bị từ nguyên liệu chất lượng.' }}</p>
            <div style="font-size:28px;font-weight:800;color:var(--primary);margin-bottom:18px;">{{ number_format($food->price, 0, ',', '.') }} ₫</div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;color:#047857;font-size:14px;font-weight:700;"><i class="fa-solid fa-circle-check"></i> Đang phục vụ</div>
            <button class="add-to-cart-btn" style="justify-content:center;" onclick="addToCart({{ $food->id }}, {{ Js::from($food->name) }}, {{ $food->price }}, {{ Js::from($food->image) }})"><i class="fa-solid fa-basket-shopping"></i> Thêm vào giỏ hàng</button>
        </div>
    </div>
</div>
@endsection
