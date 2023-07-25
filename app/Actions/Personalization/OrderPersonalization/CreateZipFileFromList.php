<?php

namespace App\Actions\Personalization;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class CreateZipFileFromList
{
    /**
     * Create A Single Zip File From a List of Files
     *
     * @param  array<string>,string  $files,$name
     */
    public function __invoke($files, $name = 'download'): BinaryFileResponse
    {
        // Create a temporary zip file
        $zipPath = storage_path('app/tmp.zip');
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Add each file to the zip
        foreach ($files as $file) {
            $fileName = basename($file);
            //$zip->addFile(storage_path('app/'.$file), $fileName);
            $zip->addFile($file, $fileName);
        }

        // Close the zip
        $zip->close();

        // Create a BinaryFileResponse for the zip file
        $response = new BinaryFileResponse($zipPath);

        // Delete the zip file after it has been sent
        $response->deleteFileAfterSend(true);

        // Set the appropriate headers for the response
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'.zip"');

        return $response;
    }
}
