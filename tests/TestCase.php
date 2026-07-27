<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function signInAs(User $user): static
    {
        $this->actingAs($user);
        $this->withSession(['auth_version' => $user->auth_version]);

        return $this;
    }
}
