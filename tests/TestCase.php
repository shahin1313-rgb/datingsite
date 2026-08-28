<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\TestingDatabaseGuard;

abstract class TestCase extends BaseTestCase
{
    /**
     * Check the selected database before Laravel starts transaction or
     * migration-related testing traits.
     *
     * @return array<class-string, int>
     */
    protected function setUpTraits()
    {
        TestingDatabaseGuard::assertSafe($this->app);

        return parent::setUpTraits();
    }
}
