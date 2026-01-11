<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending    = 'pending';      // Order created, awaiting payment
    case Confirmed  = 'confirmed';    // Payment received, ready to process
    case Processing = 'processing';   // Being prepared/packed
    case Completed  = 'completed';    // Fully done
    case Cancelled  = 'cancelled';    // Cancelled
    case Refunded   = 'refunded';     // Money returned

    // ✅ Default status
    public static function default(): self
    {
        return self::Pending;
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Pending    => 'bg-warning text-dark',
            self::Confirmed  => 'bg-info text-white',
            self::Processing => 'bg-primary text-white',
            self::Completed  => 'bg-success text-white',
            self::Cancelled  => 'bg-danger text-white',
            self::Refunded   => 'bg-secondary text-white',
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

    // ✅ Helper to safely convert string to enum
    public static function fromValue(?string $value): self
    {
        return self::tryFrom($value) ?? self::default();
    }
}