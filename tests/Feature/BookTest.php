<?php

namespace Tests\Feature;
use App\Author;
use App\Book;
use App\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('books')->truncate();
        DB::table('authors')->truncate();
        DB::table('genres')->truncate();
        DB::table('book_genre')->truncate();
        DB::table('author_book')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function user_can_get_book()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));

        $response = $this->get('/api/books');

        $response->assertStatus(200);
        $response->assertJson([
           'data' => [
               [
                   'id' => $books[0]->id,
                   'type' => 'BOOKS',
                   'attributes' => [
                       'title' => $books[0]->title,
                       'year' => $books[0]->year,
                       'isbn' => $books[0]->isbn,
                   ],
                   'relationships' => [
                       'authors' => [
                           'links' => [
                               'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                               'related' =>route('books.related.authors',['id'=>$books[0]->id]),
                           ]
                       ],
                       'genres' => [
                           'links' => [
                               'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                               'related' =>route('books.related.genres',['id'=>$books[0]->id]),
                           ],
                           'data' => [
                               [
                                   'id' => $genres->id,
                                   'type' => 'GENRES'
                               ]
                           ]
                       ]
                   ]
               ],
               [
                   'id' => $books[1]->id,
                   'type' => 'BOOKS',
                   'attributes' => [
                       'title' => $books[1]->title,
                       'year' => $books[1]->year,
                       'isbn' => $books[1]->isbn,
                   ],
                   'relationships' => [
                       'authors' => [
                           'links' => [
                               'self' => route('books.relationships.authors', ['id' => $books[1]->id]),
                               'related' =>route('books.related.authors',['id'=>$books[1]->id]),
                           ],
                           'data' => [
                               [
                                   'id' => $authors->id,
                                   'type' => 'AUTHORS'
                               ]
                           ]
                       ],
                       'genres' => [
                           'links' => [
                               'self' => route('books.relationships.genres', ['id' => $books[1]->id]),
                               'related' =>route('books.related.genres',['id'=>$books[1]->id]),
                           ],
                           'data' => [
                               [
                                   'id' => $genres->id,
                                   'type' => 'GENRES'
                               ]
                           ]
                       ]
                   ]
               ]
           ]
       ]);
    }
    public function test_can_add_book()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        $data = [
            'type' => 'BOOKS',
            'attributes' => [
                'title' => 'The Gods are not to be Blamed',
                'isbn' => Str::random(24),
                'year' => '2020',
                'author_id' => [1],
                'genre_id' => [1],
            ],
        ];
        $response = $this->postJson('/api/books', $data);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'type' => 'BOOKS',
                'attributes' => [
                    'title' => $data['attributes']['title'],
                    'year' => $data['attributes']['year'],
                    'isbn' => $data['attributes']['isbn'],
                ],
                'relationships' => [
                    'authors' => [
                        'links' => [
                            'self' => route('books.relationships.authors', ['id' => 1]),
                            'related' =>route('books.related.authors',['id'=>1]),
                        ],
                        'data' => [
                            [
                                'id' => 1,
                                'type' => 'AUTHORS'
                            ]
                        ]
                    ],
                    'genres' => [
                        'links' => [
                            'self' => route('books.relationships.genres', ['id' => 1]),
                            'related' =>route('books.related.genres',['id'=>1]),
                        ],
                        'data' => [
                            [
                                'id' => 1,
                                'type' => 'GENRES'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertDatabaseHas('books',[
            'title' => $data['attributes']['title'],
            'year' => $data['attributes']['year']
        ]);
    }
    public function test_can_update_book()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class)->create();
        $books->authors()->attach($authors->pluck('id'));
        $books->genres()->attach($genres->pluck('id'));
        sleep(1);
        $data = [
            'type' => 'BOOKS',
            'attributes' => [
                'title' => 'Things fall apart',
                'year' => '2019'
            ]
        ];
        $response = $this->patchJson('/api/books/1', $data)->assertStatus(200);
        $response->assertJson([
                'data' => [
                    'id' => 1,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $data['attributes']['title'],
                        'year' => $data['attributes']['year'],
                        'isbn' => $books->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => 1]),
                                'related' =>route('books.related.authors',['id'=>1]),
                            ],
                            'data' => [
                                [
                                    'id' => 1,
                                    'type' => 'AUTHORS'
                                ]
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => 1]),
                                'related' =>route('books.related.genres',['id'=>1]),
                            ],
                            'data' => [
                                [
                                    'id' => 1,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('books',['title' => $data['attributes']['title']]);
    }
    public function test_can_get_single_book()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class)->create();
        $books->authors()->attach($authors->pluck('id'));
        $books->genres()->attach($genres->pluck('id'));

        $response = $this->get('/api/books/1')->assertStatus(200)
            ->assertJson([
                'data' => [
                        'id' => $books->id,
                        'type' => 'BOOKS',
                        'attributes' => [
                            'title' => $books->title,
                            'year' => $books->year,
                            'isbn' => $books->isbn,
                        ],
                        'relationships' => [
                            'authors' => [
                                'links' => [
                                    'self' => route('books.relationships.authors', ['id' => $books->id]),
                                    'related' =>route('books.related.authors',['id'=>$books->id]),
                                ],
                                'data' => [
                                    [
                                        'id' => $authors->id,
                                        'type' => 'AUTHORS'
                                    ]
                                ]
                            ],
                            'genres' => [
                                'links' => [
                                    'self' => route('books.relationships.genres', ['id' => $books->id]),
                                    'related' =>route('books.related.genres',['id'=>$books->id]),
                                ],
                                'data' => [
                                    [
                                        'id' => $genres->id,
                                        'type' => 'GENRES'
                                    ]
                                ]
                            ]
                        ]
                ],
            ]);
    }
    public function test_can_delete_book()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        $book = factory(Book::class)->create();
        $response = $this->delete('/api/books/1')->assertStatus(204);
        $this->assertDatabaseMissing('books',['title' => $book->title]);
    }
    public function test_validates_input_before_creating_user()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        $data = [
            'type' => 'BOOKS',
            'attributes' => [
                'title' => 'The Gods are not to be Blamed',
                'year' => 2020,
                'author_id' => [2],
                'genre_id' => [1],
            ],
        ];
        $response = $this->postJSON('/api/books', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'attributes.isbn' => [
                    '0' => 'The attributes.isbn field is required.',
                ],
                'attributes.year' => [
                    '0' => 'The attributes.year must be a string.'
                ],
                'attributes.author_id' => [
                    '0' => 'The selected attributes.author id is invalid.'
                ]
            ]
        ]);
    }
    public function test_validates_input_before_updating_user()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class)->create();
        $books->authors()->attach($authors->pluck('id'));
        $books->genres()->attach($genres->pluck('id'));
        $data = [
            'type' => 'BOOK',
            'attributes' => [
                'title' => 'Things fall apart',
                'year' => 2019
            ]
        ];
        $response = $this->putJSON('/api/books/1', $data);
        $response->assertStatus(422)->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'attributes.year' => [
                    '0' => 'The attributes.year must be a string.'
                ],
            ]
        ]);
    }
    public function test_validates_whether_user_id_is_valid_before_updating()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        factory(Book::class)->create();
        $data = [
            'type' => 'BOOKS',
            'attributes' => [
                'title' => 'Things fall apart',
                'year' => '2019'
            ]
        ];
        $response = $this->putJSON('/api/books/5', $data);
        $response->assertStatus(404)->assertJson([
            'message' => 'User does not exists.',
            'errors' => [
                'user' => 'User does not exist'
            ]
        ]);
    }
    public function test_validates_whether_user_id_is_valid()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        factory(Book::class)->create();
        $response = $this->get('/api/books/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'User does not exists.',
            'errors' => [
                'user' => 'User does not exist'
            ]
        ]);
    }
    public function test_validates_whether_user_id_is_valid_before_deleting()
    {
        factory(Author::class)->create();
        factory(Genre::class)->create();
        factory(Book::class)->create();
        $response = $this->delete('/api/books/5');
        $response->assertStatus(404)->assertJson([
            'message' => 'User does not exists.',
            'errors' => [
                'user' => 'User does not exist'
            ]
        ]);
    }

    public function test_gets_number_of_books_based_on_author ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));

        $response = $this->get('api/books/report?by=author&id=1');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'number' => 2
            ]
        ]);
    }
    public function test_gets_number_of_books_based_on_genre ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));

        $response = $this->get('api/books/report?by=genre&id=1');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'number' => 2
            ]
        ]);
    }
    public function test_gets_number_of_books_based_on_year ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books/report?by=year&id={$books[0]->year}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'number' => 1
            ]
        ]);
    }
    public function test_able_to_search_a_book_by_title ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books?search=title&value={$books[0]->title}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $books[0]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[0]->title,
                        'year' => $books[0]->year,
                        'isbn' => $books[0]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[0]->id]),
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[0]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ]);
    }
    public function test_able_to_search_a_book_by_isbn ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books?search=isbn&value={$books[0]->isbn}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $books[0]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[0]->title,
                        'year' => $books[0]->year,
                        'isbn' => $books[0]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                                'related' => route('books.related.authors', ['id' => $books[0]->id]),
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                                'related' => route('books.related.genres', ['id' => $books[0]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ]);
    }
    public function test_able_to_browse_a_book_by_author ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books?browseby=author&value={$authors->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $books[0]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[0]->title,
                        'year' => $books[0]->year,
                        'isbn' => $books[0]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[0]->id]),
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[0]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'id' => $books[1]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[1]->title,
                        'year' => $books[1]->year,
                        'isbn' => $books[1]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[1]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[1]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->id,
                                    'type' => 'AUTHORS'
                                ]
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[1]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[1]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
    public function test_able_to_browse_a_book_by_genres ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books?browseby=genres&value={$genres->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $books[0]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[0]->title,
                        'year' => $books[0]->year,
                        'isbn' => $books[0]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[0]->id]),
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[0]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'id' => $books[1]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[1]->title,
                        'year' => $books[1]->year,
                        'isbn' => $books[1]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[1]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[1]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->id,
                                    'type' => 'AUTHORS'
                                ]
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[1]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[1]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
    public function test_able_to_browse_a_book_by_year ()
    {
        $authors = factory(Author::class)->create();
        $genres = factory(Genre::class)->create();
        $books = factory(Book::class, 2)->create();
        $books[0]->authors()->attach($authors->pluck('id'));
        $books[0]->genres()->attach($genres->pluck('id'));
        $books[1]->authors()->attach($authors->pluck('id'));
        $books[1]->genres()->attach($genres->pluck('id'));
        $response = $this->get("api/books?browseby=year&value={$books[0]->year}");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => $books[0]->id,
                    'type' => 'BOOKS',
                    'attributes' => [
                        'title' => $books[0]->title,
                        'year' => $books[0]->year,
                        'isbn' => $books[0]->isbn,
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route('books.relationships.authors', ['id' => $books[0]->id]),
                                'related' =>route('books.related.authors',['id'=>$books[0]->id]),
                            ]
                        ],
                        'genres' => [
                            'links' => [
                                'self' => route('books.relationships.genres', ['id' => $books[0]->id]),
                                'related' =>route('books.related.genres',['id'=>$books[0]->id]),
                            ],
                            'data' => [
                                [
                                    'id' => $genres->id,
                                    'type' => 'GENRES'
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ]);
    }
}
