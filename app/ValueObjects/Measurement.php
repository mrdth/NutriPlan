<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\MeasurementUnit;
use JsonSerializable;

class Measurement implements JsonSerializable
{
    public function __construct(
        public readonly float $amount,
        public readonly MeasurementUnit $unit,
    ) {
    }

    public static function from(float $amount, string $unit): self
    {
        return new self($amount, MeasurementUnit::from($unit));
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'unit' => $this->unit->value,
        ];
    }

    public function __toString(): string
    {
        return "{$this->amount} {$this->unit->value}";
    }

    public function format(): string
    {
        $amount = number_format($this->amount, 2);
        // Remove trailing zeros after decimal point
        $amount = rtrim(rtrim($amount, '0'), '.');

        return "{$amount} {$this->unit->value}";
    }
}
