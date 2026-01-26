<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BusinessController extends Controller
{
    /**
     * Display business settings page
     */
    public function index()
    {
        return view('business.index');
    }

    /**
     * Get business data for DataTables
     */
    public function data()
    {
        $business = Business::query();
        
        return DataTables::of($business)
            ->addColumn('logo', function ($row) {
                if ($row->logo_url) {
                    return '<img src="' . $row->logo_url . '" class="rounded shadow-sm" width="50" height="50" style="object-fit: contain;">';
                }
                return '<span class="badge bg-secondary">No Logo</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary open-business-settings" data-id="' . $row->id . '">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>';
            })
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }

    /**
     * Update business settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_name' => 'nullable|string|max:50',
            'tax_enabled' => 'nullable|boolean',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
            'footer_text' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $business = Business::first();

        if (!$business) {
            $business = new Business();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($business->logo && file_exists(public_path('uploads/business/' . $business->logo))) {
                unlink(public_path('uploads/business/' . $business->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Ensure directory exists
            $uploadPath = public_path('uploads/business');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            $business->logo = $filename;
        }

        // Update business fields
        $business->name = $request->name;
        $business->email = $request->email;
        $business->mobile = $request->mobile;
        $business->address = $request->address;
        $business->city = $request->city;
        $business->country = $request->country;
        $business->postal_code = $request->postal_code;
        $business->currency = $request->currency ?? 'USD';
        $business->currency_symbol = $request->currency_symbol ?? '$';
        $business->tax_rate = $request->tax_rate ?? 0;
        $business->tax_name = $request->tax_name ?? 'VAT';
        $business->tax_enabled = $request->has('tax_enabled') ? (bool) $request->tax_enabled : false;
        $business->timezone = $request->timezone ?? 'UTC';
        $business->date_format = $request->date_format ?? 'Y-m-d';
        $business->time_format = $request->time_format ?? 'H:i';
        $business->footer_text = $request->footer_text;
        $business->website = $request->website;
        $business->facebook = $request->facebook;
        $business->instagram = $request->instagram;
        $business->telegram = $request->telegram;

        $business->save();

        // Update session
        session([
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'logo' => $business->logo_url,
                'mobile' => $business->mobile,
                'email' => $business->email,
                'address' => $business->address,
                'city' => $business->city,
                'country' => $business->country,
                'currency' => $business->currency,
                'currency_symbol' => $business->currency_symbol,
                'tax_enabled' => $business->tax_enabled,
                'tax_rate' => $business->tax_rate,
                'tax_name' => $business->tax_name,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business settings updated successfully',
            'data' => $business
        ]);
    }

    /**
     * Remove business logo
     */
    public function removeLogo()
    {
        $business = Business::first();

        if ($business && $business->logo) {
            // Delete file
            $logoPath = public_path('uploads/business/' . $business->logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }

            $business->logo = null;
            $business->save();

            // Update session
            session(['business.logo' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo to remove'
        ], 400);
    }
}
