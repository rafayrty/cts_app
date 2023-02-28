<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;

class ReviewController extends Controller
{
    public function get_product_reviews($product_id)
    {
        $review = Review::where('product_id', $product_id)->where('status', true)->get();

        return $review;
    }

    public function add_review(ReviewRequest $request, $product_id)
    {
        $review = Review::create([
            'product_id' => $product_id,
            'user_id' => $request->user()->id,
            'review' => $request->review,
            'stars' => $request->stars,
        ]);

        return $review;
    }

    /**
     * Delete the review
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_review($id)
    {
        $review = Review::findOrFail($id)->where('user_id', request()->user()->id)->get();

        Review::findOrFail($id)->delete();

        return $review;
    }

    public function edit_review($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id != request()->user()->id) {
            abort(404);
        }

        return $review;
    }

    public function update_review(ReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);

        $review->update([
            'review' => $request->review,
            'stars' => $request->stars,
        ]);

        return $review;
    }
}
