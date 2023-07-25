<?php

use function Pest\Laravel\get;

test('homepage redirects to login page', function () {
    $response = get('/');
    $response->assertStatus(302);
    $response->assertRedirect('admin');
});
