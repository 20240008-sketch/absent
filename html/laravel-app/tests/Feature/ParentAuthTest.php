<?php

namespace Tests\Feature;

use App\Models\ParentModel;
use App\Models\TwoFactorCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_login_with_valid_credentials()
    {
        $parent = ParentModel::factory()->create([
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'initial_password' => false,
        ]);

        $response = $this->postJson('/api/parent/login', [
            'email' => 'parent@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => '2段階認証コードを送信しました']);
    }

    public function test_parent_with_initial_password_must_change_password()
    {
        $parent = ParentModel::factory()->create([
            'email' => 'parent@test.com',
            'password' => bcrypt('initial123'),
            'initial_password' => true,
        ]);

        $response = $this->postJson('/api/parent/login', [
            'email' => 'parent@test.com',
            'password' => 'initial123',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['requires_password_change' => true]);
    }

    public function test_parent_can_change_initial_password()
    {
        $parent = ParentModel::factory()->create([
            'email' => 'parent@test.com',
            'password' => bcrypt('initial123'),
            'initial_password' => true,
        ]);

        $response = $this->postJson('/api/parent/change-password', [
            'email' => 'parent@test.com',
            'current_password' => 'initial123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'パスワードを変更しました']);
        
        $parent->refresh();
        $this->assertFalse($parent->initial_password);
    }

    public function test_parent_can_verify_2fa_code()
    {
        $parent = ParentModel::factory()->create([
            'initial_password' => false,
        ]);
        
        TwoFactorCode::create([
            'authenticatable_id' => $parent->id,
            'authenticatable_type' => ParentModel::class,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/parent/verify-2fa', [
            'email' => $parent->email,
            'code' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    public function test_parent_can_logout()
    {
        $parent = ParentModel::factory()->create();
        
        $response = $this->actingAs($parent, 'parent')
                         ->postJson('/api/parent/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'ログアウトしました']);
    }
}
