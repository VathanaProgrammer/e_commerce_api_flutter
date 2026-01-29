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
        $role = $user->role ?? 'customer'; 

        // Reset first to avoid duplicates
        Menu::reset();
        
        // --- DASHBOARD ---
        Menu::add('Dashboard', [
            'url' => route('home'),
            'icon' => '<i class="bi bi-grid-1x2"></i>',
        ]);

        Menu::add('Management', ['type' => 'label']);

        // --- PRODUCTS (Admin | Staff) ---
        if ($role === 'admin' || $role === 'staff') {
            Menu::add('Inventory', [
                'icon' => '<i class="bi bi-box-seam"></i>',
                'children' => [
                    [
                        'title' => 'Products',
                        'url' => route('products.index'),
                    ],
                    [
                        'title' => 'Add Product',
                        'url' => route('products.create'),
                    ],
                    [
                        'title' => 'Attributes',
                        'url' => route('attributes.index'),
                    ],
                    [
                        'title' => 'Categories',
                        'url' => route('categories.index'),
                    ]
                ]
            ]);
        }

        // --- SALES (Admin | Staff) ---
        if ($role === 'admin' || $role === 'staff') {
            Menu::add('Sales & Orders', [
                'icon' => '<i class="bi bi-cart3"></i>',
                'children' => [
                    [
                        'title' => 'All Orders',
                        'url' => route('sales.orders'),
                    ]
                ]
            ]);
        }

        // --- USERS (Admin Only) ---
        if ($role === 'admin') {
            Menu::add('Users', [
                'icon' => '<i class="bi bi-people"></i>',
                'children' => [
                    [
                        'title' => 'User List',
                        'url' => route('users.index'),
                    ],
                    [
                        'title' => 'Roles & Permissions',
                        'url' => route('roles.index'),
                    ]
                ]
            ]);

            Menu::add('Configuration', ['type' => 'label']);

            Menu::add('Business Settings', [
                'url' => route('business.settings'),
                'icon' => '<i class="bi bi-gear"></i>',
            ]);
        }

        return $next($request);
    }
}