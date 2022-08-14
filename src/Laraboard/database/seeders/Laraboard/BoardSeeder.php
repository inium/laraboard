<?php

namespace Database\Seeders\Laraboard;

use App\Models\Laraboard\Board;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 게시판 1개 생성
        Board::factory(1)->create();
    }
}
