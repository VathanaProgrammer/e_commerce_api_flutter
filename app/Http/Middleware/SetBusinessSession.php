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
                        'id'   => $business->id,
                        'name' => $business->name,
                    ],
                ]);
            }
        }

        return $next($request);
    }
}