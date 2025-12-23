<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Menu;

class AdminSidebarMenu
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $role = $user->role ?? 'customer'; // get the user's role

        // Reset first to avoid duplicates on every request
        Menu::reset();

        $gray900 = '#1a1a1a'; // gray-900 color

        // Add menu items
        Menu::add('Home', [
            'url' => route('home'),
            'icon' => '<i class="bi bi-house"></i>',
            'color' => $gray900,
        ]);

        Menu::add('Products', [
            'icon' => '<i class="bi bi-box-seam"></i>',
            'color' => $gray900,
            'children' => [
                [
                    'title' => 'Product Lists',
                    'url' => route('products.index'),
                    'icon' => '<i class="bi bi-list-ul"></i>',
                    'color' => $gray900,
                ],
                [
                    'title' => 'Add Product',
                    'url' => route('products.create'),
                    'icon' => '<i class="bi bi-plus-square"></i>',
                    'color' => $gray900,
                ]
            ]
        ]);

        if ($role !== 'admin') {
            Menu::add('User Management', [
                'icon' => '<i class="bi bi-people"></i>',
                'color' => $gray900,
                'children' => [
                    [
                        'title' => 'Users',
                        'url' => route('users.index'),
                        'icon' => '<i class="bi bi-person"></i>',
                        'color' => $gray900,
                    ],
                    [
                        'title' => 'Roles',
                        'url' => route('roles.index'),
                        'icon' => '<i class="bi bi-shield-lock"></i>',
                        'color' => $gray900,
                    ]
                ]
            ]);
        }

        return $next($request);
    }
}