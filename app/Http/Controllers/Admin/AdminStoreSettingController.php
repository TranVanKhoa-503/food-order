<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStoreSettingRequest;
use App\Http\Resources\DeliveryZoneResource;
use App\Models\DeliveryZone;
use App\Models\StoreSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStoreSettingController extends Controller
{
    public function edit(Request $request): View|JsonResponse
    {
        $setting = StoreSetting::query()->first() ?? StoreSetting::create(StoreSetting::defaults());
        $zones = DeliveryZone::query()->orderBy('name')->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['data' => $setting, 'delivery_zones' => DeliveryZoneResource::collection($zones)]);
        }

        return view('admin.settings.index', compact('setting', 'zones'));
    }

    public function update(UpdateStoreSettingRequest $request): JsonResponse
    {
        $setting = StoreSetting::query()->first() ?? StoreSetting::create(StoreSetting::defaults());
        $setting->update($request->validated());

        return response()->json(['data' => $setting->fresh()]);
    }
}
