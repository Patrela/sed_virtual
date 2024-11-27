<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Trade;
use App\Models\Product;
use App\Models\Provider;
use App\Jobs\ImportFixedStaff;
use App\Jobs\ImportProductGroups;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => '1',
            'user_id' => 'test',
            'role_type' =>User::ALLROLES['Trade'],
        ]);
        $user = User::create([
            'name' => 'Saly',
            'email' => 'analista.procesos@sedintl.com',
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => '1',
            'user_id' => 'analista.procesos',
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
            'trade_id' => '1',
            'user_id' => 'SED_ADMIN',
            'role_type' =>User::ALLROLES['Administrator'],
        ]);
        $user->createToken('profile', ['user-list','user-create','user-edit', 'user-show', 'user-delete']);
        $user->createToken('operation', ['product-list','product-show', 'product-create','product-edit','product-delete']);
        $user->createToken('api', ['product-import','app-validation']);
        $user->createToken('post', ['post-create']);

        Trade::create([
            'name' => 'SED International de Colombia S.A.S',
            'email' => 'contactenos@sedintl.com',
            'trade_id' => '1',
            'nit' => '8300361083',
            'is_active' => 1,
        ]);

        Provider::create([
            'id_provider' => '1',
            'name' => 'SED International de Colombia S.A.S',
            'nit' => '8300361083',
            'email' => 'contactenos@sedintl.com',
        ]);

        Product::create([
            'name' => 'test_product',
            'short_description' => 'test_product',
            'part_num' => '_001',
            'sku' => '_001',
            'id_provider' => '1',
            'stock_quantity' => 0,
            'is_active' => 0,
        ]);
        //Import SED Categories Groups and the Excel Original Staff- Prevents load views errors
        ImportProductGroups::dispatchAfterResponse();
        ImportFixedStaff::dispatchAfterResponse();
    }
}
