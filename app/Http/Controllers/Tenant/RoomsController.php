<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Property;
use App\Models\Building;
use App\Models\Floor;
use App\Models\RoomType;
use App\Models\RoomFeature;
use App\Services\TenantSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomsController extends Controller
{
    /**
     * Display a listing of rooms for the current property
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only MANAGER and DIRECTOR roles can access room management
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access room management.');
        }

        // Get rooms for the current property with tenant filtering
        $query = Room::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        })->with(['property', 'floor.building', 'roomType', 'features']);

        // If user is MANAGER, only show rooms from their property
        if ($user->role->name === 'MANAGER') {
            $query->where('property_id', $user->property_id);
        }

        $rooms = $query->orderBy('room_number', 'asc')->paginate(20);

        // Get properties for filtering (DIRECTOR sees all, MANAGER sees only theirs)
        $properties = collect();
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)->get();
        } elseif ($user->property) {
            $properties = collect([$user->property]);
        }

        return view('Users.tenant.rooms.index', compact('rooms', 'properties'));
    }

    /**
     * Show the form for creating a new room
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only MANAGER and DIRECTOR roles can create rooms
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.rooms.index')
                ->with('error', 'You do not have permission to create rooms.');
        }

        // Get data for form
        $properties = $this->getUserProperties($user);
        $buildings = collect();
        $floors = collect();
        $roomTypes = collect();
        $roomFeatures = collect();

        // If user is MANAGER, get data for their property
        if ($user->role->name === 'MANAGER' && $user->property) {
            $buildings = $user->property->buildings()->get();
            $roomTypes = $user->property->roomTypes()->get();
            $roomFeatures = RoomFeature::where('tenant_id', $user->tenant_id)->get();
        }

        return view('Users.tenant.rooms.create', compact(
            'properties', 'buildings', 'floors', 'roomTypes', 'roomFeatures'
        ));
    }

    /**
     * Store a newly created room in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only MANAGER and DIRECTOR roles can create rooms
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.rooms.index')
                ->with('error', 'You do not have permission to create rooms.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|string|max:20',
            'status' => 'required|in:' . implode(',', Room::STATUSES),
            'current_rate' => 'required|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'features.*' => 'uuid|exists:room_features,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to user's tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // If user is MANAGER, they can only create rooms for their property
            if ($user->role->name === 'MANAGER' && $validated['property_id'] !== $user->property_id) {
                throw ValidationException::withMessages([
                    'property_id' => ['You can only create rooms for your assigned property.']
                ]);
            }

            // Verify floor belongs to property
            $floor = Floor::whereHas('building', function($q) use ($validated) {
                $q->where('property_id', $validated['property_id']);
            })->where('id', $validated['floor_id'])->firstOrFail();

            // Verify room type belongs to property
            $roomType = RoomType::where('id', $validated['room_type_id'])
                ->where('property_id', $validated['property_id'])
                ->firstOrFail();

            // Check if room number already exists for this property
            $existingRoom = Room::where('property_id', $validated['property_id'])
                ->where('room_number', $validated['room_number'])
                ->first();

            if ($existingRoom) {
                throw ValidationException::withMessages([
                    'room_number' => ['Room number already exists for this property.']
                ]);
            }

            // Create the room
            $room = Room::create([
                'property_id' => $validated['property_id'],
                'floor_id' => $validated['floor_id'],
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $validated['room_number'],
                'status' => $validated['status'],
                'current_rate' => $validated['current_rate'],
                'notes' => $validated['notes'],
            ]);

            // Attach room features if provided
            if (!empty($validated['features'])) {
                // Verify features belong to tenant
                $features = RoomFeature::where('tenant_id', $user->tenant_id)
                    ->whereIn('id', $validated['features'])
                    ->pluck('id')
                    ->toArray();
                
                $room->features()->attach($features);
            }

            DB::commit();

            return redirect()->route('tenant.rooms.show', $room->id)
                ->with('success', 'Room created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create room. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified room
     */
    public function show(Room $room)
    {
        $user = Auth::user();
        
        // Verify room belongs to user's tenant
        TenantSecurityService::abortUnlessOwner($room->property, 'tenant_id');

        // If not DIRECTOR, only show rooms from same property
        if ($user->role->name !== 'DIRECTOR' && $room->property_id !== $user->property_id) {
            abort(403, 'Unauthorized access to room.');
        }

        // Load relationships
        $room->load(['property', 'floor.building', 'roomType', 'features']);

        return view('Users.tenant.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room
     */
    public function edit(Room $room)
    {
        $user = Auth::user();
        
        // Verify room belongs to user's tenant
        TenantSecurityService::abortUnlessOwner($room->property, 'tenant_id');

        // Permission checks
        if (!$this->canEditRoom($user, $room)) {
            return redirect()->route('tenant.rooms.show', $room->id)
                ->with('error', 'You do not have permission to edit this room.');
        }

        // Get data for form
        $properties = $this->getUserProperties($user);
        $buildings = $room->property->buildings()->get();
        $floors = $room->floor->building->floors()->get();
        $roomTypes = $room->property->roomTypes()->get();
        $roomFeatures = RoomFeature::where('tenant_id', $user->tenant_id)->get();

        // Load current features
        $room->load('features');

        return view('Users.tenant.rooms.edit', compact(
            'room', 'properties', 'buildings', 'floors', 'roomTypes', 'roomFeatures'
        ));
    }

    /**
     * Update the specified room in storage
     */
    public function update(Request $request, Room $room)
    {
        $user = Auth::user();
        
        // Verify room belongs to user's tenant
        TenantSecurityService::abortUnlessOwner($room->property, 'tenant_id');

        // Permission checks
        if (!$this->canEditRoom($user, $room)) {
            return redirect()->route('tenant.rooms.show', $room->id)
                ->with('error', 'You do not have permission to edit this room.');
        }

        $validated = $request->validate([
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|string|max:20',
            'status' => 'required|in:' . implode(',', Room::STATUSES),
            'current_rate' => 'required|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'features.*' => 'uuid|exists:room_features,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify floor belongs to same property
            $floor = Floor::whereHas('building', function($q) use ($room) {
                $q->where('property_id', $room->property_id);
            })->where('id', $validated['floor_id'])->firstOrFail();

            // Verify room type belongs to same property
            $roomType = RoomType::where('id', $validated['room_type_id'])
                ->where('property_id', $room->property_id)
                ->firstOrFail();

            // Check if room number already exists for this property (excluding current room)
            $existingRoom = Room::where('property_id', $room->property_id)
                ->where('room_number', $validated['room_number'])
                ->where('id', '!=', $room->id)
                ->first();

            if ($existingRoom) {
                throw ValidationException::withMessages([
                    'room_number' => ['Room number already exists for this property.']
                ]);
            }

            // Update the room
            $room->update([
                'floor_id' => $validated['floor_id'],
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $validated['room_number'],
                'status' => $validated['status'],
                'current_rate' => $validated['current_rate'],
                'notes' => $validated['notes'],
            ]);

            // Update room features
            $room->features()->detach();
            if (!empty($validated['features'])) {
                // Verify features belong to tenant
                $features = RoomFeature::where('tenant_id', $user->tenant_id)
                    ->whereIn('id', $validated['features'])
                    ->pluck('id')
                    ->toArray();
                
                $room->features()->attach($features);
            }

            DB::commit();

            return redirect()->route('tenant.rooms.show', $room->id)
                ->with('success', 'Room updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update room. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified room from storage
     */
    public function destroy(Room $room)
    {
        $user = Auth::user();
        
        // Verify room belongs to user's tenant
        TenantSecurityService::abortUnlessOwner($room->property, 'tenant_id');

        // Only DIRECTOR and MANAGER can delete rooms
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete rooms.'
            ], 403);
        }

        // If MANAGER, can only delete rooms from their property
        if ($user->role->name === 'MANAGER' && $room->property_id !== $user->property_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete rooms from your assigned property.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Check if room has active reservations
            $activeReservations = $room->reservationRooms()
                ->whereHas('reservation', function($q) {
                    $q->where('status', 'CONFIRMED');
                })
                ->count();

            if ($activeReservations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete room with active reservations."
                ], 400);
            }

            // Detach features
            $room->features()->detach();
            
            // Soft delete the room
            $room->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room. Please try again.'
            ], 500);
        }
    }

    /**
     * Get buildings for a property (AJAX)
     */
    public function getBuildings($propertyId)
    {
        $user = Auth::user();
        
        // Verify property belongs to user's tenant
        $property = Property::where('id', $propertyId)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // If MANAGER, can only access their property
        if ($user->role->name === 'MANAGER' && $propertyId !== $user->property_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $buildings = $property->buildings()->get(['id', 'name']);

        return response()->json($buildings);
    }

    /**
     * Get floors for a building (AJAX)
     */
    public function getFloors($buildingId)
    {
        $user = Auth::user();
        
        // Verify building belongs to user's tenant through property
        $building = Building::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        })->where('id', $buildingId)->first();

        if (!$building) {
            return response()->json(['error' => 'Building not found'], 404);
        }

        // If MANAGER, can only access buildings from their property
        if ($user->role->name === 'MANAGER' && $building->property_id !== $user->property_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $floors = $building->floors()->orderBy('number', 'asc')->get(['id', 'number', 'description']);

        return response()->json($floors);
    }

    /**
     * Get room types for a property (AJAX)
     */
    public function getRoomTypes($propertyId)
    {
        $user = Auth::user();
        
        // Verify property belongs to user's tenant
        $property = Property::where('id', $propertyId)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // If MANAGER, can only access their property
        if ($user->role->name === 'MANAGER' && $propertyId !== $user->property_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $roomTypes = $property->roomTypes()->get(['id', 'name', 'base_rate']);

        return response()->json($roomTypes);
    }

    /**
     * Update room status (AJAX)
     */
    public function updateStatus(Request $request, Room $room)
    {
        $user = Auth::user();
        
        // Verify room belongs to user's tenant
        TenantSecurityService::abortUnlessOwner($room->property, 'tenant_id');

        // Permission checks
        if (!$this->canEditRoom($user, $room)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this room.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', Room::STATUSES),
        ]);

        try {
            $room->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Room status updated successfully!',
                'room' => $room->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room status.'
            ], 500);
        }
    }

    /**
     * Get available properties for the user
     */
    private function getUserProperties($user)
    {
        if ($user->role->name === 'DIRECTOR') {
            return Property::where('tenant_id', $user->tenant_id)->get();
        } elseif ($user->role->name === 'MANAGER' && $user->property) {
            return collect([$user->property]);
        }

        return collect();
    }

    /**
     * Check if current user can edit the room
     */
    private function canEditRoom($currentUser, $room)
    {
        // DIRECTOR can edit any room in their tenant
        if ($currentUser->role->name === 'DIRECTOR') {
            return true;
        }

        // MANAGER can edit rooms in their property
        if ($currentUser->role->name === 'MANAGER') {
            return $room->property_id === $currentUser->property_id;
        }

        return false;
    }
}
