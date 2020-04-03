<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuth()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function testUserCreate()
    {
        Storage::fake('profile_images');
       
        $data['name'] =  $this->faker->name;
        $data['email'] =  $this->faker->safeEmail;
        $data['password'] =  '12345678';
        $data['profile_image'] =  UploadedFile::fake()->image('photo1.jpg');
        
        $response = $this->json('POST', '/user',$data);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $response->assertJson(['message' => 'User Registerd Successfully.']);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email']
        ]);
        
    }

    public function testUserLogin(){
        $data['email'] =  'johndoe1@testing.com';
        $data['password'] =  '12345678';

        $response = $this->json('POST', '/login',$data);
        $response->assertStatus(302);
        $response->assertRedirect('/');

        $user = User::where('email', $data['email'])->first();
        
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
    }
}
