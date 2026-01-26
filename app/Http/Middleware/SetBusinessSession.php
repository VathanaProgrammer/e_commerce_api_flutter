<?php
namespace App\Http\Middleware;

use App\Models\Business;
use Closure;
use Illuminate\Http\Request;

class SetBusinessSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('business')) {
            $business = Business::first();

            if ($business) {
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
                        'currency' => $business->currency ?? 'USD',
                        'currency_symbol' => $business->currency_symbol ?? '$',
                        'tax_enabled' => $business->tax_enabled ?? false,
                        'tax_rate' => $business->tax_rate ?? 0,
                        'tax_name' => $business->tax_name ?? 'VAT',
                    ],
                ]);
            }
        }

        return $next($request);
    }
}