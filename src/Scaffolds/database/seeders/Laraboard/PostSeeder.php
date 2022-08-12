<?php

namespace Database\Seeders\Laraboard;

use App\Models\Laraboard\Board;
use App\Models\Laraboard\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory(200)->create();
    }
}
