<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\StoreResidentRequest;
use App\Http\Requests\Resident\UpdateResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Models\Resident;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function index(Request $request): JsonResponse
    {
        $residents = Resident::with('currentHouse')
            ->when($request->search, fn ($q, $s) => $q->where('full_name', 'like', "%{$s}%")
                ->orWhere('phone_number', 'like', "%{$s}%"))
            ->when($request->resident_type, fn ($q, $t) => $q->where('resident_type', $t))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Residents retrieved successfully',
            'data'    => ResidentResource::collection($residents),
            'meta'    => [
                'current_page' => $residents->currentPage(),
                'per_page'     => $residents->perPage(),
                'total'        => $residents->total(),
                'last_page'    => $residents->lastPage(),
            ],
        ]);
    }

    public function store(StoreResidentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['ktp_photo_path'] = $this->fileUpload->storeKtpPhoto($request->file('ktp_photo'));
        unset($data['ktp_photo']);

        $resident = Resident::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Resident created successfully',
            'data'    => new ResidentResource($resident),
        ], 201);
    }

    public function show(Resident $resident): JsonResponse
    {
        $resident->load('currentHouse');

        return response()->json([
            'success' => true,
            'data'    => new ResidentResource($resident),
        ]);
    }

    public function update(UpdateResidentRequest $request, Resident $resident): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('ktp_photo')) {
            $this->fileUpload->deleteFile($resident->ktp_photo_path);
            $data['ktp_photo_path'] = $this->fileUpload->storeKtpPhoto($request->file('ktp_photo'));
            unset($data['ktp_photo']);
        }

        $resident->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Resident updated successfully',
            'data'    => new ResidentResource($resident->fresh()),
        ]);
    }

    public function destroy(Resident $resident): JsonResponse
    {
        $this->fileUpload->deleteFile($resident->ktp_photo_path);
        $resident->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resident deleted successfully',
        ]);
    }
}
