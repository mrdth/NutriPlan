<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('guest cannot view verification prompt', function () {
    $response = get(route('verification.notice'));

    $response->assertRedirect(route('login'));
});

test('verified user is redirected to dashboard', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = actingAs($user)
        ->get(route('verification.notice'));

    $response->assertRedirect(route('recipes.index'));
});

test('unverified user sees verification prompt', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $response = actingAs($user)
        ->get(route('verification.notice'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('auth/VerifyEmail')
            ->has('status')
    );
});
