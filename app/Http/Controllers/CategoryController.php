<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index()
    {
        return view('categories.index');
    }
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::query(); // <-- Pass the query, not get()

            return DataTables::of($query)
                ->addColumn('actions', function ($row) {
                    $edit = '<a href="' . route('categories.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">Edit</a>';
                    $delete = '<button data-url="' . route('categories.destroy', $row->id) . '" class="btn btn-sm btn-danger delete-category">Delete</button>';
                    return $edit . $delete;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }
}