<?php

namespace Moztopia\Artisanize;

use Illuminate\Support\ServiceProvider;

class ArtisanizeServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register services, bindings, or configuration here if needed
    }

    public function boot()
    {
        // Publish files from the package to the Laravel app structure
        $this->publishes([
            __DIR__ . '/Commands/LangTranslateCommand.php' => base_path('app/Console/Commands/LangTranslateCommand.php'),
        ], 'commands');
    }
}
