<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('mobile_number', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is verified
            if (!$user->isVerified) {
                return response()->json(['error' => 'Account not verified'], 401);
            }

            $token = JWTAuth::fromUser($user); // Generate JWT token

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer'
                ]
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function register(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'user_type' => 'required|string|in:user,delivery',
            'mobile_number' => 'required|string|max:20|unique:users',
            'location' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Max file size 2MB (2048 KB)
        ]);

        try {
            $sid = getenv("TWILIO_ACCOUNT_SID");
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio = new Client($sid, $token);

            $message = $twilio->messages
                ->create(
                    $request->mobile_number, // to
                    array(
                        "from" => getenv("TWILIO_PHONE_NUMBER"),
                        "body" => "Hello to creative minds Company"
                    )
                );
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Use valid phone number with country code',]);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
        } else {
            $imageName = null;
        }

        $user = User::create([
            'username' => $request->username,
            'user_type' => $request->user_type,
            'mobile_number' => $request->mobile_number,
            'location' => $request->location,
            'password' => Hash::make($request->password),
            'image' => $imageName,
            'isVerified' => $message->sid ? true : false,
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
