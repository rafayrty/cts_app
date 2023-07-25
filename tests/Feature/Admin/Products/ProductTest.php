<?php

namespace Tests\Feature\Admin\Products;

use function Pest\Laravel\get;
use function Tests\loginAdmin;

test('Check if Product page is Working', function () {

    loginAdmin();
    get('/admin/products')
        ->assertStatus(200);
});
