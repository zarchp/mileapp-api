<?php

test('login successfully', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => '12345678',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
        ])
        ->assertJson([
            'message' => 'Mocked login successful',
            'token_type' => 'Bearer',
        ]);

    expect($response->json('access_token'))->toBeString()->toHaveLength(60);
});

test('failed validation when fields are missing', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'email',
                'password',
            ],
        ]);
});
