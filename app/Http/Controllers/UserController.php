<?php

namespace App\Http\Controllers;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    //

    public function index()
    {
        return view('users.index');
    }


    public function data()
    {
        $query = User::query();

        return DataTables::of($query)
            ->addColumn('full_name', function ($user) {
                return ($user->prefix ? $user->prefix . ' ' : '') . $user->first_name . ' ' . $user->last_name;
            })
            ->addColumn('profile_image_url', function ($user) {
                $url = $user->profile_image_url ?: '/img/default-profile.png';
                return '<img src="' . $url . '" class="rounded-circle" width="40" height="40">';
            })
            ->addColumn('is_active', function ($user) {
                return $user->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($user) {
                return '
                <a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger delete-user" data-url="' . route('users.destroy', $user->id) . '">
                    Delete
                </button>
            ';
            })
            ->rawColumns(['profile_image_url', 'is_active', 'actions'])
            ->make(true);
    }
}