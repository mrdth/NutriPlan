<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('user can view reset password page', function () {
    $response = get(route('password.reset', ['token' => 'fake-token']));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('auth/ResetPassword')
            ->has('token')
            ->has('email')
    );
});

test('password can be reset with valid token', function () {
    Event::fake();

    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', trans('passwords.reset'));

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();

    Event::assertDispatched(PasswordReset::class);
});

test('reset password with invalid token fails', function () {
    $user = User::factory()->create();

    $response = post(route('password.store'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertSessionHasErrors('email');
});

test('reset password validates input', function () {
    $response = post(route('password.store'), [
        'token' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors(['token', 'email', 'password']);
});
