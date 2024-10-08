# Laravel JSON Lang

A package to export Laravel PHP translation files as JSON.

## How it works

This package adds a command that processes input files by grouping them according to their respective languages. For each language, a job is dispatched to enable parallel processing. This job handles the files by reading each translation key and updating it or not, in a JSON translation file. The JSON translation keys are structured to ensure that the translation helper function works the same way as it would if the translations were coming from PHP files.

## Installation

1. Require the package via Composer:

```
composer require guizoxxv/laravel-json-lang --dev
```

2. Publish the package's configuration file:

```
php artisan vendor:publish --provider="Guizoxxv\LaravelJsonLang\LaravelJsonLangServiceProvider"
```

3. Run the command to export files:

```
php artisan laravel-json-lang:export
```

## Configuration

The following configuration parameters are available:

### 1. input_paths

An array of target file system paths to be processed. Each element in the array should be a string representing either a file path or a directory path. The path should be absolute, starting from the root of the application.

Example:

```php
'input_paths' => [
    '/path/to/file.php',
    '/path/to/directory'
]
```
In this example, `/path/to/specific/file.php` is a single file that will be processed, while all `.php` files within `/path/to/directory/` and its subdirectories will also be processed.

**Vendor prefix**

When using package translations, a key/value pair with the path as key and package namespace as value to indicate the correct translation prefix.

Example:

```php
'input_paths' => [
    '/lang/en/validation.php' => 'myPackage',
]
```

```php
echo trans('myPackage::validation.required');
```

### 2. output_path

Specifies the directory where the JSON translation files should be saved.

### 3. override_existing_keys

If a key already exists in the JSON translation files, specifies whether to keep the ones in the JSON file (`false`) or override with the value from the PHP file being processed (`true`).

### 4. languages

An array (allowlist) of languages to be processed. When not provided, all available languages will be processed.

## Observations

1. This package reads the full content of translation files into memory. Should you encounter memory issues, try reducing the number of languages being processed at once.