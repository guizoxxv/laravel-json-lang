<?php

namespace Guizoxxv\LaravelJsonLang\Console;

use Guizoxxv\LaravelJsonLang\Jobs\ProcessLanguageFiles;
use Illuminate\Console\Command;

class ExportLanguageFilesAsJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-json-lang:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports target PHP translation files as JSON.';

    protected array $files_per_language;
   
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $output_path = config('laravel-json-lang.output_path');

        if (!$this->createOrValidatedOutputPath($output_path)) {
            $this->error("Insufficient permission on output path [{$output_path}]");

            return;
        }

        foreach (config('laravel-json-lang.input_paths') as $key => $value) {
            [$vendor_prefix, $path] = $this->getVendorPrefixAndPath($key, $value);

            $path = base_path($path);

            if (!file_exists($path)) {
                $this->warn("Path [{$path}] does not exists. Skipping.");
                continue;
            }

            if (is_dir($path)) {
                $files_list = $this->getFilesList($path);

                foreach ($files_list as $file_path) {
                    $this->addFileToLanguageGroup($file_path, $vendor_prefix);
                }
            } else {
                $this->addFileToLanguageGroup($path, $vendor_prefix);
            }
        }

        foreach ($this->files_per_language as $lang => $files) {
            $output_file_path = $this->getOutputFilePath($output_path, $lang);

            $this->info("Processing [$lang] language files.");

            ProcessLanguageFiles::dispatch($lang, $files, $output_file_path);
        }
    }

    private function createOrValidatedOutputPath(string $path): bool
    {
        if (file_exists($path)) {
            return is_writable($path);
        }

        return mkdir(
            directory: $path,
            recursive: true,
        );
    }

    private function getVendorPrefixAndPath(string|int $key, string $value): array
    {
        if (is_string($key)) {
            $vendor_prefix = $value;
            $path = $key;
        } else {
            $vendor_prefix = null;
            $path = $value;
        }

        return [$vendor_prefix, $path];
    }

    private function getFilesList(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
    
        foreach ($iterator as $file_info) {
            if ($file_info->isFile()) {
                if ($file_info->getExtension() === 'php') {
                    $files[] = $file_info->getPathname();
                }
            }
        }
    
        return $files;
    }

    private function addFileToLanguageGroup(string $path, ?string $vendor_prefix): void
    {
        $lang = basename(dirname($path));
        $languages = config('laravel-json-lang.languages');
    
        if (!isset($languages) || in_array($lang, $languages)) {
            if ($vendor_prefix) {
                $this->files_per_language[$lang][$path] = $vendor_prefix;
            } else {
                $this->files_per_language[$lang][] = $path;
            }
        }
    }

    private function getOutputFilePath(string $path, string $lang): string
    {
        return "{$path}/{$lang}.json";
    }
}
