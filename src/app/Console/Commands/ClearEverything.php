<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearEverything extends Command
{
    protected $signature = 'artisanize:clear-everything';

    protected $description = 'Clear all caches and recompile everything';

    public function handle()
    {
        $this->info('Clearing Application Cache...');
        Artisan::call('cache:clear');
        $this->info('Cleared Application Cache');

        $this->info('Clearing Route Cache...');
        Artisan::call('route:clear');
        $this->info('Cleared Route Cache');

        $this->info('Clearing Configuration Cache...');
        Artisan::call('config:clear');
        $this->info('Cleared Configuration Cache');

        $this->info('Clearing Compiled Views...');
        Artisan::call('view:clear');
        $this->info('Cleared Compiled Views');

        $this->info('Clearing Event Cache...');
        Artisan::call('event:clear');
        $this->info('Cleared Event Cache');

        $this->info('Clearing Compiled Classes...');
        Artisan::call('clear-compiled');
        $this->info('Cleared Compiled Classes');

        $this->info('Clearing Optimized Class Loader...');
        Artisan::call('optimize:clear');
        $this->info('Cleared Optimized Class Loader');

        $this->info('Restarting Queue Worker...');
        Artisan::call('queue:restart');
        $this->info('Restarted Queue Worker');

        $this->info('Recompiling Configuration Cache...');
        Artisan::call('config:cache');
        $this->info('Recompiled Configuration Cache');

        $this->info('Recompiling Route Cache...');
        Artisan::call('route:cache');
        $this->info('Recompiled Route Cache');

        $this->info('Recompiling Optimized Class Loader...');
        Artisan::call('optimize');
        $this->info('Recompiled Optimized Class Loader');
    }
}
