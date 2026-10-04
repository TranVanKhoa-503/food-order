<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDeliveryZoneRequest;
use App\Http\Resources\DeliveryZoneResource;
use App\Models\DeliveryZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminDeliveryZoneController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return DeliveryZoneResource::collection(DeliveryZone::query()->orderBy('name')->get());
    }

    public function store(StoreDeliveryZoneRequest $request): JsonResponse
    {
        $zone = DeliveryZone::create($request->validated());

        return (new DeliveryZoneResource($zone))->response()->setStatusCode(201);
    }

    public function update(StoreDeliveryZoneRequest $request, DeliveryZone $deliveryZone): DeliveryZoneResource
    {
        $deliveryZone->update($request->validated());

        return new DeliveryZoneResource($deliveryZone->fresh());
    }

    public function destroy(DeliveryZone $deliveryZone): JsonResponse
    {
        $deliveryZone->delete();

        return response()->json(null, 204);
    }
}
