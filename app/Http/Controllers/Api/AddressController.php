<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            Address::where('user_id', Auth::id())
                ->update(['is_default' => false]);
        }

        $address = Address::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            Address::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $newDefault = Address::where('user_id', Auth::id())->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function setDefault($id)
    {
        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        Address::where('user_id', Auth::id())
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated',
            'data' => $address
        ]);
    }
}
