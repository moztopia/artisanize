<?php

namespace Moztopia\Artisanize;

use Illuminate\Support\ServiceProvider;

class ArtisanizeServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!is_dir(base_path('lang'))) {
                \Artisan::call('lang:publish', ['--force' => true]);
            }
        }

        $this->publishes([
            base_path('lang/en') => base_path('/lang/.source'),
        ], 'artisanize');

        $this->publishes([
            __DIR__ . '/app/Console/Commands/LangTranslateCommand.php' => base_path('app/Console/Commands/LangTranslateCommand.php'),
            __DIR__ . '/lang/' => base_path('lang/'),
        ], 'artisanize');

        $this->updateEnvFile();

        if ($this->app->runningInConsole()) {
            \Artisan::call('vendor:publish', [
                '--tag' => 'artisanize',
                '--force' => true,
            ]);
        }
    }

    protected function updateEnvFile()
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        $envKeys = [
            '',
            '# moztopia/artisanize - lang:translate',
            '# LANG_TRANSLATE_BASEPATH=',
            '# LANG_TRANSLATE_SOURCE=',
            '# LANG_TRANSLATE_TARGETS=',
            '# LANG_TRANSLATE_FILES=',
            '# LANG_TRANSLATE_PARAMETERS=',
            'LANG_TRANSLATE_LANGUAGES=en,zh,hi,es,fr,th,de,ar',
            'LANG_TRANSLATE_GEMINI_KEY=your-(aistudio.google.com)-key',
        ];

        // Add to .env
        if (file_exists($envPath)) {
            foreach ($envKeys as $envKey) {
                [$key] = explode('=', $envKey, 2);
                if (strpos(file_get_contents($envPath), $key) === false) {
                    file_put_contents($envPath, PHP_EOL . $envKey, FILE_APPEND);
                }
            }
        }

        // Add to .env.example
        if (file_exists($envExamplePath)) {
            foreach ($envKeys as $envKey) {
                [$key] = explode('=', $envKey, 2);
                if (strpos(file_get_contents($envExamplePath), $key) === false) {
                    file_put_contents($envExamplePath, PHP_EOL . $envKey, FILE_APPEND);
                }
            }
        }
    }
}
