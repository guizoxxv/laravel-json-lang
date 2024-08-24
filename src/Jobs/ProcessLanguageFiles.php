<?php

namespace Guizoxxv\LaravelJsonLang\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLanguageFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $lang,
        private array $files,
        private string $output_file_path
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->files as $key => $value) {
            [$vendor_prefix, $path] = $this->getVendorPrefixAndPath($key, $value);

            $this->processFile($vendor_prefix, $path);
        }
    }

    private function getVendorPrefixAndPath(string|int $key, string $value): array
    {
        if (is_string($key)) {
            $vendor_prefix = $value;
            $path = $key;
        } else {
            $vendor_prefix = '';
            $path = $value;
        }

        return [$vendor_prefix, $path];
    }

    private function processFile(
        ?string $vendor_prefix,
        string $input_file_path
    ): void
    {
        try {
            $input_translations = require $input_file_path;

            if (!is_array($input_translations)) {
                throw new \Exception("Expected array in [{$input_file_path}]");
            }
    
            $input_translations = $this->flattenInputArray($input_translations);
            
            $prefix = $this->getPrefix($vendor_prefix, $input_file_path);
    
            $output_translations = $this->getTranslations();
            $output_translations = $this->updateTranslations(
                $input_translations,
                $output_translations,
                $prefix
            );
    
            $this->generateOutputTranslationsJson($output_translations);
        } catch (\Throwable $th) {
            Log::warn(
                "Failed to process file [{$input_file_path}]: {$th->getMessage()}"
            );
        }
    }

    private function flattenInputArray(array $data, string $prefix = ''): array
    {
        $output = [];

        foreach ($data as $key => $value) {
            $new_key = $prefix . $key;
    
            if (is_array($value)) {
                foreach ($this->flattenInputArray($value, "{$new_key}.") as $sub_key => $sub_value) {
                    $output[$sub_key] = $sub_value;
                }
            } else {
                $output[$new_key] = $value;
            }
        }
    
        return $output;
    }

    private function getPrefix(?string $vendor_prefix, string $path): string
    {
        $file_name = pathinfo($path, PATHINFO_FILENAME);

        return $vendor_prefix
            ? "{$vendor_prefix}::{$file_name}"
            : $file_name;
    }

    private function getTranslations(): array
    {
        if (file_exists($this->output_file_path)) {
            $contents = file_get_contents($this->output_file_path);

            if ($contents) {
                return json_decode($contents, true);
            }
        }

        return [];
    }

    private function updateTranslations(
        array $input_translations,
        array $output_translations,
        string $prefix
    ): array
    {
        if (config('laravel-json-lang.override_existing_keys')) {
            foreach ($input_translations as $key => $value) {
                $output_translations["{$prefix}.{$key}"] = $value;
            }
        } else {
            foreach ($input_translations as $key => $value) {
                $key = "{$prefix}.{$key}";

                if (!isset($output_translations[$key])) {
                    $output_translations[$key] = $value;
                }
            }
        }

        return $output_translations;
    }

    private function generateOutputTranslationsJson(array $data): void
    {
        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($this->output_file_path, $json_data);
    }
}
