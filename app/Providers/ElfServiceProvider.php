<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Core\Services\PersonService;
use App\Modules\Core\Services\OrganizationService;
use App\Modules\Shared\Workflow\WorkflowEngine;
use App\Modules\Shared\MasterData\MasterDataService;
use App\Modules\Shared\InternalTx\InternalTransactionService;

class ElfServiceProvider extends ServiceProvider
{
    /**
     * Register all ELF module services.
     */
    public function register(): void
    {
        // Core Domain Services
        $this->app->singleton(PersonService::class);
        $this->app->singleton(OrganizationService::class);

        // Shared Engine Services
        $this->app->singleton(WorkflowEngine::class);
        $this->app->singleton(MasterDataService::class);
        $this->app->singleton(InternalTransactionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
