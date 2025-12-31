<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
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
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s A') : '-';
                })
                ->addColumn('actions', function ($row) {
                    $edit = '<button data-id="'. $row->id .'" data-name="' . $row->name . '" type="button" class="edit-category btn btn-sm btn-primary me-1">Edit</button>';
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

    public function destroy(Request $request, $id)
    {
        $output = ['success' => false, 'msg' => 'Something went wrong'];

        try {
            DB::beginTransaction();

            $category = Category::find($id);

            if (! $category) {
                $output = [
                    'success' => false,
                    'msg' => 'Category not found'
                ];
                return response()->json(['data' => $output]);
            }

            $category->delete();

            DB::commit();

            $output = [
                'success' => true,
                'msg' => 'Category deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Category delete failed', [
                'id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        return response()->json(['data' => $output]);
    }

    public function update(Request $request, $id)
    {
        Log::info('Updating category', ['id' => $id, 'request' => $request->all()]);
        $output = ['success' => false, 'msg' => 'Something went wrong'];

        try {
            $category = Category::find($id);

            if (!$category) {
                $output['msg'] = 'Category not found';
                return response()->json(['data' => $output]);
            }

            $category->name = $request->input('name');
            $category->save();

            $output['success'] = true;
            $output['msg'] = 'Category updated successfully';
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            $output['msg'] = 'Failed to update category';
        }

        return response()->json(['data' => $output]);
    }
}