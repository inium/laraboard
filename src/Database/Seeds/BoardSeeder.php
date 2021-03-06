<?php

namespace Inium\Laraboard\Database\Seeds;

use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 게시판 사용자, 게시판, 게시글, 댓글 생성
        factory(\Inium\Laraboard\App\Comment::class, 200)->create();
        factory(\Inium\Laraboard\App\Comment::class, 100)->create();
    }
}
