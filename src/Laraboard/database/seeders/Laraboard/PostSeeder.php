<?php

namespace Database\Seeders\Laraboard;

use App\Models\Laraboard\Post;
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
        // 게시글 공지사항 5개 생성
        Post::factory(5)
            ->notice()
            ->create();

        // 게시글 200개 생성
        Post::factory(200)->create();
    }
}
