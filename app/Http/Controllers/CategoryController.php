<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $rq)
    {
        Log::info('Storing new category', ['request' => $rq->all()]);
        $rq->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();


            Category::create([
                'name' => $rq->name,
            ]);

            Log::info('Category created successfully', ['name' => $rq->name]);
            DB::commit();

            return response()->json([
                'data' => [
                    'success' => true,
                    'msg' => 'Category created successfully',
                    'name' => $rq->name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating category', ['error' => $e->getMessage()]);
            DB::rollBack();
            return redirect()->back()->with(['success' => false, 'msg' => 'Error creating category: ' . $e->getMessage()]);
        }
    }
}