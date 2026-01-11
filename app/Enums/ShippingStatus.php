<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case ordered   = 'ordered';
    case cancelled = 'cancelled';
    case delivered = 'delivered';
    case shipping  = 'shipping';

    // ✅ Default status
    public static function default(): self
    {
        return self::ordered;
    }

    public function colorClass(): string
    {
        return match($this) {
            self::cancelled => 'bg-warning',
            self::shipping  => 'bg-shipping',
            self::delivered => 'bg-completed',
            self::ordered   => 'bg-ordered',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badge(): string
    {
        // ✅ Use the enum itself, fallback to default if needed
        return sprintf(
            '<span class="px-2 py-1 rounded-[1px] text-white small fw-medium %s">%s</span>',
            $this->colorClass(),
            $this->label()
        );
    }

    // ✅ Easy helper for controller
    public static function fromValue(?string $value): self
    {
        return self::tryFrom($value) ?? self::default();
    }
}