<?php

namespace App\Console\Commands;

use App\Services\ProductImportService;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    * This line defines the signature of the console command.
    * - `import:products`: The name of the command
    * - `{file}`: Required argument representing the path to the CSV file containing products.
    * - `{--test}`: Optional flag (`--` indicates it's an option) named "test". Defaults to `false` if not provided.
    */
    protected $signature = 'import:products {file} {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from a CSV file';

    private $productImportService;
    public function __construct(ProductImportService $productImportService){
        parent::__construct();
        $this->productImportService = $productImportService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // This line retrieves the value of the required argument named "file" using the `argument` method.
        $file = $this->argument('file');

        // This line checks if the provided file path exists using `file_exists`.
        // If not found, an error message is displayed using `error` and the function exits using `return`.
        if (!file_exists($file)) {
            $this->error("File '$file' not found.");
            return;
        }

        // This line retrieves the value of the optional "test" flag using the `option` method.
        // The nullish coalescing operator (`??`) sets a default value of `false` if the option is not provided.
        $isTest = $this->option('test') ?? false;

        // This line calls the `import` method of the injected `productImportService` instance.
        $results = $this->productImportService->import($file, $isTest);

        $this->info("Processed: {$results['processed']}");
        $this->info("Successful: {$results['success']}");
        $this->info("Skipped: {$results['skipped']}");

        if (isset($results['failed'])) {
            $this->warn('Failed imports:');
            foreach ($results['failed'] as $failedItem) {
                $this->error(" - {$failedItem['message']}");
            }
        }
    }
}
