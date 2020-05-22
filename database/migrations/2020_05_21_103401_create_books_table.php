<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function(Blueprint $table){
            $table->id();
            $table->string('author_name');
            $table->timestamps();
        });

        Schema::create('genres',function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('year');
            $table->string('isbn');
            $table->timestamps();
        });

        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignId('author_id');
            $table->foreignId('book_id');

            $table->foreign('author_id')->references('id')->on('authors')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('book_id')->references('id')->on('books')
                ->onUpdate('cascade')->onDelete('cascade');

        });
        Schema::create('book_genre', function (Blueprint $table) {
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('genre_id');

            $table->foreign('book_id')->references('id')->on('books')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('books');
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('book_genre');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
