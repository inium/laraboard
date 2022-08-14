<?php

namespace Database\Seeders\Laraboard;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Database\Seeders\Laraboard\BoardSeeder;
use Database\Seeders\Laraboard\PostSeeder;
use Database\Seeders\Laraboard\CommentSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LaraboardSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();

        // For testing purpose
        \App\Models\User::factory()->create([
            "name" => "Laraboard",
            "email" => "laraboard@example.net",
            "email_verified_at" => now(),
            "password" =>
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            "remember_token" => Str::random(10),
        ]);

        $this->call([
            BoardSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
