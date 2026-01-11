<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending   = 'pending';  // ✅ Fixed typo from "padding"
    case Completed = 'completed'; // ✅ Changed from "paid" to match migration
    case Failed    = 'failed';

    // ✅ Default status
    public static function default(): self
    {
        return self::Pending;
    }

    public function colorClass(): string
    {
        return match($this) {
            self::Failed    => 'bg-danger text-white',
            self::Completed => 'bg-success text-white',
            self::Pending   => 'bg-warning text-dark',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badge(): string
    {
        return sprintf(
            '<span class="px-2 py-1 rounded-[1px] small fw-medium %s">%s</span>',
            $this->colorClass(),
            $this->label()
        );
    }

    // ✅ Helper to safely convert string to enum or use default
    public static function fromValue(?string $value): self
    {
        return self::tryFrom($value) ?? self::default();
    }
}