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

    }
}