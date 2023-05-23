<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductsCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove images or pdf files which are not linked to any products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $documents = Document::all();
        $filesToKeep = [];
        foreach ($documents as $document) {
            //$file_name = substr($document->attatchment, strpos($document->attatchment, "/") + 1);
            $filesToKeep[] = 'public/'.$document->attatchment;
        }
        // directory where the files are located
        $directory = '/public/uploads';
        // get a list of all files in the directory
        $files = Storage::files($directory);
        // loop over the files and delete any that are not in the $filesToKeep array
        //foreach ($files as $file) {
        //if (!in_array($file, $filesToKeep) && Storage::mimeType($file) === 'application/pdf') {
        //Storage::delete($file);
        //}
        //}
        $products = Product::all();

        $filesToKeep = [];
        foreach ($products as $product) {
            $pdf_info = json_decode($product->pdf_info, true);
            foreach ($pdf_info as $pdf) {
                $filesToKeep[] = $pdf['pdf'];
            }
        }
        //To merge all subarrays

        // directory where the files are located
        //$directory = public_path('/uploads');

        // get a list of all files in the directory
        //$files = File::allFiles($directory);
        //$filesToKeep = call_user_func_array('array_merge', $filesToKeep);

        //foreach ($files as $file) {
        //$fileName = $file->getRelativePathname();
        //if (!in_array($fileName, $filesToKeep) && strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'png') {
        //File::delete($file);
        //}
        //}
        return Command::SUCCESS;
    }
}
