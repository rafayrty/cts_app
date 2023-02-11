<?php

namespace App\Http\Controllers;

use App\Actions\GeneratePDF;
use App\Models\Fonts;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonalizationController extends Controller
{
    public function __construct(GeneratePDF $generatePDF)
    {
        $this->GeneratePDFAction = $generatePDF;
    }

   /**
    * Generate PDF by sending the request to the
    *
    * @param  \Illuminate\Http\Request  $request
    * @return string|null
    */
   public function generatePDF($id)
   {
       $product = Product::findOrFail($id);
       $document = $product->documents->last();
       //$pages = $product->pages;
       $file = Storage::disk('local')->path('public/'.$document->attatchment);

       $product = Product::findOrFail($id);

       $document = $product->documents->first();
       $pages = $product->pages;

       $fonts = Fonts::all();
       $array = [];
       if ($fonts) {
           foreach ($fonts as $font) {
               $array[] = ['font_name' => $font->font_name, 'attatchment' =>  Storage::path('public/'.$font->attatchment) ];
           }
       }

       $array[] = ['font_name' => 'NotoSansArabic-Regular', 'attatchment' => public_path('fonts/NotoSansArabic-Regular')];
       if (file_exists($file)) {
           $response = ($this->GeneratePDFAction)($file, $pages, array_values($array));
           $body = $response->getBody();
           // Explicitly cast the body to a string
           $stringBody = (string) $body;

           $file_name = 'GeneratedPDF-'.time().'.pdf';
           $path = 'downloads/'.$file_name;
           Storage::put($path, $stringBody);

           return response()->download(Storage::path($path), $file_name, [], 'inline');
       }

       abort(404);
   }
}
