<?php

namespace Tests\Feature;

use App\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('books')->truncate();
        DB::table('authors')->truncate();
        DB::table('genres')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */

    public function test_genre_can_get_book()
    {
        $genres = factory(Genre::class, 2)->create();

        $response = $this->get('/api/genres');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $genres[0]->id,
                    'type' => 'GENRES',
                    'attributes' => [
                        'name' => $genres[0]->name,
                        'created_at' => $genres[0]->created_at->toJSON(),
                        'updated_at' => $genres[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => $genres[1]->id,
                    'type' => 'GENRES',
                    'attributes'=>[
                        'name' => $genres[1]->name,
                        'created_at' => $genres[1]->created_at->toJSON(),
                        'updated_at' => $genres[1]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
    }
    public function test_can_add_genre()
    {
        $data = [
            'type' => 'GENRES',
            'attributes' => [
                'name' => 'classic'
            ]
        ];

        $response = $this->postJson('/api/genres', $data);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'type' => 'GENRES',
                'attributes' => [
                    'name' => $data['attributes']['name'],
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON()
                ]
            ]
        ]);
        $this->assertDatabaseHas('genres',['name' => $data['attributes']['name']]);
    }

    public function test_can_update_genre()
    {
        $genre = factory(Genre::class)->create();
        sleep(1);
        $data = [
            'type' => 'GENRES',
            'attributes' => [
                'name' => 'adventure',
            ]
        ];
        $response = $this->patchJson('/api/genres/1', $data)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $genre->id,
                    'type' => 'GENRES',
                    'attributes' => [
                        'name' => $data['attributes']['name'],
                        'created_at' => $genre->created_at->toJSON(),
                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ],
                ],
            ]);
        $this->assertDatabaseHas('genres',['name' => $data['attributes']['name']]);
    }

    public function test_can_get_single_genre()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->get('/api/genres/1');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $genre->id,
                    'type' => 'GENRES',
                    'attributes' => [
                        'name' => $genre->name,
                        'created_at' => $genre->created_at->toJSON(),
                        'updated_at' => $genre->updated_at->toJSON()
                    ],
                ],
            ]);
    }
    public function test_can_delete_genre()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->delete('/api/genres/1')->assertStatus(204);
        $this->assertDatabaseMissing('genres',['name' => $genre->name]);
    }

    public function test_validates_input_before_creating_genre()
    {
        $data = [
            'type' => 'GENRES',
            'attributes' => [
                'name' => 12,
            ],
        ];
        $response = $this->postJSON('/api/genres', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'attributes.name' => [
                    '0' => 'The attributes.name must be a string.'
                ]
            ]
        ]);
    }
    public function test_validates_input_before_updating_genre()
    {
        factory(Genre::class)->create();
        $data = [
            'type' => 'GENRES',
            'attributes' => [
                'name' => 12,
            ],
        ];
        $response = $this->putJSON('/api/books/1', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => []
        ]);
    }
    public function test_validates_whether_genre_id_is_valid_before_updating()
    {

        factory(Genre::class)->create();
        $data = [
            'type' => 'GENRES',
            'attributes' => [
                'name' => 'classic',
            ],
        ];
        $response = $this->putJSON('/api/genres/5', $data);
        $response->assertStatus(404)->assertJson([
            'message' => 'genre does not exists.',
            'errors' => [
                'genre' => 'genre does not exist'
            ]
        ]);
    }
    public function test_validates_whether_genre_id_is_valid()
    {
        factory(Genre::class)->create();
        $response = $this->get('/api/genres/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'genre does not exists.',
            'errors' => [
                'genre' => 'genre does not exist'
            ]
        ]);
    }
    public function test_validates_whether_genre_id_is_valid_before_deleting()
    {
        factory(Genre::class)->create();
        $response = $this->delete('/api/genres/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'genre does not exists.',
            'errors' => [
                'genre' => 'genre does not exist'
            ]
        ]);
    }
}
