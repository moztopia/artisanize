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
        $this->output->newLine();
        $this->output->writeLn('artisanize:clear-everything');
        $this->output->newLine();
        $this->output->writeln($this->myinfo('Clearing Application Cache...'));
        Artisan::call('cache:clear');
        $this->output->writeln($this->myinfo('Clearing Route Cache...'));
        Artisan::call('route:clear');
        $this->output->writeln($this->myinfo('Clearing Configuration Cache...'));
        Artisan::call('config:clear');
        $this->output->writeln($this->myinfo('Clearing Compiled Views...'));
        Artisan::call('view:clear');
        $this->output->writeln($this->myinfo('Clearing Event Cache...'));
        Artisan::call('event:clear');
        $this->output->writeln($this->myinfo('Clearing Compiled Classes...'));
        Artisan::call('clear-compiled');
        $this->output->writeln($this->myinfo('Clearing Optimized Class Loader...'));
        Artisan::call('optimize:clear');
        $this->output->writeln($this->myinfo('Restarting Queue Worker...'));
        Artisan::call('queue:restart');
        $this->output->writeln($this->myinfo('Recompiling Configuration Cache...'));
        Artisan::call('config:cache');
        $this->output->writeln($this->myinfo('Recompiling Route Cache...'));
        Artisan::call('route:cache');
        $this->output->writeln($this->myinfo('Recompiling Optimized Class Loader...'));
        Artisan::call('optimize');
        $this->output->newLine();
    }

    private function myinfo($message, $prefix = '  ', $suffix = ' ')
    {
        return $prefix . '<bg=blue;fg=white> INFO </>' . $suffix . $message;
    }
}
