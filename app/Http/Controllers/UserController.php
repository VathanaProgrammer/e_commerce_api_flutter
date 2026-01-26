<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function index()
    {
        return view('users.index');
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            abort(404);
        }

        return view('users.profile', compact('user'));
    }

    public function data()
    {
        $currentUserId = Auth::id();

        $query = User::query();

        return DataTables::of($query)
            ->addColumn('full_name', function ($user) {
                return ($user->prefix ? $user->prefix . ' ' : '') . $user->first_name . ' ' . $user->last_name;
            })
            ->addColumn('profile_image_url', function ($user) {
                $url = $user->profile_image_url;
                return '<img src="' . $url . '" class="rounded-circle" width="40" height="40">';
            })
            ->addColumn('is_active', function ($user) {
                return $user->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($user) use ($currentUserId) {

                // Always allow edit
                $actions = '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-primary">
                            Edit
                        </a>';

                // ❌ Do NOT allow delete if this is the logged-in user
                if ($user->id !== $currentUserId) {
                    $actions .= '
                    <button class="btn btn-sm btn-danger delete-user"
                        data-url="' . route('users.destroy', $user->id) . '">
                        Delete
                    </button>';
                }

                return $actions;
            })
            ->rawColumns(['profile_image_url', 'is_active', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $img = 'https://static.vecteezy.com/system/resources/previews/013/042/571/original/default-avatar-profile-icon-social-media-user-photo-in-flat-style-vector.jpg';
        return view('users.create', compact('img'));
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        Log::info('data', ['request all' => $request->all()]);
        $request->validate([
            'prefix' => 'nullable|in:Mr,Miss,other',
            'first_name' => 'required|string|max:100',
            'last_name' => 'max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:255',
            'password' => 'required|string|min:3',
            'role' => 'required|in:admin,staff,customer',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $profileImageUrl = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/users'), $filename);
            $profileImageUrl = '/uploads/users/' . $filename;
        }

        $user = User::create([
            'prefix' => $request->prefix,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name ?? null,
            'email' => $request->email,
            'username' => $request->username,
            'password_hash' => Hash::make($request->password),
            'role' => $request->role,
            'gender' => $request->gender,
            'profile_image_url' => $profileImageUrl,
            'is_active' => $request->boolean('is_active', true),
            'phone' => $request->phone ?? null,
            'city' => $request->city ?? null,
            'address' => $request->address ?? null,
            'profile_completion' => $request->profile_completion ?? null
        ]);

        $user->assignRole($request->role);

        $output = [
            "success" => true,
            "msg" => "User created successfully.",
            "location" => route('users.index')
        ];

        return response()->json($output);
    }

    public function update(Request $request, $id)
    {
        Log::info('data', ['request all' => $request->all()]);
        $user = User::findOrFail($id);

        // Validate
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'username'   => 'nullable|string|max:100',
            'password'   => 'nullable|string|min:3',
            'role'       => 'required|in:admin,staff,customer',
            'gender'     => 'nullable|in:male,female,other',
            'prefix'     => 'nullable|in:Mr,Miss,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'  => 'required|boolean',
        ]);

        // Fill user data
        $user->fill([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name ?? null,
            'email'      => $request->email,
            'username'   => $request->username,
            'role'       => $request->role,
            'gender'     => $request->gender,
            'prefix'     => $request->prefix,
            'is_active'  => $request->boolean('is_active'),
            'phone' => $request->phone ?? null,
            'city' => $request->city ?? null,
            'address' => $request->address ?? null,
            'profile_completion' => $request->profile_completion ?? null
        ]);

        // Password
        if ($request->filled('password')) {
            $user->password_hash = bcrypt($request->password);
        }

        // Profile image (save to public/users)
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/users'), $filename);
            $user->profile_image_url = '/uploads/users/' . $filename;
        }

        $user->save();
        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'msg' => 'User updated successfully',
            'location' => route('users.index')
        ]);
    }
}