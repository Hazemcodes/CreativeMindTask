<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();

        $profileData = [
            'id' => $user->id,
            'username' => $user->username,
            'user_type' => $user->user_type,
            'mobile_number' => $user->mobile_number,
            'location' => $user->location,
            'created_at' => $user->created_at,
        ];

        return response()->json(['profile' => $profileData]);
    }
}
