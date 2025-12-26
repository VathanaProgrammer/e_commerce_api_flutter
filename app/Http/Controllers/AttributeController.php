<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->get();
        return view('attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:attributes,name',
            'values.*' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $attribute = Attribute::create([
                'name' => $request->name,
            ]);

            foreach ($request->values ?? [] as $val) {
                if($val) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => $val,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('attributes.index')->with('success', 'Attribute created!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Store Error', ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create attribute.');
        }
    }

    public function edit($id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);
        return view('attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:attributes,name,'.$attribute->id,
            'values.*.id' => 'nullable|exists:attribute_values,id',
            'values.*.value' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $attribute->update(['name' => $request->name]);

            // Update existing or create new values
            foreach ($request->values ?? [] as $val) {
                if(isset($val['id']) && $val['id']) {
                    $av = AttributeValue::find($val['id']);
                    if($av && $val['value']) $av->update(['value' => $val['value']]);
                    elseif($av && !$val['value']) $av->delete();
                } elseif(isset($val['value']) && $val['value']) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => $val['value'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('attributes.index')->with('success', 'Attribute updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Update Error', ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update attribute.');
        }
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->values()->delete();
        $attribute->delete();
        return response()->json(['success' => true]);
    }
}