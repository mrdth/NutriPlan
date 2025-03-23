<?php

use App\Http\Requests\Recipe\ImportRecipeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->user = createUser();
    Auth::login($this->user);
    $this->request = new ImportRecipeRequest();
});

it('authorizes authenticated users', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('does not authorize guests', function () {
    Auth::logout();
    expect($this->request->authorize())->toBeFalse();
});

it('validates recipe URLs', function () {
    $validator = validator(['url' => 'https://example.com/recipe'], $this->request->rules());
    expect($validator->passes())->toBeTrue();
});

it('requires a valid URL', function () {
    $validator = validator(['url' => 'not-a-url'], $this->request->rules());
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('url'))
        ->toBe('The url field must be a valid URL.');
});

it('requires the URL field', function () {
    $validator = validator([], $this->request->rules());
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('url'))
        ->toBe('The url field is required.');
});
