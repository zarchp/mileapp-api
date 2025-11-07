<?php

test('the application index returns a not found response', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
