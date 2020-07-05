<?php

namespace Inium\Laraboard\Database\Seeds;

use Illuminate\Database\Seeder;

class BoardRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 게시판 사용자 권한 생성
        factory(\Inium\Laraboard\App\Role::class)->create();
    }
}
