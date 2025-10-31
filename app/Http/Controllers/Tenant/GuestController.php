<?php

namespace  App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestContact;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!lin_array($user->role->name, ['RECEPTIONIST' ,'MANAGER'])){
            return  redirect()->route('user.dashboard')
            ->with('error', 'no  permission  ');
         }

         $query =  Guest::with([]);
         if($user->role->name== 'RECEPTIONIST'){
            $query->whereHas('property', function($q) use ($user){
                $q->where('tenant_id',$user->tenant_id);
            });
       } elseif ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
       }   
        elseif ($user->role->name === 'HOUSEKEEPER') {
            // Housekeepers see only their assigned tasks
            $query->where('assigned_to', $user->id);
        }

    }
}