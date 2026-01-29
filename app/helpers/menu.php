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
        
        // Sidebar Wrapper
        $html = '<aside class="w-full h-full bg-white shadow-xl rounded-r-2xl py-6 sidebar-animate overflow-y-auto border-r border-slate-100 font-sans">';
        $html .= '<nav class="px-3 space-y-2">';

        foreach (static::$items as $item) {
            // Role Check
            if (isset($item['role']) && $item['role']) {
                if ($userRole === 'admin') {
                   // Admin passes
                } elseif ($userRole !== $item['role']) {
                    // Skip if role doesn't match and not admin (simplified logic, ideally handled by middleware)
                     // If strict role check is needed, uncomment next line:
                   // continue;
                }
            }

            // Handle Section Labels
            if (isset($item['type']) && $item['type'] === 'label') {
                $html .= '<div class="px-4 mt-8 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none">' . $item['title'] . '</div>';
                continue;
            }

            $hasChildren = !empty($item['children']);
            $isActive = ($item['url'] === $currentUrl);

            // Icon processing (SidebarMenu middleware passes full <i> tag, let's extract or use as is)
            // The Blade design used specific classes on icons. 
            // Let's strip the class from the passed icon str or expect the middleware to update.
            // For now, usage: $item['icon'] is '<i class="bi bi-house"></i>'.
            
            // Extract class from icon string for advanced styling or just use regex
            preg_match('/class="([^"]+)"/', $item['icon'], $matches);
            $iconClass = $matches[1] ?? 'bi bi-circle';

            if ($hasChildren) {
                // Check if any child is active
                $childActive = false;
                foreach ($item['children'] as $child) {
                    if (trim($child['url']) === $currentUrl) $childActive = true;
                    // Also check for wildcard matches if needed, but strict url is safer for now
                    // Or check if current URL starts with..
                }

                // Determine styling based on active state
                // Using the Color Palette from the "White Theme" step
                // Blue, Indigo, Emerald, Purple based on valid sections? 
                // We'll use a generic "Indigo" style for dropdowns to keep it simple, or map titles to colors.
                
                $themeColor = 'indigo'; 
                if (stripos($item['title'], 'Sales') !== false) $themeColor = 'emerald';
                if (stripos($item['title'], 'User') !== false) $themeColor = 'purple';
                if (stripos($item['title'], 'Business') !== false) $themeColor = 'amber';

                $activeBtnClass = "bg-{$themeColor}-50 text-{$themeColor}-700 font-semibold shadow-sm ring-1 ring-{$themeColor}-100";
                $inactiveBtnClass = "text-slate-600 hover:bg-slate-50 hover:text-slate-900";
                
                $btnClass = $childActive ? $activeBtnClass : $inactiveBtnClass;
                $iconColorClass = $childActive ? "text-{$themeColor}-600" : "text-slate-400 group-hover:text-{$themeColor}-500";
                $chevronClass = $childActive ? "rotate-180 text-{$themeColor}-500" : "text-slate-400";
                
                $isOpen = $childActive ? 'true' : 'false';

                $html .= '<div x-data="{ open: ' . $isOpen . ' }">';
                $html .= '<button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group ' . $btnClass . '">';
                $html .= '<div class="flex items-center gap-3">';
                $html .= '<i class="' . $iconClass . ' text-xl transition-transform group-hover:scale-110 ' . $iconColorClass . '"></i>';
                $html .= '<span class="tracking-wide text-sm">' . $item['title'] . '</span>';
                $html .= '</div>';
                $html .= '<i class="bi bi-chevron-down text-[10px] transition-transform duration-300 ' . $chevronClass . '" :class="open ? \'rotate-180\' : \'\'"></i>';
                $html .= '</button>';

                $html .= '<div x-show="open" x-collapse class="mt-1 space-y-1 pl-4 ml-6 border-l-2 border-slate-100">';
                
                foreach ($item['children'] as $child) {
                    $isChildActive = ($child['url'] === $currentUrl);
                    
                    $activeLinkClass = "text-{$themeColor}-600 font-medium bg-{$themeColor}-50/50";
                    $inactiveLinkClass = "text-slate-500 hover:text-slate-800 hover:bg-slate-50";
                    $linkClass = $isChildActive ? $activeLinkClass : $inactiveLinkClass;

                    $html .= '<a href="' . $child['url'] . '" class="block px-4 py-2 text-sm rounded-lg transition-colors relative ' . $linkClass . '">';
                    $html .= $child['title'];
                    $html .= '</a>';
                }

                $html .= '</div></div>';

            } else {
                // Single Link
                $activeClass = 'bg-blue-50 text-blue-600 font-semibold shadow-sm ring-1 ring-blue-100';
                $inactiveClass = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
                $finalClass = $isActive ? $activeClass : $inactiveClass;
                
                $iconStyle = $isActive ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500';

                $html .= '<a href="' . $item['url'] . '" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out ' . $finalClass . '">';
                $html .= '<i class="' . $iconClass . ' text-xl transition-transform group-hover:scale-110 ' . $iconStyle . '"></i>';
                $html .= '<span class="tracking-wide text-sm">' . $item['title'] . '</span>';
                
                if ($isActive) {
                    $html .= '<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-500 shadow-sm"></span>';
                }
                
                $html .= '</a>';
            }
        }

        $html .= '</nav></aside>';
        return $html;
    }

    public static function reset()
    {
        static::$items = [];
    }
}