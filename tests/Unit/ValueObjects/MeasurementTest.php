<?php

declare(strict_types=1);

use App\Enums\MeasurementUnit;
use App\ValueObjects\Measurement;

test('it can be created from amount and unit string', function () {
    $measurement = Measurement::from(2.5, 'cup');

    expect($measurement->amount)->toBe(2.5);
    expect($measurement->unit)->toBe(MeasurementUnit::CUP);
});

test('it can be converted to json', function () {
    $measurement = new Measurement(2.5, MeasurementUnit::CUP);

    expect($measurement->jsonSerialize())->toBe([
        'amount' => 2.5,
        'unit' => 'cup',
    ]);
});

test('it can be converted to string', function () {
    $measurement = new Measurement(2.5, MeasurementUnit::CUP);

    expect((string) $measurement)->toBe('2.5 cup');
});

test('it formats measurements nicely', function () {
    $measurements = [
        new Measurement(2.5, MeasurementUnit::CUP),
        new Measurement(2.0, MeasurementUnit::GRAM),
        new Measurement(2.50, MeasurementUnit::MILLILITER),
    ];

    expect($measurements[0]->format())->toBe('2.5 cup');
    expect($measurements[1]->format())->toBe('2 g');
    expect($measurements[2]->format())->toBe('2.5 ml');
});
