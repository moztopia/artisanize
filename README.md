Here's a suggested structure for your detailed README file:

---

# Artisanize Package

## Overview

This application is a development testing tool created to facilitate local Laravel development needs, and we are pleased to share it with the community.

### Why was this app created?

As developers, we often need efficient tools to streamline testing processes. The first tool in this suite is `lang:translate`. It serves to:

- Help generate multiple language files when testing the localization of a Laravel app.
- Provide a starting point for porting your tool or application into other languages.
- Supply human translators with a good foundation to work from in generated files.

## Installation

### Before you install ..

If you have a desire to use the `lang:translate` tool, you will NEED a Google AI Studio API key. At the time of this tool's creation, the process was very easy and could be completed in a couple of minutes. Specifically:

`1. Go -> https://aistudio.google.com`

`2. Click the (Get API key) link`

`3. Complete Google's Process`

Hang on to the key for after the installation and then open the .env file and put your key in the variable ... it will look something like this:

`LANG_TRANSLATE_GEMINI_KEY=your-(aistudio.google.com)-key,`

and you will make it look something like this:

`LANG_TRANSLATE_GEMINI_KEY=ljkh24jhkfwkhkjh23kjhkl34hj23k4jh,`

### Installing the Artisanize Tools

To install this package, use Composer to require the package and publish the necessary assets:

```bash
composer require moztopia/artisanize
composer vendor:publish --tag=artisanize
```

# !!! CRITICAL !!!

After installing and running the vendor:publish command there will be a new folder in your lang/ folder called `.source`. You should copy any file(s) that you want to be ai-translated (regardless of their language) into this folder. This is the source folder that the tool uses to generate language files in all of the ISO-639 folder names.

## Tools

### `lang:translate`

The `lang:translate` tool allows you to generate translated language files for testing and localization. It leverages the Gemini API for translations.

#### Command Signature:

```bash
php artisan lang:translate {--targets=} {--files=} {--overwrite} {--source=}
```

#### Options:

- `--targets=`: Specify the target languages (comma-separated).
- `--files=`: Define the language files to translate (comma-separated, supports wildcards like `*`).
- `--overwrite`: Overwrite existing target files (optional).
- `--source=`: Specify the source folder for the files (optional).

#### Key Features:

1. **Multi-language Generation**:

   - Supports translating language files to various ISO-639-compliant languages.
   - Aims to preserve translation consistency across files.

2. **File Matching**:

   - Allows the use of patterns (e.g., `*`, `?`) to match specific files for translation.

3. **Overwrite Support**:

   - Ensures existing files can be updated or left untouched, based on your requirements.

4. **Environment Variables**:

   - Customize behaviors (e.g., `LANG_TRANSLATE_BASEPATH`, `LANG_TRANSLATE_FILES`) through `.env` settings.

5. **Translation API**:
   - Uses the Gemini API for accurate and meaningful translations.

#### Example Usage:

Translate all files for French (`fr`) and German (`de`), including overwriting existing translations:

```bash
php artisan lang:translate --targets=fr,de --overwrite
```

## Contributing

We welcome contributions! Feel free to fork the repository, create a branch, and submit a pull request.
