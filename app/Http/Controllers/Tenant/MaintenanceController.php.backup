<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access maintenance management.');
        }

        $query = MaintenanceRequest::with(['property', 'room', 'reportedBy', 'assignedTo']);

        // Filter by tenant's properties
        if ($user->role->name === 'DIRECTOR') {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show open and in-progress requests
            $query->whereIn('status', ['OPEN', 'ASSIGNED', 'IN_PROGRESS']);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $requests = $query->latest('reported_at')->paginate(20);

        // Get properties for filter
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.maintenance.index', compact('requests', 'properties'));
    }

    /**
     * Show the form for creating a new maintenance request
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create maintenance requests.');
        }

        // Get properties
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        // Get maintenance staff (can be assigned supervisors or other staff)
        $staff = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['SUPERVISOR', 'MANAGER']);
            })
            ->where('is_active', true)
            ->get();

        // Get rooms if property is pre-selected
        $rooms = collect();
        if ($request->filled('property_id')) {
            $rooms = Room::where('property_id', $request->property_id)
                ->with('roomType')
                ->get();
        }

        return view('Users.tenant.maintenance.create', compact('properties', 'staff', 'rooms'));
    }

    /**
     * Store a newly created maintenance request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create maintenance requests.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'location_details' => 'nullable|string|max:255',
            'issue_type' => 'required|string|max:100',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'assigned_to' => 'nullable|uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $maintenanceRequest = MaintenanceRequest::create([
                'property_id' => $property->id,
                'room_id' => $validated['room_id'] ?? null,
                'location_details' => $validated['location_details'] ?? null,
                'issue_type' => $validated['issue_type'],
                'description' => $validated['description'],
                'reported_by' => $user->id,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'status' => $validated['assigned_to'] ? 'ASSIGNED' : 'OPEN',
                'priority' => $validated['priority'],
                'reported_at' => now(),
                'assigned_at' => $validated['assigned_to'] ? now() : null,
            ]);

            // Update room status if maintenance is urgent and room is specified
            if ($validated['priority'] === 'URGENT' && isset($validated['room_id'])) {
                $room = Room::find($validated['room_id']);
                if ($room && in_array($room->status, ['AVAILABLE', 'CLEAN'])) {
                    $room->update(['status' => 'MAINTENANCE']);
                }
            }

            DB::commit();

            return redirect()->route('tenant.maintenance.show', $maintenanceRequest)
                ->with('success', 'Maintenance request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create maintenance request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified maintenance request
     */
    public function show(MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        $maintenance->load(['property', 'room.roomType', 'reportedBy', 'assignedTo']);

        return view('Users.tenant.maintenance.show', compact('maintenance'));
    }

    /**
     * Update maintenance request status
     */
    public function updateStatus(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return back()->with('error', 'You do not have permission to update maintenance requests.');
        }

        $validated = $request->validate([
            'status' => 'required|in:OPEN,ASSIGNED,IN_PROGRESS,ON_HOLD,COMPLETED,CANCELLED',
            'resolution_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $newStatus = $validated['status'];
            $updateData = ['status' => $newStatus];

            // Set timestamps based on status
            if ($newStatus === 'IN_PROGRESS' && !$maintenance->started_at) {
                $updateData['started_at'] = now();
            } elseif ($newStatus === 'COMPLETED' && !$maintenance->completed_at) {
                $updateData['completed_at'] = now();
                
                // Update room status if it was in maintenance
                if ($maintenance->room && $maintenance->room->status === 'MAINTENANCE') {
                    $maintenance->room->update(['status' => 'DIRTY']);
                }
            }

            if ($request->filled('resolution_notes')) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }

            $maintenance->update($updateData);

            DB::commit();

            return back()->with('success', 'Maintenance request status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update maintenance request: ' . $e->getMessage());
        }
    }

    /**
     * Assign maintenance request to staff
     */
    public function assign(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return back()->with('error', 'You do not have permission to assign maintenance requests.');
        }

        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|uuid|exists:users,id',
        ]);

        try {
            $staff = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $maintenance->update([
                'assigned_to' => $staff->id,
                'assigned_at' => now(),
                'status' => 'ASSIGNED',
            ]);

            return back()->with('success', 'Maintenance request assigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign maintenance request: ' . $e->getMessage());
        }
    }
}
