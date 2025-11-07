<?php

use Illuminate\Support\Str;

function authHeaders(): array
{
    return [
        'Authorization' => 'Bearer ' . Str::random(60),
    ];
}

test('rejects invalid authorization', function () {
    $invalidTokens = [
        '',
        'Bearer shorttoken',
        'Bearer ' . Str::random(59),
        'Bearer ' . Str::random(61),
    ];

    foreach ($invalidTokens as $token) {
        $headers = ['Authorization' => $token];
        $this->getJson('/api/tasks', $headers)->assertStatus(401);
    }
});

test('paginated list with meta', function () {
    $response = $this->getJson('/api/tasks?per_page=2&page=1', authHeaders());

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'description',
                    'due_date',
                    'is_completed',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ]
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ],
        ]);

    expect($response->json('meta.per_page'))->toBe(2);
    expect($response->json('meta.current_page'))->toBe(1);
});

test('apply sorting by field and order', function () {
    $asc = $this->getJson('/api/tasks?sort_by=id&sort_order=asc', authHeaders());
    $desc = $this->getJson('/api/tasks?sort_by=id&sort_order=desc', authHeaders());

    $idAsc = collect($asc->json('data'))->pluck('id')->all();
    $idDesc = collect($desc->json('data'))->pluck('id')->all();

    expect(array_reverse($idAsc))->toBe($idDesc);
});

test('apply filter by is_completed', function () {
    $response = $this->getJson('/api/tasks?filter[is_completed]=true', authHeaders());

    $response->assertOk();

    collect($response->json('data'))->each(function ($task) {
        expect($task['is_completed'])->toBeTrue();
    });
});

test('get single task', function () {
    $response = $this->getJson('/api/tasks/1', authHeaders());

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'due_date',
                'is_completed',
                'completed_at',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('get single task not found', function () {
    $response = $this->getJson('/api/tasks/999', authHeaders());

    $response->assertNotFound();
});

test('create new task', function () {
    $payload = [
        'title' => 'Pest testing',
        'description' => 'Description for Pest testing',
        'due_date' => '2025-11-07',
        'is_completed' => false,
    ];
    $response = $this->postJson('/api/tasks', $payload, authHeaders());

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'due_date',
                'is_completed',
                'completed_at',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'data' => $payload,
        ]);
});

test('failed validation when fields are missing on create new task', function () {
    $response = $this->postJson('/api/tasks', [], authHeaders());

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title',
                'description',
                'due_date',
                'is_completed',
            ],
        ]);
});

test('update an existing task', function () {
    $payload = [
        'title' => 'Pest testing update',
        'description' => 'Description for Pest testing update',
        'due_date' => '2025-11-07',
        'is_completed' => true,
    ];
    $response = $this->putJson('/api/tasks/1', $payload, authHeaders());

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'due_date',
                'is_completed',
                'completed_at',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'data' => $payload,
        ]);
});

test('failed validation when fields are missing on update existing task', function () {
    $response = $this->putJson('/api/tasks/1', [], authHeaders());

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title',
                'description',
                'due_date',
                'is_completed',
            ],
        ]);
});

test('update non existing task', function () {
    $payload = [
        'title' => 'Pest testing update',
        'description' => 'Description for Pest testing update',
        'due_date' => '2025-11-07',
        'is_completed' => true,
    ];
    $response = $this->putJson('/api/tasks/999', $payload, authHeaders());

    $response->assertNotFound();
});

test('delete an existing task', function () {
    $response = $this->deleteJson('/api/tasks/1', [], authHeaders());

    $response->assertNoContent();
});

test('delete non existing task', function () {
    $response = $this->deleteJson('/api/tasks/999', [], authHeaders());

    $response->assertNotFound();
});
