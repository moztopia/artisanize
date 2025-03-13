<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

/**
 * Class LangTranslate
 * Handles the translation of language files using the Gemini API.
 */

class LangTranslateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */

    protected $signature = 'lang:translate 
                                {--targets=} 
                                {--files=} 
                                {--overwrite} 
                                {--source=} 
                                {--install}';

    /**
     * The description of the command.
     *
     * @var string
     */

    protected $description = 'Translate language file(s) using Gemini API.';

    /**
     * Handles the command execution.
     *
     * @return int
     */

    public function handle()
    {
        $langPath = base_path('lang');

        if (!File::exists($langPath)) {
            $this->warn("The '/lang' folder does not exist. Running the install process...");
            return $this->install();
        }

        $basePath = env('LANG_TRANSLATE_BASEPATH', 'lang');
        $sourceDir = $this->option('source') ?? env('LANG_TRANSLATE_SOURCE', '.source');
        $defaultTargets = env('LANG_TRANSLATE_TARGETS', '*');
        $defaultFiles = env('LANG_TRANSLATE_FILES', '*');
        $allowedLanguages = env('LANG_TRANSLATE_LANGUAGES', []);

        if (!is_array($allowedLanguages)) {
            $allowedLanguages = explode(',', $allowedLanguages);
        }

        $extraParameters = $this->getExtraParameters();

        $targetOption = $this->option('targets') ?? $defaultTargets;
        $filesOption = $this->option('files') ?? $defaultFiles;
        $overwrite = $this->option('overwrite') || in_array('overwrite', $extraParameters);

        $sourceLangPath = base_path("{$basePath}/{$sourceDir}");
        $targets = ($targetOption === '*') ? $allowedLanguages : explode(',', $targetOption);

        if (!empty($allowedLanguages)) {
            $targets = array_intersect($targets, $allowedLanguages);
        }

        if (in_array($sourceDir, $targets)) {
            $targets = array_diff($targets, [$sourceDir]);
            $this->warn(trans('artisanize.skipping_source_folder', ['folder' => $sourceDir]));
        }

        if (empty($targets)) {
            $this->error(trans('artisanize.no_valid_targets'));
            return 1;
        }

        $files = $this->resolveFiles($filesOption, $sourceLangPath);

        if (empty($files)) {
            $this->error(trans('artisanize.no_files_matched'));
            return 1;
        }

        foreach ($targets as $target) {
            $this->info(trans('artisanize.processing_target', ['target' => $target]));
            foreach ($files as $file) {
                $this->translateSpecificFile($target, $file, $sourceLangPath, $basePath, $overwrite, $extraParameters);
            }
        }

        $this->info(trans('artisanize.translation_completed'));
        return 0;
    }

    /**
     * Installs the necessary language directories and files for the translation process.
     *
     * This method checks if the 'lang' directory exists. If not, it creates the required 
     * '.source' directory inside 'lang' and copies the contents of the default 'en' 
     * language folder to it, ensuring the environment is set up for translation.
     *
     * @return int Returns 0 on successful installation, or 1 if an error occurs.
     */

    protected function install()
    {
        $langPath = base_path('lang');

        if (!File::exists($langPath)) {
            $this->warn("The '/lang' folder does not exist. Run 'php artisan lang:publish' to generate it.");
            return 1;
        }

        $sourcePath = base_path('lang/.source');
        $defaultLangPath = base_path('lang/en');

        if (!File::exists($defaultLangPath)) {
            $this->error("The default language folder 'lang/en' does not exist. Ensure 'php artisan lang:publish' has been run.");
            return 1;
        }

        if (!File::exists($sourcePath)) {
            File::makeDirectory($sourcePath, 0755, true);
            $this->info("Created '/lang/.source' folder.");
        }

        File::copyDirectory($defaultLangPath, $sourcePath);
        $this->info("Copied 'lang/en' to '/lang/.source'. Installation complete.");
        return 0;
    }

    /**
     * Parses and retrieves extra parameters from the environment variable LANG_TRANSLATE_PARAMETERS.
     *
     * @return array
     */

    protected function getExtraParameters()
    {
        $parameterString = env('LANG_TRANSLATE_PARAMETERS', '');
        $parameters = explode(',', $parameterString);
        $parsedParameters = [];

        if ($parameterString) {
            foreach ($parameters as $parameter) {
                if (strpos($parameter, '=') !== false) {
                    [$key, $value] = explode('=', $parameter, 2);
                    $parsedParameters[$key] = $value;
                } else {
                    $parsedParameters[$parameter] = true;
                }
            }
        }

        return $parsedParameters;
    }

    /**
     * Resolves files to be translated based on the provided patterns or explicit filenames.
     *
     * @param string $filesOption
     * @param string $sourceLangPath
     * @return array
     */
    protected function resolveFiles($filesOption, $sourceLangPath)
    {
        $patterns = explode(',', $filesOption);
        $resolvedFiles = [];

        foreach ($patterns as $pattern) {
            $regex = '/^' . str_replace(['*', '?'], ['.*', '.'], $pattern) . '$/';

            foreach (File::files($sourceLangPath) as $file) {
                $filename = $file->getFilenameWithoutExtension();

                if (preg_match($regex, $filename)) {
                    $resolvedFiles[] = $filename;
                }
            }
        }

        return array_unique($resolvedFiles);
    }

    /**
     * Translates a specific language file to a target language.
     *
     * @param string $target
     * @param string $file
     * @param string $sourceLangPath
     * @param string $basePath
     * @param bool $overwrite
     * @param array $extraParameters
     * @return void
     */
    protected function translateSpecificFile($target, $file, $sourceLangPath, $basePath, $overwrite, $extraParameters)
    {
        $targetLangPath = base_path("{$basePath}/{$target}");
        if (!File::exists($targetLangPath)) {
            File::makeDirectory($targetLangPath, 0755, true);
            $this->info(trans('artisanize.created_language_folder', ['folder' => $target]));
        }

        $sourceFilePath = $sourceLangPath . '/' . $file . '.php';
        $targetFilePath = $targetLangPath . '/' . $file . '.php';

        if (File::exists($targetFilePath)) {
            if ($overwrite) {
                File::delete($targetFilePath);
                $this->info(trans('artisanize.overwriting_file', ['file' => $file]));
            } else {
                $this->warn(trans('artisanize.file_exists', ['file' => $file, 'target' => $target]));
                return;
            }
        }

        if (!File::exists($sourceFilePath)) {
            $this->error(trans('artisanize.source_file_not_found', ['file' => $file, 'path' => $sourceLangPath]));
            return;
        }

        $this->info(trans('artisanize.translating_file', ['file' => $file, 'target' => $target, 'parameters' => json_encode($extraParameters)]));
        $this->performTranslation($sourceFilePath, $targetFilePath, $target);
    }

    /**
     * Performs the translation of a specific file using the Gemini API.
     *
     * @param string $sourceFilePath
     * @param string $targetFilePath
     * @param string $langCode
     * @return void
     */
    protected function performTranslation(string $sourceFilePath, string $targetFilePath, string $langCode)
    {
        $sourceFileContents = File::get($sourceFilePath);

        $apiKey = env('LANG_TRANSLATE_GEMINI_KEY');

        if (!$apiKey) {
            $this->error(trans('artisanize.gemini_api_key_missing'));
            return;
        }
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $prompt = "Translate the values in this PHP language array file to ISO-639 {$langCode} language. Do not translate the keys or PHP code. Only translate the string values. Return the result as a valid PHP array.\n\nFile contents:\n\n" . $sourceFileContents;

        $requestData = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($apiUrl, $requestData);

            if ($response->successful()) {
                $apiResponseData = $response->json();
                $translatedText = Arr::get($apiResponseData, 'candidates.0.content.parts.0.text');

                if (!$translatedText) {
                    $this->error(trans('artisanize.gemini_translation_error'));
                    $this->error(json_encode($apiResponseData, JSON_PRETTY_PRINT));
                    return;
                }

                $translatedText = trim($translatedText);

                if (str_starts_with($translatedText, '```php')) {
                    $translatedText = substr($translatedText, strlen('```php'));
                }
                if (str_ends_with($translatedText, '```')) {
                    $translatedText = substr($translatedText, 0, -strlen('```'));
                }

                $translatedText = trim($translatedText);

                $filePutResult = File::put($targetFilePath, $translatedText);

                if ($filePutResult !== false) {
                    $this->info(trans('artisanize.saved_translated_file', ['file' => $targetFilePath]));
                } else {
                    $this->error(trans('artisanize.error_writing_file', ['file' => $targetFilePath]));
                }
            } else {
                $this->error(trans('artisanize.gemini_request_failed', ['status' => $response->status()]));
                $this->error(trans('artisanize.gemini_response_body', ['body' => $response->body()]));
            }
        } catch (\Exception $e) {
            $this->error(trans('artisanize.gemini_request_error', ['error' => $e->getMessage()]));
        }
    }
}
