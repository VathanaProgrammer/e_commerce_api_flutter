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
        $html = '<ul class="nav flex-column">';

        foreach (static::$items as $item) {
            if ($item['role'] && $userRole !== $item['role']) continue;

            $hasChildren = !empty($item['children']);
            $collapseId = 'menu' . md5($item['title']);
            $isActive = ($item['url'] === $currentUrl);

            // Parent link: use inline style if color is set
            $parentAttr = '';
            if (!empty($item['color'])) {
                $parentAttr = 'style="color:' . $item['color'] . '"';
            } elseif (static::$linkClassCallback) {
                $parentAttr = 'class="' . call_user_func(static::$linkClassCallback, $item, $isActive, $userRole) . '"';
            } else {
                $parentAttr = 'class="nav-link' . ($isActive ? ' active' : '') . '"';
            }

            $html .= '<li class="nav-item">';

            if ($hasChildren) {
                $childActive = false;
                foreach ($item['children'] as $child) {
                    if (isset($child['role']) && $child['role'] && $userRole !== $child['role']) continue;
                    if (isset($child['url']) && $child['url'] === $currentUrl) $childActive = true;
                }

                $html .= '<a ' . $parentAttr . ' class="d-flex justify-content-between align-items-center nav-link" data-bs-toggle="collapse" href="#' . $collapseId . '">';
                $html .= '<span>' . ($item['icon'] ?? '') . ' ' . $item['title'] . '</span>';
                $html .= '<i class="bi bi-chevron-down"></i>';
                $html .= '</a>';

                $html .= '<ul class="collapse nav flex-column ms-3 ' . ($childActive ? 'show' : '') . '" id="' . $collapseId . '">';
                foreach ($item['children'] as $child) {
                    if (isset($child['role']) && $child['role'] && $userRole !== $child['role']) continue;

                    $childStyle = '';
                    if (!empty($child['color'])) {
                        $childStyle = 'style="color:' . $child['color'] . '"';
                        $childClass = 'nav-link'; // do not add conflicting text-white-50
                    } else {
                        $childClass = 'nav-link text-white-50';
                    }

                    $childActiveClass = (isset($child['url']) && $child['url'] === $currentUrl) ? ' active' : '';

                    $html .= '<li class="nav-item">';
                    $html .= '<a href="' . ($child['url'] ?? '#') . '" class="' . $childClass . $childActiveClass . '" ' . $childStyle . '>';
                    $html .= ($child['icon'] ?? '') . ' ' . $child['title'];
                    $html .= '</a></li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<a href="' . $item['url'] . '" ' . $parentAttr . '>';
                $html .= ($item['icon'] ?? '') . ' ' . $item['title'];
                $html .= '</a>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
        return $html;
    }

    public static function reset()
    {
        static::$items = [];
    }
}