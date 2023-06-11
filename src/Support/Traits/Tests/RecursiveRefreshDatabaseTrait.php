<?php

namespace Inium\Laraboard\Support\Traits\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait RecursiveRefreshDatabaseTrait
{
    use RefreshDatabase;

    /**
     * Refresh the in-memory database.
     *
     * @return void
     */
    protected function refreshInMemoryDatabase()
    {
        $this->artisan("migrate");

        // 'database/migrations/sub-folder’ would probably be ‘database/migrations/old’ in the case of the OP
        $this->artisan("migrate", [
            "--path" => "database/migrations/laraboard",
        ]);

        $this->app[Kernel::class]->setArtisan(null);
    }
}
