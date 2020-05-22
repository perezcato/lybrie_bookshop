<?php

namespace Tests\Feature;

use App\Author;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthorTest extends TestCase
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

    public function test_authors_can_get_book()
    {
        $authors = factory(Author::class, 2)->create();

        $response = $this->get('/api/authors');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $authors[0]->id,
                    'type' => 'AUTHORS',
                    'attributes' => [
                        'author_name' => $authors[0]->author_name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => $authors[1]->id,
                    'type' => 'AUTHORS',
                    'attributes'=>[
                        'author_name' => $authors[1]->author_name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
    }
    public function test_can_add_authors()
    {
        $data = [
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => 'Efo Kodjo'
            ]
        ];

        $response = $this->postJson('/api/authors', $data);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'type' => 'AUTHORS',
                'attributes' => [
                    'author_name' => $data['attributes']['author_name'],
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON()
                ]
            ]
        ]);
        $this->assertDatabaseHas('authors',['author_name' => $data['attributes']['author_name']]);
    }

    public function test_can_update_authors()
    {
        $authors = factory(Author::class)->create();
        sleep(1);
        $data = [
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => 'adventure',
            ]
        ];
        $response = $this->patchJson('/api/authors/1', $data)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $authors->id,
                    'type' => 'AUTHORS',
                    'attributes' => [
                        'author_name' => $data['attributes']['author_name'],
                        'created_at' => $authors->created_at->toJSON(),
                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ],
                ],
            ]);
        $this->assertDatabaseHas('authors',['author_name' => $data['attributes']['author_name']]);
    }

    public function test_can_get_single_authors()
    {
        $author = factory(Author::class)->create();
        $response = $this->get('/api/authors/1');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'type' => 'AUTHORS',
                    'attributes' => [
                        'author_name' => $author->author_name,
                        'created_at' => $author->created_at->toJSON(),
                        'updated_at' => $author->updated_at->toJSON()
                    ],
                ],
            ]);
    }
    public function test_can_delete_authors()
    {
        $author = factory(Author::class)->create();
        $response = $this->delete('/api/authors/1')->assertStatus(204);
        $this->assertDatabaseMissing('authors',['author_name' => $author->name]);
    }

    public function test_validates_input_before_creating_authors()
    {
        $data = [
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => 12,
            ],
        ];
        $response = $this->postJSON('/api/authors', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'attributes.author_name' => [
                    '0' => 'The attributes.author name must be a string.'
                ]
            ]
        ]);
    }
    public function test_validates_input_before_updating_authors()
    {
        factory(Author::class)->create();
        $data = [
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => 12,
            ],
        ];
        $response = $this->putJSON('/api/books/1', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => []
        ]);
    }
    public function test_validates_whether_authors_id_is_valid_before_updating()
    {

        factory(Author::class)->create();
        $data = [
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => 'classic',
            ],
        ];
        $response = $this->putJSON('/api/authors/5', $data);
        $response->assertStatus(404)->assertJson([
            'message' => 'author does not exists.',
            'errors' => [
                'author' => 'author does not exist'
            ]
        ]);
    }
    public function test_validates_whether_authors_id_is_valid()
    {
        factory(Author::class)->create();
        $response = $this->get('/api/authors/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'author does not exists.',
            'errors' => [
                'author' => 'author does not exist'
            ]
        ]);
    }
    public function test_validates_whether_authors_id_is_valid_before_deleting()
    {
        factory(Author::class)->create();
        $response = $this->delete('/api/authors/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'author does not exists.',
            'errors' => [
                'author' => 'author does not exist'
            ]
        ]);
    }
}
