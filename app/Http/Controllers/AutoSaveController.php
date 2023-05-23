<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\Product;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutoSaveController extends Controller
{
    public function index(Request $request)
    {
        if (Filament::auth()->user()) {
            if (! $request->product_id) {
                $product = Product::create([
                    'demo_name' => $request->demo_name,
                    'replace_name' => $request->replace_name,
                    'product_name' => $request->product_name,
                    'excerpt' => $request->excerpt,
                    'slug' => $request->slug,
                    'description' => $request->description,
                    'price' => $request->price,
                    'discount_percentage' => $request->discount_percentage ?? 0,
                    'featured' => $request->featured,
                ]);

                return $product;
            } else {
                $images = [];
                $product = Product::findOrFail($request->product_id);
                //Check if images are the same or different
                if (! $this->check_images($request->images, $product)) {
                    foreach ($request->images as $image) {
                        $filename = substr($image, strpos($image, ':') + 1);
                        $path = 'livewire-tmp/'.$filename;
                        //$image = Storage::disk('local')->path('livewire-tmp/'.$filename);
                        Storage::copy($path, 'public/uploads/'.$filename);
                        $images[] = 'uploads/'.$filename;
                    }
                }

                $update_array = [
                    'demo_name' => $request->demo_name,
                    'replace_name' => $request->replace_name,
                    'product_name' => $request->product_name,
                    'slug' => $request->slug,
                    'excerpt' => $request->excerpt,
                    'description' => $request->description,
                    'price' => $request->price,
                    'discount_percentage' => $request->discount_percentage ?? 0,
                    'featured' => $request->featured,
                    'category_id' => $request->category_id,
                    'pdf_info' => $request->pdf_info,
                    //'images' => $images
                ];

                if (count($images) > 0) {
                    $update_array = array_merge(['images' => $images], $update_array);
                }

                $product = Product::findOrFail($request->product_id)->update($update_array);

                //Create Product Attribute
                $prd = Product::findOrFail($request->product_id);

                if ($request->product_attributes) {
                    $prd->product_attributes()->sync($request->product_attributes);
                }
                if ($request->covers) {
                    $prd->covers()->sync($request->covers);
                }

                if ($request->category) {
                    $category = Category::findOrFail($request->category);
                    $prd->category()->associate($category);
                }

                //For Documents
                if ($request->Documents) {
                    foreach ($request->Documents as $document) {
                        if ($document['name'] && $document['attatchment'] && $document['pdf_name'] && ! $this->check_documents($document['attatchment'], $prd)) {
                            $attatchments = [];
                            $attatchment = $document['attatchment'][array_key_first($document['attatchment'])];
                            $filename = substr($attatchment, strpos($attatchment, ':') + 1);
                            $path = 'livewire-tmp/'.$filename;
                            //dd($path);
                            //$image = Storage::disk('local')->path('livewire-tmp/'.$filename);
                            Storage::copy($path, 'public/uploads/'.$filename);
                            $attatchment = 'uploads/'.$filename;
                            $document = Document::updateOrCreate([
                                'product_id' => $request->product_id,
                            ], [
                                'pdf_name' => $document['pdf_name'],
                                'name' => $document['name'],
                                'type' => $document['type'],
                                'gender' => $document['gender'],
                                'attatchment' => $attatchment,
                                'dimensions' => $document['dimensions'],
                            ]);
                        }
                    }
                }
                if ($request->pages) {
                    $pages_array = [];
                    foreach ($request->pages as $page) {
                        if ($page['pages']) {
                            $pages_array[] = $page;
                        }
                    }
                    Product::findOrFail($prd->id)->update(['pages' => $pages_array]);
                }

                if ($request->dedications) {
                    $dedications_array = [];
                    foreach ($request->dedications as $dedication) {
                        if ($dedication['dedications']) {
                            $dedications_array[] = $page;
                        }
                    }
                    Product::findOrFail($prd->id)->update(['dedications' => $dedications_array]);
                }

                if ($request->barcodes) {
                    $barcodes_array = [];
                    foreach ($request->barcodes as $barcode) {
                        if ($barcode['barcodes']) {
                            $barcodes_array[] = $barcode;
                        }
                    }
                    Product::findOrFail($prd->id)->update(['barcodes' => $barcodes_array]);
                }

                return $prd;
            }
        }
        abort(404);
    }

    public function check_images($images, $product)
    {
        $new_array = [];
        foreach ($images as $image) {
            $filename = substr($image, strpos($image, ':') + 1);
            $new_array = ['uploads/'.$filename];
        }

        if (! $product->images) {
            return false;
        }
        $result = array_intersect($new_array, $product->images);
        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    public function check_documents($attatchment, $product)
    {
        $new_path = [];
        $attatchment = $attatchment[array_key_first($attatchment)];
        $filename = substr($attatchment, strpos($attatchment, ':') + 1);
        $new_path = 'uploads/'.$filename;
        $documents = Document::where('product_id', $product->id);
        $docs_array = [];
        if (! $documents) {
            return false;
        }
        foreach ($documents as $doc) {
            if ($new_path == $doc->attatchment) {
                return false;
            }

            return true;
        }

        return false;
    }
}
