<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Input paths
    |--------------------------------------------------------------------------
    |
    | An array of target file system paths to be processed
    |
    */
    'input_paths' => [
        '/lang',
    ],

    /*
    |--------------------------------------------------------------------------
    | Output path
    |--------------------------------------------------------------------------
    |
    | Specifies the directory where the JSON translation files should be saved.
    |
    */
    'output_path' => app()->langPath(),

    /*
    |--------------------------------------------------------------------------
    | Override existing keys
    |--------------------------------------------------------------------------
    |
    | If a key already exists in the JSON translation files,
    | specifies whether to keep the ones in the JSON file (`false`)
    | or override with the value from the PHP file being processed (`true`).
    |
    */
    'override_existing_keys' => false,

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    |
    | An array (allowlist) of languages to be processed.
    | When not provided, all available languages will be processed.
    |
    */
    // 'languages' => ['en'],
];