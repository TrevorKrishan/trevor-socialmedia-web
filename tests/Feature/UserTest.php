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
        // Storage::fake('profile_images');
       
        $data['name'] =  'John Doe';
        $data['email'] =  'johndoe@testing.com';
        $data['password'] =  '12345678';
        // $data['profile_image'] =  UploadedFile::fake()->image('photo1.jpg');
        
        $user = $this->json('POST', '/user', [
            $data
        ]);

        // $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
        // $this->assertEquals($data['profile_image'], $user->profile_image);

        // Storage::disk('profile_images')->assertExists('photo1.jpg');
    }
}
