<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Trade;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user = User::create([
            'name' => 'Saly',
            'email' => 'analista.procesos@sedintl.com',
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => '402e83e9-cc23-11ee-8452-0e4de3ffebc3',
            'user_id' => '75a6b76a-4605-4300-9c94-085208582eeb',
            'role_type' =>User::ALLROLES['Staff'],
        ]);
        $user->createToken('profile', ['user-create','user-edit', 'user-show']);
        $user->createToken('operation', ['product-list','product-show']);
        $user->createToken('post', ['post-create']);

        $user = User::create([
            'name' => 'Adm',
            'email' => 'adm@correo.com',
            'password' => 'Adm123456',
            'remember_token' => 'Adm123456',
            'role_type' =>User::ALLROLES['Administrator'],
        ]);
        $user->createToken('profile', ['user-list','user-create','user-edit', 'user-show', 'user-delete']);
        $user->createToken('operation', ['product-list','product-show', 'product-create','product-edit','product-delete']);
        $user->createToken('api', ['product-import','app-validation']);
        $user->createToken('post', ['post-create']);

        Trade::create([
            'name' => 'PRUEBAS 1',
            'email' => 'test@correo.com',
            'trade_id' => '402e83e9-cc23-11ee-8452-0e4de3ffebc3',
            'nit' => '001',
            'is_active' => 1,
        ]);
    }
}
