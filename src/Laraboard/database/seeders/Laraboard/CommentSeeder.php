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
        // 댓글 생성
        Comment::factory(100)->create();

        // 자식 댓글 생성
        $coll = Comment::all();
        foreach ($coll as $v) {
            Comment::factory(rand(1, 8))
                ->children($v->id, $v->post_id)
                ->create();
        }
    }
}
