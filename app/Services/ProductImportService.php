<?php

namespace App\Services;

use SplFileObject;
use App\Models\Product;
use Carbon\Carbon;

class ProductImportService
{
    public function import(string $filePath, bool $isTest = false)
    {
        $processed = 0;
        $success = 0;
        $skipped = 0;
        $failed = [];

        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $header = $file->fgetcsv(); // Read header row

        if (count($header) === 0) {
            // Handle empty header row (error or unexpected data)
            return [
                'processed' => $processed,
                'success' => $success,
                'skipped' => $skipped,
                'failed' => ['message' => 'Empty header row in CSV file'],
            ];
        }

        foreach ($file as $key => $row) {
            if($key !==0){
                if (empty($row)) {
                    break;
                }
                $processed++;

                $data = array_combine($header, $row);

                if (!$this->validateProduct($data)) {
                    $skipped++;
                    continue;
                }

                $product = new Product;
                // Map data to product model properties
                $product->strProductName = $data['Product Name'];
                $product->strProductDesc = $data['Product Description'];
                $product->strProductCode = $data['Product Code'];
                $product->dtmDiscontinued = $data['Discontinued'];
                $product->stock_level = $data['Stock'];
                $product->price = $data['Cost in GBP'];
                $product->dtmDiscontinued = $data['Discontinued'] === 'yes'? Carbon::now() : null;

                // Apply discontinued logic based on data['discontinued']

                if ($isTest) {
                    // Simulate insert without actually saving to database
                    $success++;
                } else {
                    if (!$product->save()) {
                        $failed[] = ['message' => "Failed to save product '{$data['Product Name']}'"];
                    } else {
                        $success++;
                    }
                }
            }




        }

        return [
            'processed' => $processed,
            'success' => $success,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }

    private function validateProduct(array $data): bool
    {
        // Implement validation logic here
        // - Check for missing required fields
        // - Validate price range ($5 - $1000)
        // - Handle discontinued flag (set discontinued_at if needed)
        // Return true if valid, false otherwise

        // Example validation (replace with your specific logic)
        if($data['Cost in GBP'] < 5 && $data['Stock'] < 10){
            return false;
        }elseif($data['Cost in GBP'] >1000){
            return false;
        }elseif($data['Discontinued'] === 'yes'){
            return true;
        }else{
            return false;
        }

        return true;
    }
}
