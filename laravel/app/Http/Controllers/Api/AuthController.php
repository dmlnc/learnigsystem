<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ThumbnailResource;
use App\Models\User;

class AuthController extends Controller
{

    /**
     * Login a user and return an access token for study.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studyLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            
            $token = $user->createToken('spa')->plainTextToken;

            $data = [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],

                'company' => new CompanyResource($user->company),
                
            ];


            
            return response()->json($data, 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Login a user and return an access token for admin panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            if(!$user->isAdmin){
                return response()->json(['message' => 'Access not allowed'], 401);
            }

            $token = $user->createToken('spa')->plainTextToken;

            $data = [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'abilities' => $user->roles()->with('permissions')->get()
                        ->pluck('permissions')
                        ->flatten()
                        ->pluck('title')
                        ->toArray(),
                ],

                'company' => new CompanyResource($user->company),
                
            ];


            
            return response()->json($data, 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Logout the authenticated user and invalidate their access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
