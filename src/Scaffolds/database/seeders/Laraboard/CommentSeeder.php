<?php

namespace Database\Seeders\Laraboard;

use App\Models\Laraboard\Comment;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comment::factory(300)->create();
        Comment::factory(500)
            ->children()
            ->create();
    }
}
