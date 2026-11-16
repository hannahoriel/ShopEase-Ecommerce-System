<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        // Keep Blade's generated PHP files out of the synced project tree and
        // isolate parallel PHPUnit processes from one another. On Windows,
        // OneDrive or another PHP process can hold a shared compiled view open
        // while Laravel atomically replaces it, causing intermittent rename
        // access-denied failures.
        $compiledViews = sys_get_temp_dir().DIRECTORY_SEPARATOR.'shopease-test-views-'.getmypid();
        putenv('VIEW_COMPILED_PATH='.$compiledViews);
        $_ENV['VIEW_COMPILED_PATH'] = $compiledViews;
        $_SERVER['VIEW_COMPILED_PATH'] = $compiledViews;

        return parent::createApplication();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    protected function primaryAdmin(): User
    {
        return User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('id')
            ->firstOrFail();
    }
}
