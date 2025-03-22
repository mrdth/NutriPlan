<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\Enums\MeasurementUnit;
use App\ValueObjects\Measurement;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeasurementTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_amount_and_unit_string(): void
    {
        $measurement = Measurement::from(2.5, 'cup');

        $this->assertEquals(2.5, $measurement->amount);
        $this->assertEquals(MeasurementUnit::CUP, $measurement->unit);
    }

    #[Test]
    public function it_can_be_converted_to_json(): void
    {
        $measurement = new Measurement(2.5, MeasurementUnit::CUP);

        $this->assertEquals([
            'amount' => 2.5,
            'unit' => 'cup',
        ], $measurement->jsonSerialize());
    }

    #[Test]
    public function it_can_be_converted_to_string(): void
    {
        $measurement = new Measurement(2.5, MeasurementUnit::CUP);

        $this->assertEquals('2.5 cup', (string) $measurement);
    }

    #[Test]
    public function it_formats_measurements_nicely(): void
    {
        $measurements = [
            new Measurement(2.5, MeasurementUnit::CUP),
            new Measurement(2.0, MeasurementUnit::GRAM),
            new Measurement(2.50, MeasurementUnit::MILLILITER),
        ];

        $this->assertEquals('2.5 cup', $measurements[0]->format());
        $this->assertEquals('2 g', $measurements[1]->format());
        $this->assertEquals('2.5 ml', $measurements[2]->format());
    }
}
