<?php

use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

function loggedUser(?User $user)
{
    $user = $user ?? User::factory();
    test()->actingAs($user);
}
