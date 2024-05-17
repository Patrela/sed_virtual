<?php

namespace Tests\Feature;

use console;
use Tests\TestCase;
use App\Models\User;
use App\Models\Trade;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SanctumTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        // $user =  User::factory()->create([
        //         'email' => 'test@example.com',
        //         'name' => 'Test User',
        //         'password' => 'password',
        //     ]);
        User::factory()->create([
            'name' => 'Pat User',
            'email' => 'pat@correo.com',
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => 'SED',
            'user_id' => 'SED_TRADE_1',
            'role_type' =>User::ALLROLES['Trade'],
        ]);

        $response = $this->post('/api/login', [
            'email' => 'pat@correo.com',
            'password' => 'Test123456', //$user->password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['name','email'],
            'token',
        ]);
    }
    public function test_user_can_see_auth_routes(): void
    {

        $user = User::factory()->create([
            'name' => 'Pat2 User',
            'email' => 'pat2@correo.com',
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => 'SED',
            'user_id' => 'SED_TRADE_2',
            'role_type' =>User::ALLROLES['Trade'],
        ]);

        $response = $this->post('/api/login', [
            'email' => 'pat2@correo.com',
            'password' => 'Test123456', //$user->password,
        ]);
        $token = $response->json('token');
        $response =  $this->withHeader('Authorization',"Bearer {$token}")
                        ->get('api/user');
        //dd($response->json());
        $response->assertJson([
            'id' => $user->id,
            'name'=>$user->name,
            'email'=>$user->email,
        ]);
    }

    public function test_user_can_request_with_permissions(): void{
        $user = User::factory()->create([
            'name' => 'Pat3 User',
            'email' => 'pat3@correo.com',
            'password' => 'Test123456',
            'remember_token' => 'Test123456',
            'trade_id' => 'SED',
            'user_id' => 'SED_TRADE_3',
            'role_type' =>User::ALLROLES['Trade'],
        ]);

        Sanctum::actingAs($user, ['post-create']);

        $response = $this->getJson('api/post/create',[
            'title' => 'Create my first post',
            'content' =>'my-first pretty with ability to create-post',
        ]);
        //dd($response->json());
        $response->assertStatus(200);
    }

    public function test_vtex_with_headers(): void
    {
        $company= "402e83e9-cc23-11ee-8452-0e4de3ffebc3";
        $useremail = "analista.procesos@sedintl.com";
        $name = "Saly";
        $token = "HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y";
        $user =  User::factory()->create([
            'email' => $useremail,
            'name' =>  $name,
            'trade_id' => $company,
            'user_id' => 'XGreratPath',
            'password' => 'password',
            'remember_token' => 'password',
            'role_type' =>User::ALLROLES['Trade'],
        ]);
        Sanctum::actingAs($user, ['user-create']);

        $headers = [
            'x-api-company' => $company,
            'x-api-User' => $useremail,
            'Authorization' => 'Bearer '.$token,
        ];

        $response = $this->getJson('/api/vtex/?name=' .$name, $headers);
        //dd($response->json());
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'company',
                    'email',
                    'name', // Adjust if the response doesn't include name
                ]);
    }


    public function test_vtex_without_params(): void
    {
        $company= "Company";
        $token = "HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y_222";
        $name = "AnyName";
        $response = $this->withHeaders([
            'x-api-company' => $company,
            'Authorization'=>'Bearer '.$token
        ])->getJson('/api/vtex/?name=' .$name);

        $response->assertStatus(401);
    }
    public function test_trade_valid_token()
    {
        // Arrange (Create a trade with the specified token)
        $token = "Any";
        $trade = Trade::create([
            'name' => 'tesrtin Sanctum',
            'email' => 'test@correo.com',
            'trade_id' => $token,
            'nit' => '001',
            'is_active' => 1,
        ]);

        // Act (Call the tokenExists method)
        $response = $this->getJson("trades/id/$token"); // Assuming a route for the method

        // Assert (Verify the response is 1 for a valid token)
        $response->assertStatus(200)
                ->assertJsonFragment([1]); // Assuming JSON response
    }

    public function test_trade_invalid_token()
    {
        // Arrange (No trade created with the specified token)
        $token = "invalid_token";

        // Act (Call the tokenExists method)
        $response = $this->getJson("trades/id/$token"); // Assuming a route for the method

        // Assert (Verify the response is 0 for an invalid token)
        $response->assertStatus(200)
                ->assertJsonFragment([0]); // Assuming JSON response
    }

    public function test_sed_products(): void
    {
        $response = $this->get('/api/sed/sync');

        $response->assertStatus(200);
    }
    public function test_sed_products_with_parameters(): void
    {
        $department = 'Computadores';
        $brand = 'LENOVO';

        $response = $this->get("api/sed/sync/dep/department=$department&brand=$brand");
        //dd($response->json());
        $response->assertStatus(200);
                // Add assertions for expected response data based on your API logic
                // (e.g., JSON structure, presence of specific fields)
                // ...;
    }
}
