<?php

namespace App\Services;

use App\Models\House;
use App\Models\Resident;
use Illuminate\Support\Facades\DB;

class HouseService
{
    public function assignResident(House $house, int $residentId, string $startDate, ?string $notes = null): void
    {
        if ($house->status === 'occupied') {
            throw new \RuntimeException('House is already occupied. Unassign current resident first.');
        }

        $resident = Resident::findOrFail($residentId);

        // Check if resident is already active in another house
        $alreadyAssigned = $resident->currentHouse()->exists();
        if ($alreadyAssigned) {
            throw new \RuntimeException('Resident is already assigned to another house.');
        }

        DB::transaction(function () use ($house, $residentId, $startDate, $notes) {
            $house->residents()->attach($residentId, [
                'start_date' => $startDate,
                'end_date'   => null,
                'is_active'  => true,
                'notes'      => $notes,
            ]);

            $house->update(['status' => 'occupied']);
        });
    }

    public function unassignResident(House $house, string $endDate, ?string $notes = null): void
    {
        if ($house->status === 'vacant') {
            throw new \RuntimeException('House has no active resident to unassign.');
        }

        DB::transaction(function () use ($house, $endDate, $notes) {
            // Close the active pivot record (keep for history — never delete)
            $house->residents()
                ->wherePivot('is_active', true)
                ->updateExistingPivot(
                    $house->activeResidents()->first()?->id,
                    ['end_date' => $endDate, 'is_active' => false, 'notes' => $notes]
                );

            $house->update(['status' => 'vacant']);
        });
    }
}
