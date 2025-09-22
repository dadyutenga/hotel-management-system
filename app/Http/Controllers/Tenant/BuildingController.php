<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    /**
     * Store a newly created building
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        // Verify property belongs to user's tenant
        $property = Property::where('id', $validated['property_id'])
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        // Only DIRECTOR and MANAGER roles can create buildings
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create buildings.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $building = Building::create([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Building created successfully!',
                'building' => $building
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create building. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified building
     */
    public function update(Request $request, Building $building)
    {
        $user = Auth::user();
        
        // Verify building belongs to user's tenant
        if ($building->property->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to building.'
            ], 403);
        }

        // Only DIRECTOR and MANAGER roles can edit buildings
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit buildings.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $building->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Building updated successfully!',
                'building' => $building
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update building. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified building
     */
    public function destroy(Building $building)
    {
        $user = Auth::user();
        
        // Verify building belongs to user's tenant
        if ($building->property->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to building.'
            ], 403);
        }

        // Only DIRECTOR role can delete buildings
        if ($user->role->name !== 'DIRECTOR') {
            return response()->json([
                'success' => false,
                'message' => 'Only business directors can delete buildings.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Check if building has floors
            $floorsCount = $building->floors()->count();
            if ($floorsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete building with {$floorsCount} floors. Please remove floors first."
                ], 400);
            }

            $building->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Building deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete building. Please try again.'
            ], 500);
        }
    }
}