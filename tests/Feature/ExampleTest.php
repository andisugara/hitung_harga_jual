<?php

test('the application returns a successful response', function () {
    $this->get('/')
        ->assertRedirect(route('products.index'));

    $this->get(route('products.index'))
        ->assertOk();
});
