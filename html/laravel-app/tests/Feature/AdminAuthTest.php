<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\TwoFactorCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_valid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => '2段階認証コードを送信しました']);
        
        $this->assertDatabaseHas('two_factor_codes', [
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
        ]);
    }

    public function test_admin_cannot_login_with_invalid_credentials()
    {
        Admin::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_admin_can_verify_2fa_code()
    {
        $admin = Admin::factory()->create();
        
        $code = TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/admin/verify-2fa', [
            'email' => $admin->email,
            'code' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
        
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_cannot_verify_expired_2fa_code()
    {
        $admin = Admin::factory()->create();
        
        TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '123456',
            'expires_at' => now()->subMinutes(1),
        ]);

        $response = $this->postJson('/api/admin/verify-2fa', [
            'email' => $admin->email,
            'code' => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code']);
    }

    public function test_admin_can_logout()
    {
        $admin = Admin::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')
                         ->postJson('/api/admin/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'ログアウトしました']);
        
        $this->assertGuest('admin');
    }
}
