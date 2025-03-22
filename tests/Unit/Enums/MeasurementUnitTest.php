<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\MeasurementUnit;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeasurementUnitTest extends TestCase
{
    #[Test]
    public function it_provides_human_readable_labels(): void
    {
        $this->assertEquals('Gram', MeasurementUnit::GRAM->label());
        $this->assertEquals('Milliliter', MeasurementUnit::MILLILITER->label());
        $this->assertEquals('Cup', MeasurementUnit::CUP->label());
    }

    #[Test]
    public function it_identifies_volume_units(): void
    {
        $this->assertTrue(MeasurementUnit::MILLILITER->isVolume());
        $this->assertTrue(MeasurementUnit::LITER->isVolume());
        $this->assertTrue(MeasurementUnit::TEASPOON->isVolume());
        $this->assertTrue(MeasurementUnit::TABLESPOON->isVolume());
        $this->assertTrue(MeasurementUnit::CUP->isVolume());

        $this->assertFalse(MeasurementUnit::GRAM->isVolume());
        $this->assertFalse(MeasurementUnit::PIECE->isVolume());
    }

    #[Test]
    public function it_identifies_weight_units(): void
    {
        $this->assertTrue(MeasurementUnit::GRAM->isWeight());
        $this->assertTrue(MeasurementUnit::KILOGRAM->isWeight());

        $this->assertFalse(MeasurementUnit::MILLILITER->isWeight());
        $this->assertFalse(MeasurementUnit::PIECE->isWeight());
    }

    #[Test]
    public function it_identifies_unit_measurements(): void
    {
        $this->assertTrue(MeasurementUnit::PIECE->isUnit());
        $this->assertTrue(MeasurementUnit::PINCH->isUnit());

        $this->assertFalse(MeasurementUnit::GRAM->isUnit());
        $this->assertFalse(MeasurementUnit::MILLILITER->isUnit());
    }
}
