<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;

class ReviewController extends Controller
{

    public function get_product_reviews($product_id){
      $review = Review::where('product_id',$product_id)->get();

      return $review;
    }

    public function add_review(ReviewRequest $request,$product_id){
        $review = Review::create([
          'product_id'=>$product_id,
          'user_id'=>$request->user()->id,
          'review'=>$request->review,
          'star'=>$request->star,
        ]);

      return $review;
    }

    public function delete_review($id){

      $review = Review::findOrFail($id)->where('user_id',request()->user()->id)->get();

      return $review;
    }

}
