<?php

use Illuminate\Database\Seeder;

class LaraboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 게시판 사용자, 게시판, 게시글, 댓글 생성
        factory(App\Laraboard\Comment::class, 300)->create();
    }
}
