<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\MeasurementUnit;
use JsonSerializable;

class Measurement implements JsonSerializable, \Stringable
{
    public function __construct(
        public readonly float $amount,
        public readonly ?MeasurementUnit $unit = null,
    ) {
    }

    public static function from(float $amount, ?string $unit = null): self
    {
        return new self(
            $amount,
            $unit !== null && $unit !== '' && $unit !== '0' ? MeasurementUnit::from($unit) : null
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'unit' => $this->unit?->value,
        ];
    }

    public function __toString(): string
    {
        if (!$this->unit instanceof \App\Enums\MeasurementUnit) {
            return (string) $this->amount;
        }

        return "{$this->amount} {$this->unit->value}";
    }

    public function format(): string
    {
        $amount = number_format($this->amount, 2);
        // Remove trailing zeros after decimal point
        $amount = rtrim(rtrim($amount, '0'), '.');

        if (!$this->unit instanceof \App\Enums\MeasurementUnit) {
            return $amount;
        }

        return "{$amount} {$this->unit->value}";
    }
}
