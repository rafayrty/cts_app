<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeOption;
use Database\Seeders\ProductAttributeSeeder;

use function Pest\Laravel\get;
use function Pest\Laravel\seed;

it('Should Show Most Sold Products', function () {

    Product::factory()->create(['is_published' => false]);
    Product::factory()->create(['sold_amount' => 3]);
    Product::factory()->create(['sold_amount' => 4]);

    $response = get('api/products/most-sold-products');
    $response_body = $response->json();
    //Get most sold count
    $most_sold_products = Product::where('is_published', true)->orderBy('sold_amount', 'DESC')->get();

    $i = 0;
    //Expect the count to be equal
    expect($most_sold_products->count())->toEqual(count($response_body));

    //Check the order
    foreach ($most_sold_products as $product) {
        $check_product = Product::find($response_body[$i]['id']);
        expect($check_product->sold_amount)->toEqual($product->sold_amount);
        //Make sure product is also published
        expect($check_product->is_published)->toEqual(true);
        $i++;
    }

    $response->assertJsonStructure(
        ['*' => ['id',
            'images', 'product_type',
            'product_name', 'slug',
            'demo_name', 'replace_name',
            'excerpt', 'price',
            'discount_percentage', 'has_sale',
            'front_price', 'has_female',
            'age_groups', 'is_in_wishlist',
        ]]);
    $response->assertStatus(200);
});

it('Should Show Personalized Notebooks', function () {

    Product::factory()->create(['is_published' => false, 'product_type' => 2]);
    Product::factory()->create(['product_type' => 2]);
    Product::factory()->create(['product_type' => 1]);

    $response = get('api/products/personalized-notebooks');
    $response_body = $response->json();
    //Get most sold count
    $featured_products = Product::where('is_published', true)->where('product_type', 2)->get();

    $i = 0;
    //Expect the count to be equal
    expect($featured_products->count())->toEqual(count($response_body));

    //Check the order
    foreach ($featured_products as $product) {
        $check_product = Product::find($response_body[$i]['id']);
        expect($check_product->sold_amount)->toEqual($product->sold_amount);
        //Make sure product is also published
        expect($check_product->is_published)->toEqual(true);
        $i++;
    }

    $response->assertJsonStructure(
        ['*' => ['id',
            'images', 'product_type',
            'product_name', 'slug',
            'demo_name', 'replace_name',
            'excerpt', 'price',
            'discount_percentage', 'has_sale',
            'front_price', 'has_female',
            'age_groups', 'is_in_wishlist',
        ]]);
    $response->assertStatus(200);
});

it('Should Show Featured Products', function () {

    Product::factory()->create(['is_published' => false, 'featured' => 1]);
    Product::factory()->create(['featured' => 1]);

    $response = get('api/products/get-featured-products');
    $response_body = $response->json();
    //Get most sold count
    $featured_products = Product::where('is_published', true)->where('featured', true)->get();

    $i = 0;
    //Expect the count to be equal
    expect($featured_products->count())->toEqual(count($response_body));

    //Check the order
    foreach ($featured_products as $product) {
        $check_product = Product::find($response_body[$i]['id']);
        expect($check_product->sold_amount)->toEqual($product->sold_amount);
        //Make sure product is also published
        expect($check_product->is_published)->toEqual(true);
        $i++;
    }

    $response->assertJsonStructure(
        ['*' => ['id',
            'images', 'product_type',
            'product_name', 'slug',
            'demo_name', 'replace_name',
            'excerpt', 'price',
            'discount_percentage', 'has_sale',
            'front_price', 'has_female',
            'age_groups', 'is_in_wishlist',
        ]]);
    $response->assertStatus(200);
});

it('Should Show Related Product', function () {

    //Create the genders

    seed((ProductAttributeSeeder::class));

    $male = ProductAttributeOption::findOrFail(1);
    $female = ProductAttributeOption::findOrFail(2);
    $categories = Category::factory()->count(2)->create();

    $product_first = Product::factory()->afterCreating(function (Product $product) use ($male) {
        Product::find($product->id)->product_attributes()->sync([$male->id]);
        Product::find($product->id)->categories()->sync([Category::findOrFail(1)->id]);
    })->create();
    $product_second = Product::factory()->afterCreating(function (Product $product) use ($male) {
        Product::find($product->id)->product_attributes()->sync([$male->id]);
        Product::find($product->id)->categories()->sync([Category::findOrFail(1)->id]);
    })->create();

    $url = 'api/products/get-related-products/'.$product_first->id.'/1/Male';
    $response = get($url);
    $response_body = $response->json();

    //Get the first product from response
    $check_product = Product::find($response_body[0]['id']);
    //And check if it is equal to second product which is related to the first one
    expect($check_product->id)->toEqual($product_second->id);
    //Make sure product is also published
    expect($check_product->is_published)->toEqual(true);

    $response->assertJsonStructure(
        ['*' => ['id',
            'images', 'product_type',
            'product_name', 'slug',
            'demo_name', 'replace_name',
            'excerpt', 'price',
            'discount_percentage', 'has_sale',
            'front_price', 'has_female',
            'age_groups', 'is_in_wishlist',
        ]]);
    $response->assertStatus(200);
});
