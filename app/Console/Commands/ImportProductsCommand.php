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
     */
    protected $signature = 'import:products {file}';

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
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File '$file' not found.");
            return;
        }

        $isTest = false;

        $results = $this->productImportService->import($file, $isTest);
        dd($results);
        return 'ss';
    }
}
