<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\House\AssignResidentRequest;
use App\Http\Requests\House\StoreHouseRequest;
use App\Http\Requests\House\UnassignResidentRequest;
use App\Http\Requests\House\UpdateHouseRequest;
use App\Http\Resources\HouseDetailResource;
use App\Http\Resources\HouseResource;
use App\Models\House;
use App\Services\HouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function __construct(private HouseService $houseService) {}

    public function index(Request $request): JsonResponse
    {
        $houses = House::with('activeResidents')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->ownership_type, fn ($q, $t) => $q->where('ownership_type', $t))
            ->when($request->search, fn ($q, $s) => $q->where('house_number', 'like', "%{$s}%"))
            ->orderBy('house_number')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Houses retrieved successfully',
            'data'    => HouseResource::collection($houses),
            'meta'    => [
                'current_page' => $houses->currentPage(),
                'per_page'     => $houses->perPage(),
                'total'        => $houses->total(),
                'last_page'    => $houses->lastPage(),
            ],
        ]);
    }

    public function store(StoreHouseRequest $request): JsonResponse
    {
        $house = House::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'House created successfully',
            'data'    => new HouseResource($house),
        ], 201);
    }

    public function show(House $house): JsonResponse
    {
        $house->load(['activeResidents', 'residents', 'bills']);

        return response()->json([
            'success' => true,
            'data'    => new HouseDetailResource($house),
        ]);
    }

    public function update(UpdateHouseRequest $request, House $house): JsonResponse
    {
        $house->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'House updated successfully',
            'data'    => new HouseResource($house->fresh()->load('activeResidents')),
        ]);
    }

    public function destroy(House $house): JsonResponse
    {
        if ($house->bills()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete house with existing bills.',
            ], 409);
        }

        $house->delete();

        return response()->json([
            'success' => true,
            'message' => 'House deleted successfully',
        ]);
    }

    public function assignResident(AssignResidentRequest $request, House $house): JsonResponse
    {
        try {
            $this->houseService->assignResident(
                $house,
                $request->integer('resident_id'),
                $request->start_date,
                $request->notes
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident assigned successfully',
            'data'    => new HouseDetailResource($house->fresh()->load(['activeResidents', 'residents', 'bills'])),
        ]);
    }

    public function unassignResident(UnassignResidentRequest $request, House $house): JsonResponse
    {
        try {
            $this->houseService->unassignResident(
                $house,
                $request->end_date,
                $request->notes
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident unassigned successfully',
            'data'    => new HouseDetailResource($house->fresh()->load(['activeResidents', 'residents', 'bills'])),
        ]);
    }
}
