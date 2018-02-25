<?php

use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $post = new App\Post([
          'title' => 'Learning Laravel',
          'content' => 'This blog post will get you right on the track with laravel!'
        ]);
        $post->save();

        $post = new App\Post([
          'title' => 'Somethig else',
          'content' => 'Some other content!'
        ]);
        $post->save();

    }
}
