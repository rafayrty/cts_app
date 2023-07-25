<?php

namespace App\Actions\Personalization;

use App\DTO\Personalization\DocumentInformationData;
use App\Models\Document;

class GetDocumentInformation
{
    public function __invoke($id): DocumentInformationData
    {
        $document = Document::findOrFail($id);
        $pages = $document->product->pagesParsed;
        $dedications = $document->product->dedicationsParsed;
        $barcodes = $document->product->barcodes;

        return new DocumentInformationData($document, $pages, $dedications, $barcodes);
    }
}
