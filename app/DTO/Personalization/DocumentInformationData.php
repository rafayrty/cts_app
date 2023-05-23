<?php

namespace App\DTO\Personalization;

use App\Models\Document;
use Spatie\LaravelData\Data;

class DocumentInformationData extends Data
{
    public function __construct(
        public Document $document,
        public array $pages,
        public array $dedications,
        public array $barcodes,
    ) {
    }
}
