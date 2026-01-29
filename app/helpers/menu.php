<?php

namespace App\Helpers;

class Menu
{
    protected static $items = [];
    protected static $linkClassCallback = null;

    public static function add($title, $options = [])
    {
        $item = [
            'title'    => $title,
            'url'      => $options['url'] ?? '#',
            'icon'     => $options['icon'] ?? '',
            'role'     => $options['role'] ?? null,
            'children' => $options['children'] ?? [],
            'color'    => $options['color'] ?? '', // optional color
            'type'     => $options['type'] ?? 'link', // 'link' or 'label'
        ]; 
        
        static::$items[] = $item;
        return $item;
    }

    public static function linkClass($callback)
    {
        static::$linkClassCallback = $callback;
    }

    public static function render($userRole = null)
    {
        $currentUrl = url()->current();
        $html = '';

        foreach (static::$items as $item) {
            // Role Check (Ideally handled in middleware, but keeping as fallback)
            if (isset($item['role']) && $item['role'] && $userRole !== 'admin' && $userRole !== $item['role']) {
                continue;
            }

            // Handle Section Labels
            if (isset($item['type']) && $item['type'] === 'label') {
                $html .= '<div class="px-4 mt-8 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none">' . $item['title'] . '</div>';
                continue;
            }

            $hasChildren = !empty($item['children']);
            $isActive = (trim($item['url'], '/') === trim($currentUrl, '/'));

            // Extract icon class
            preg_match('/class="([^"]+)"/', $item['icon'], $matches);
            $iconClass = $matches[1] ?? 'bi bi-circle';

            if ($hasChildren) {
                // Check if any child is active
                $childActive = false;
                foreach ($item['children'] as $child) {
                    if (trim($child['url'], '/') === trim($currentUrl, '/')) {
                        $childActive = true;
                        break;
                    }
                }

                // Theme color mapping
                $themeColor = 'indigo'; 
                if (stripos($item['title'], 'Sales') !== false) $themeColor = 'emerald';
                elseif (stripos($item['title'], 'User') !== false) $themeColor = 'purple';
                elseif (stripos($item['title'], 'Setting') !== false || stripos($item['title'], 'Business') !== false) $themeColor = 'amber';
                elseif (stripos($item['title'], 'Inventory') !== false || stripos($item['title'], 'Product') !== false) $themeColor = 'indigo';

                $isOpen = $childActive ? 'true' : 'false';

                $html .= '<div x-data="{ open: ' . $isOpen . ' }">';
                
                // Button
                $html .= '<button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group "
                    :class="open ? \'bg-' . $themeColor . '-50 text-' . $themeColor . '-700 font-semibold shadow-sm ring-1 ring-' . $themeColor . '-100\' : \'text-slate-600 hover:bg-slate-50 hover:text-slate-900\'">';
                
                $html .= '<div class="flex items-center gap-3">';
                $html .= '<i class="' . $iconClass . ' text-xl transition-transform group-hover:scale-110" 
                             :class="open ? \'text-' . $themeColor . '-600\' : \'text-slate-400 group-hover:text-' . $themeColor . '-500\'"></i>';
                $html .= '<span class="tracking-wide text-sm">' . $item['title'] . '</span>';
                $html .= '</div>';
                
                $html .= '<i class="bi bi-chevron-down text-[10px] transition-transform duration-300" 
                             :class="open ? \'rotate-180 text-' . $themeColor . '-500\' : \'text-slate-400\'"></i>';
                $html .= '</button>';

                // Submenu
                $html .= '<div x-show="open" x-collapse x-cloak class="mt-1 space-y-1 pl-4 ml-6 border-l-2 border-slate-100">';
                
                foreach ($item['children'] as $child) {
                    $isChildActive = (trim($child['url'], '/') === trim($currentUrl, '/'));
                    
                    $linkClass = $isChildActive 
                        ? "text-{$themeColor}-600 font-medium bg-{$themeColor}-50/50" 
                        : "text-slate-500 hover:text-slate-800 hover:bg-slate-50";

                    $html .= '<a href="' . $child['url'] . '" class="block px-4 py-2 text-sm rounded-lg transition-colors relative ' . $linkClass . '">';
                    $html .= $child['title'];
                    $html .= '</a>';
                }

                $html .= '</div></div>';

            } else {
                // Single Link
                $html .= '<a href="' . $item['url'] . '" 
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out ' . 
                    ($isActive ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900') . '">';
                
                $html .= '<i class="' . $iconClass . ' text-xl transition-transform group-hover:scale-110 ' . 
                    ($isActive ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500') . '"></i>';
                
                $html .= '<span class="tracking-wide text-sm">' . $item['title'] . '</span>';
                
                if ($isActive) {
                    $html .= '<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-500 shadow-sm"></span>';
                }
                
                $html .= '</a>';
            }
        }

        return $html;
    }

    public static function reset()
    {
        static::$items = [];
    }
}