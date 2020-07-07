<?php

namespace Inium\Laraboard\Database\Seeds;

use Illuminate\Database\Seeder;
use Inium\Laraboard\App\Role;

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
        $collections = [
            ['name' => 'admin', 'description' => 'admin', 'is_admin' => true],
            ['name' => 'lv10',  'description' => 'lv10',  'is_admin' => false],
            ['name' => 'lv9',   'description' => 'lv9',   'is_admin' => false],
            ['name' => 'lv8',   'description' => 'lv8',   'is_admin' => false],
            ['name' => 'lv7',   'description' => 'lv7',   'is_admin' => false],
            ['name' => 'lv6',   'description' => 'lv6',   'is_admin' => false],
            ['name' => 'lv5',   'description' => 'lv5',   'is_admin' => false],
            ['name' => 'lv4',   'description' => 'lv4',   'is_admin' => false],
            ['name' => 'lv3',   'description' => 'lv3',   'is_admin' => false],
            ['name' => 'lv2',   'description' => 'lv2',   'is_admin' => false],
            ['name' => 'lv1',   'description' => 'lv1',   'is_admin' => false]
        ];

        $privileges = collect($collections)->map(function ($elem) {
            return factory(Role::class)->create($elem);
        });
    }
}
