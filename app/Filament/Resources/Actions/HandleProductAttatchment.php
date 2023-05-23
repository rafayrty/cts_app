<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\Facades\Storage;
//use Spatie\PdfToImage\Pdf as ConvertToImage;
use Karkow\MuPdf\Pdf;

class HandleProductAttatchment
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $name = $state->getClientOriginalName();
        if ($name != $get('pdf_name') && $get('../../pdf_info') != '' && $get('pdf_name') != null) {
            $pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('pdf_name'), array_column($pdfs, 'filename'));

            unset($pdfs[$key]);
            $set('../../pdf_info', json_encode($pdfs));
        }
        $path = Storage::disk('local')->path($state->path());
        $pdf = new Pdf($state->path(), config('app.mupdf_path'));
        $set('pdf_name', $name);
        if ($get('../../pdf_info') != '') {
            $pdfs = json_decode($get('../../pdf_info'), true);
        } else {
            $pdfs = [];
        }
        $count = $this->getPDFPages($state->path());
        $dimensions = $this->getDimensions($state->path());
        $set('dimensions', $dimensions);
        $images = [];

        if ($get('type') != 2) {
            $img_path = 'uploads/'.time().$get('type').'-'.'actual-cover'.'.png';

            $img = $pdf->setPage(1)->saveImage($img_path);
            $images = $this->getSplittedImages($img_path);

            $dimensions['width'] = $dimensions['width'] / 2;
            //$dimensions['width'] = getimagesize($images[0])[0];
            //$dimensions['height'] = getimagesize($images[0])[1];

            array_push($pdfs, ['filename' => $name, 'dimensions' => json_encode($dimensions), 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
        } else {
            $images = [];
            for ($i = 1; $i <= $count; $i++) {
                $img_path = 'uploads/'.time().$get('type').'-'.$i.'.png';
                $pdf->setPage($i)
                    ->saveImage($img_path);
                $images[] = $img_path;
            }

            //array_push($pdfs, ['filename' => $name, 'dimensions' => json_encode($get('dimensions')), 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
            array_push($pdfs, ['filename' => $name, 'dimensions' => json_encode($dimensions), 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
        }

        $set('../../pdf_info', json_encode($pdfs));




        //$pages = $get('../../pages');
        //$new_pages = [];
        //$pdf_info = $pdfs;
        //$documents = [];

        //$found_document = [];

        //foreach($pdfs as $pdf){
            //if($pdf['name']==$get('name')){
                 //$found_document = $pdf;
            //}
        //}

        //foreach ($pages as $key => $page) {
            //if ($page['document'] == $get('name')) {
                //$page['pages']['dimensions'] = $get('dimensions');
                //$new_pages[] = $page;
            //}
         //}
            ////Splicing the pages
        ////$new_pages = array_splice($new_pages,0,count($found_document));
        //$total_pages = count($found_document['pdf']);
        //$new_updated_pages = [];
        //foreach($new_pages as $page){
            //if($page['document']==$found_document['name']){
                //if($page['pages']['page_number']<$total_pages){
                        //$new_updated_pages[] = $page;
                //}
            //}
        //}
        //$set('pages', $new_updated_pages);
        //$set('pages', $get('pages'));
    }

    public function getDimensions($document)
    {
    $cmd = config('app.pdf_info_path');
    exec("$cmd \"$document\" 2>&1", $output);

    $dimensions = [];

    foreach ($output as $op) {
        //Extract the number

        //if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
        if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
            $dimensions['width'] = intval($matches[1]);
            $dimensions['height'] = intval($matches[2]);
            break;
        }

        if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts~', $op, $matches) === 1) {
            $dimensions['width'] = intval($matches[1]);
            $dimensions['height'] = intval($matches[2]);
            break;
        }
    }

    return $dimensions;
}

      public function getPDFPages($document)
      {
          $cmd = config('app.pdf_info_path');
          exec("$cmd \"$document\" 2>&1", $output);
          $pagecount = 0;
          foreach ($output as $op) {
              // Extract the number
              if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                  $pagecount = intval($matches[1]);
                  break;
              }
          }

          return $pagecount;
      }

  public function getSplittedImages($img_path)
  {
      // Load the PNG image
      $image = imagecreatefrompng($img_path);

      // Get the width and height of the image
      $width = imagesx($image);
      $height = imagesy($image);

      // Create two new images for each half
      $leftImage = imagecreatetruecolor($width / 2, $height);
      $rightImage = imagecreatetruecolor($width / 2, $height);

      // Copy the left half of the original image to the left image
      imagecopy($leftImage, $image, 0, 0, 0, 0, $width / 2, $height);

      // Copy the right half of the original image to the right image
      imagecopy($rightImage, $image, 0, 0, $width / 2, 0, $width / 2, $height);

      // Save the two new images

      $left_path = 'uploads/'.time().'left.png';
      $right_path = 'uploads/'.time().'right.png';

      imagepng($leftImage, $left_path);
      imagepng($rightImage, $right_path);

      // Free up memory
      imagedestroy($image);
      imagedestroy($leftImage);
      imagedestroy($rightImage);

      return [$left_path, $right_path];
  }
}
