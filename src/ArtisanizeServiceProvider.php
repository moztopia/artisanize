<?php

namespace Moztopia\Artisanize;

use Illuminate\Support\ServiceProvider;

class ArtisanizeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Commands/LangTranslateCommand.php' => base_path('app/Console/Commands/LangTranslateCommand.php'),
            __DIR__ . '/resources/lang/' => resource_path('lang/vendor/artisanize'),
        ], 'artisanize');

        $this->updateEnvFile();
    }

    protected function updateEnvFile()
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        $envKeys = [
            '# LANG_TRANSLATE_BASEPATH=',
            '# LANG_TRANSLATE_SOURCE=',
            '# LANG_TRANSLATE_TARGETS=',
            '# LANG_TRANSLATE_FILES=',
            '# LANG_TRANSLATE_PARAMETERS=',
            'LANG_TRANSLATE_LANGUAGES=en,th,de,es',
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
