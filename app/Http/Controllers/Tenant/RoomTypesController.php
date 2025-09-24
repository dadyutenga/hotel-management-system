<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomType;
use App\Services\TenantSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomTypesController extends Controller
{
    protected $tenantSecurity;

    public function __construct(TenantSecurityService $tenantSecurity)
    {
        $this->tenantSecurity = $tenantSecurity;
    }

    /**
     * Display a listing of room types
     */
    public function index(Request $request)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        $propertyId = $request->get('property_id');
        $search = $request->get('search');

        // Get properties for the current tenant
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        // Build query for room types
        $roomTypesQuery = RoomType::with(['property'])
            ->whereHas('property', function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            });

        // Apply filters
        if ($propertyId) {
            // Validate property belongs to current tenant
            $property = Property::where('id', $propertyId)
                ->where('tenant_id', $user->tenant_id)
                ->first();
            
            if (!$property) {
                abort(403, 'Unauthorized access to this property');
            }
            
            $roomTypesQuery->where('property_id', $propertyId);
        }

        if ($search) {
            $roomTypesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roomTypes = $roomTypesQuery->orderBy('name')->paginate(12);

        return view('Users.tenant.room-types.index', compact('roomTypes', 'properties', 'propertyId', 'search'));
    }

    /**
     * Show the form for creating a new room type
     */
    public function create(Request $request)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        $propertyId = $request->get('property_id');
        
        // Get properties for the current tenant
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        $selectedProperty = null;
        if ($propertyId) {
            $selectedProperty = Property::where('id', $propertyId)
                ->where('tenant_id', $user->tenant_id)
                ->first();
                
            if (!$selectedProperty) {
                abort(403, 'Unauthorized access to this property');
            }
        }

        return view('Users.tenant.room-types.create', compact('properties', 'selectedProperty'));
    }

    /**
     * Store a newly created room type
     */
    public function store(Request $request)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        // Fix: Validate only fields that exist in the model
        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'base_rate' => 'required|numeric|min:0|max:999999.99',
            'capacity' => 'required|integer|min:1|max:20'  // Fixed: was max_occupancy
        ]);

        // Validate property belongs to current tenant
        $property = Property::where('id', $validated['property_id'])
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$property) {
            abort(403, 'Unauthorized access to this property');
        }

        // Check for duplicate room type names within the property
        $existingRoomType = RoomType::where('property_id', $validated['property_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingRoomType) {
            return back()->withErrors([
                'name' => 'A room type with this name already exists in this property.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Fix: Create with only model fillable fields
            $roomType = RoomType::create([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_rate' => $validated['base_rate'],
                'capacity' => $validated['capacity']  // Fixed: matches the model field
            ]);

            DB::commit();

            return redirect()->route('tenant.room-types.show', $roomType->id)
                ->with('success', 'Room type created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to create room type. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Display the specified room type
     */
    public function show($id)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        $roomType = RoomType::with(['property', 'rooms.floor.building'])
            ->where('id', $id)
            ->first();

        if (!$roomType) {
            abort(404, 'Room type not found');
        }

        // Validate tenant security
        if ($roomType->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this room type');
        }

        return view('Users.tenant.room-types.show', compact('roomType'));
    }

    /**
     * Show the form for editing the specified room type
     */
    public function edit($id)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        $roomType = RoomType::with(['property'])
            ->where('id', $id)
            ->first();

        if (!$roomType) {
            abort(404, 'Room type not found');
        }

        // Validate tenant security
        if ($roomType->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this room type');
        }

        // Get properties for the current tenant
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Users.tenant.room-types.edit', compact('roomType', 'properties'));
    }

    /**
     * Update the specified room type
     */
    public function update(Request $request, $id)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            abort(403, 'Unauthorized access to room types management');
        }

        $roomType = RoomType::where('id', $id)->first();

        if (!$roomType) {
            abort(404, 'Room type not found');
        }

        // Validate tenant security
        if ($roomType->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this room type');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'base_rate' => 'required|numeric|min:0|max:999999.99',
            'capacity' => 'required|integer|min:1|max:20'
        ]);

        // Validate new property belongs to current tenant
        $property = Property::where('id', $validated['property_id'])
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$property) {
            abort(403, 'Unauthorized access to this property');
        }

        // Check for duplicate room type names within the property (excluding current)
        $existingRoomType = RoomType::where('property_id', $validated['property_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingRoomType) {
            return back()->withErrors([
                'name' => 'A room type with this name already exists in this property.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $roomType->update([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_rate' => $validated['base_rate'],
                'capacity' => $validated['capacity']
            ]);

            DB::commit();

            return redirect()->route('tenant.room-types.show', $roomType->id)
                ->with('success', 'Room type updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to update room type. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Remove the specified room type
     */
    public function destroy($id)
    {
        // Ensure user has proper permissions
        $user = Auth::user();
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $roomType = RoomType::with(['rooms'])->where('id', $id)->first();

        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Room type not found'], 404);
        }

        // Validate tenant security
        if ($roomType->property->tenant_id !== $user->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Check if room type has associated rooms
        if ($roomType->rooms->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete room type. There are ' . $roomType->rooms->count() . ' rooms using this type.'
            ], 422);
        }

        try {
            $roomType->delete();
            return response()->json(['success' => true, 'message' => 'Room type deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete room type'], 500);
        }
    }

    /**
     * Get room types for a specific property (AJAX)
     */
    public function getRoomTypesByProperty($propertyId)
    {
        // Validate property belongs to current tenant
        $user = Auth::user();
        $property = Property::where('id', $propertyId)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $roomTypes = RoomType::where('property_id', $propertyId)
            ->orderBy('name')
            ->get(['id', 'name', 'base_rate', 'capacity']);

        return response()->json($roomTypes);
    }
}