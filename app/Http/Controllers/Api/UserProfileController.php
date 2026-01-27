<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Get user profile by ID
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'prefix' => $user->prefix,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'gender' => $user->gender,
                    'profile_image_url' => $user->profile_image_url,
                    'is_active' => $user->is_active,
                    'last_login' => $user->last_login,
                    'username' => $user->username,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'city' => $user->city,
                    'address' => $user->address,
                    'profile_completion' => $user->profile_completion ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update user profile
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Profile update request', ['user_id' => $id, 'data' => $request->all()]);
            
            $user = User::findOrFail($id);

            // Validation rules
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'username' => 'nullable|string|max:100|unique:users,username,' . $user->id,
                'password' => 'nullable|string|min:3',
                'gender' => 'nullable|in:male,female,other',
                'prefix' => 'nullable|in:Mr,Miss,other',
                'phone' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'profile_image' => 'nullable|string', // base64 or URL
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user fields
            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }
            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }
            if ($request->has('prefix')) {
                $user->prefix = $request->prefix;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('city')) {
                $user->city = $request->city;
            }
            if ($request->has('address')) {
                $user->address = $request->address;
            }

            // Handle password update
            if ($request->filled('password')) {
                $user->password_hash = Hash::make($request->password);
            }

            // Handle profile image (base64 or file upload)
            if ($request->has('profile_image') && !empty($request->profile_image)) {
                $profileImage = $request->profile_image;
                
                // Check if it's base64
                if (preg_match('/^data:image\/(\w+);base64,/', $profileImage, $type)) {
                    $data = substr($profileImage, strpos($profileImage, ',') + 1);
                    $data = base64_decode($data);
                    
                    $extension = strtolower($type[1]);
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $path = public_path('uploads/users');
                    
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    
                    file_put_contents($path . '/' . $filename, $data);
                    $user->profile_image_url = '/uploads/users/' . $filename;
                }
            }

            // Calculate profile completion
            $user->profile_completion = $this->calculateProfileCompletion($user);

            $user->save();

            Log::info('Profile updated successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'prefix' => $user->prefix,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'gender' => $user->gender,
                    'profile_image_url' => $user->profile_image_url,
                    'is_active' => $user->is_active,
                    'last_login' => $user->last_login,
                    'username' => $user->username,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'city' => $user->city,
                    'address' => $user->address,
                    'profile_completion' => $user->profile_completion,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user)
    {
        $fields = [
            'first_name',
            'last_name',
            'email',
            'username',
            'gender',
            'phone',
            'city',
            'address',
            'profile_image_url',
            'prefix'
        ];

        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / count($fields)) * 100);
    }
}
