<?php

namespace Inium\Laraboard\Database\Seeds;

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
        factory(\Inium\Laraboard\Models\Comment::class, 200)->create();
        factory(\Inium\Laraboard\Models\Comment::class, 100)->create();
    }
}
