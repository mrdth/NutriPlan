<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('guest cannot request verification email', function () {
    $response = post(route('verification.send'));

    $response->assertRedirect(route('login'));
});

test('verified user is redirected to dashboard', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = actingAs($user)
        ->post(route('verification.send'));

    $response->assertRedirect(route('recipes.index'));
});

test('unverified user receives verification email', function () {
    Notification::fake();

    $user = User::factory()->create(['email_verified_at' => null]);

    $response = actingAs($user)
        ->post(route('verification.send'));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
});
