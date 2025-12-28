<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    public function index()
    {
        return view('attributes.index');
    }

    public function data()
    {
        $query = Attribute::with('values');

        return DataTables::of($query)
            ->addColumn('values', function ($attr) {
                return $attr->values->pluck('value')->implode(', ');
            })
            ->addColumn('actions', function ($attr) {
                return '
                        <button class="btn btn-sm btn-primary edit-attr"
                            data-id="' . $attr->id . '"
                            data-name="' . $attr->name . '"
                            data-values="' . $attr->values->pluck('value')->implode(', ') . '">
                            Edit
                        </button>

                <button class="btn btn-sm btn-danger delete-attr"
                    data-url="' . route('attributes.destroy', $attr->id) . '">
                    Delete
                </button>
            ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'values' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create attribute
            $attribute = Attribute::create([
                'name' => $request->name,
            ]);

            // Handle values (comma separated)
            if ($request->filled('values')) {
                $values = array_filter(
                    array_map('trim', explode(',', $request->values))
                );

                foreach ($values as $value) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value'        => $value,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Attribute created successfully',
                'data'    => $attribute->load('values')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg' => 'Failed to create attribute',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);
        return view('attributes.edit', compact('attribute'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'values' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $attribute = Attribute::findOrFail($id);

            // Update attribute name
            $attribute->update([
                'name' => $request->name,
            ]);

            // Sync values
            AttributeValue::where('attribute_id', $id)->delete();

            if ($request->filled('values')) {
                $values = array_unique(
                    array_filter(array_map('trim', explode(',', $request->values)))
                );

                foreach ($values as $value) {
                    AttributeValue::create([
                        'attribute_id' => $id,
                        'value'        => $value,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute'
            ], 500);
        }
    }
}