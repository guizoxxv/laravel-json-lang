<?php

use Illuminate\Console\Command;

class ExportLanguageAsJsonCommand extends Command
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
    protected $description = 'Export target language files as JSON.';
 
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
    }
}