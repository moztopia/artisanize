<?php

return [
    'created_language_folder' => "Created language folder ':folder'.",
    'skipping_source_folder' => "Skipping folder ':folder' because it is the same as the source directory.",
    'no_valid_targets' => 'No valid targets found. Ensure your configuration includes supported languages or matches the allowed list.',
    'no_files_matched' => 'No files provided or matched in --files.',
    'processing_target' => "Processing target: :target",
    'translating_file' => "Translating file: :file.php to :target with additional parameters: :parameters",
    'source_file_not_found' => "Source language file ':file.php' not found in ':path'. Skipping.",
    'overwriting_file' => "Overwriting existing file: :file.php.",
    'file_exists' => "Language file ':file.php' already exists in ':target'. Skipping.",
    'translation_completed' => 'Translation process completed.',
    'saved_translated_file' => "Saved translated file to: :file",
    'error_writing_file' => "Error writing translated content to file: :file",
    'gemini_api_key_missing' => 'Gemini API key not configured. Aborting.',
    'gemini_request_failed' => 'Gemini API request failed with status code: :status',
    'gemini_response_body' => 'Response body: :body',
    'gemini_translation_error' => 'Gemini API response did not contain translated text. Response details:',
    'gemini_request_error' => 'Error during Gemini API request: :error',
];
