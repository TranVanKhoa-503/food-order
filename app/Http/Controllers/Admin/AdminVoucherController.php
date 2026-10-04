<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminVoucherController extends Controller
{
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));
        $query = Voucher::query()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->paginate(min(max((int) $request->query('per_page', 15), 1), 50));

        if ($request->expectsJson() || $request->is('api/*')) {
            return VoucherResource::collection($vouchers);
        }

        return view('admin.vouchers.index', compact('vouchers', 'search'));
    }

    public function show(Voucher $voucher): VoucherResource
    {
        return new VoucherResource($voucher);
    }

    public function store(StoreVoucherRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['min_order_value'] = $data['min_order_value'] ?? 0;
        $data['is_active'] = (bool) $data['is_active'];

        return (new VoucherResource(Voucher::create($data)))
            ->response()
            ->setStatusCode(201);
    }

    public function update(StoreVoucherRequest $request, Voucher $voucher): VoucherResource
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['min_order_value'] = $data['min_order_value'] ?? 0;
        $voucher->update($data);

        return new VoucherResource($voucher->fresh());
    }

    public function destroy(Voucher $voucher): Response|JsonResponse
    {
        if ($voucher->orders()->exists()) {
            return response()->json([
                'message' => 'Voucher đã được dùng cho đơn hàng, hãy tắt trạng thái thay vì xóa.',
            ], 409);
        }

        $voucher->delete();

        return response()->noContent();
    }
}
