<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $query = Guest::where('tenant_id', $tenant->id)
            ->with(['creator', 'updater', 'contacts']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('guest_type')) {
            $query->where('guest_type', $request->guest_type);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $guests = $query->paginate(20);

        return response()->json([
            'success' => true,
            'guests' => $guests
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'id_type' => 'required|in:PASSPORT,NATIONAL_ID,DRIVER_LICENSE,OTHER',
            'id_number' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'nationality' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'guest_type' => 'required|in:INDIVIDUAL,CORPORATE,VIP,GROUP',
            'corporate_account_id' => 'nullable|uuid|exists:corporate_accounts,id',
            'preferences' => 'nullable|string',
            'notes' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required|string|max:100',
            'contacts.*.relationship' => 'required|string|max:50',
            'contacts.*.phone' => 'required|string|max:20',
            'contacts.*.email' => 'nullable|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $guest = Guest::create([
                'tenant_id' => $tenant->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'guest_type' => $request->guest_type,
                'corporate_account_id' => $request->corporate_account_id,
                'preferences' => $request->preferences,
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            if ($request->filled('contacts')) {
                foreach ($request->contacts as $contactData) {
                    GuestContact::create([
                        'guest_id' => $guest->id,
                        'name' => $contactData['name'],
                        'relationship' => $contactData['relationship'],
                        'phone' => $contactData['phone'],
                        'email' => $contactData['email'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guest created successfully',
                'guest' => $guest->load('contacts')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating guest: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating guest: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $guest = Guest::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with([
                'contacts',
                'creator',
                'updater',
                'corporateAccount',
                'reservations' => function($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                }
            ])
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'guest' => $guest
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $guest = Guest::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'id_type' => 'sometimes|required|in:PASSPORT,NATIONAL_ID,DRIVER_LICENSE,OTHER',
            'id_number' => 'sometimes|required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'nationality' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'guest_type' => 'sometimes|required|in:INDIVIDUAL,CORPORATE,VIP,GROUP',
            'corporate_account_id' => 'nullable|uuid|exists:corporate_accounts,id',
            'preferences' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $guest->update([
                'first_name' => $request->get('first_name', $guest->first_name),
                'last_name' => $request->get('last_name', $guest->last_name),
                'email' => $request->get('email', $guest->email),
                'phone' => $request->get('phone', $guest->phone),
                'id_type' => $request->get('id_type', $guest->id_type),
                'id_number' => $request->get('id_number', $guest->id_number),
                'date_of_birth' => $request->get('date_of_birth', $guest->date_of_birth),
                'gender' => $request->get('gender', $guest->gender),
                'nationality' => $request->get('nationality', $guest->nationality),
                'country' => $request->get('country', $guest->country),
                'city' => $request->get('city', $guest->city),
                'address' => $request->get('address', $guest->address),
                'postal_code' => $request->get('postal_code', $guest->postal_code),
                'guest_type' => $request->get('guest_type', $guest->guest_type),
                'corporate_account_id' => $request->get('corporate_account_id', $guest->corporate_account_id),
                'preferences' => $request->get('preferences', $guest->preferences),
                'notes' => $request->get('notes', $guest->notes),
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guest updated successfully',
                'guest' => $guest->load('contacts')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating guest: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating guest: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reservationHistory($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $guest = Guest::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        $reservations = $guest->reservations()
            ->with(['property', 'reservationRooms.room', 'reservationRooms.roomType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'guest' => [
                'id' => $guest->id,
                'full_name' => $guest->first_name . ' ' . $guest->last_name,
                'email' => $guest->email,
                'phone' => $guest->phone,
            ],
            'reservations' => $reservations
        ]);
    }

    public function addContact(Request $request, $guestId)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $guest = Guest::where('tenant_id', $tenant->id)
            ->where('id', $guestId)
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'relationship' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contact = GuestContact::create([
                'guest_id' => $guest->id,
                'name' => $request->name,
                'relationship' => $request->relationship,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact added successfully',
                'contact' => $contact
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error adding contact: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding contact: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeContact($guestId, $contactId)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $guest = Guest::where('tenant_id', $tenant->id)
            ->where('id', $guestId)
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        $contact = GuestContact::where('guest_id', $guest->id)
            ->where('id', $contactId)
            ->first();

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        try {
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact removed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error removing contact: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error removing contact: ' . $e->getMessage()
            ], 500);
        }
    }
}
