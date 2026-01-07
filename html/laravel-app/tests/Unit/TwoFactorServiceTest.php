<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\ParentModel;
use App\Models\TwoFactorCode;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TwoFactorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TwoFactorService();
        Mail::fake();
    }

    public function test_can_generate_2fa_code_for_admin()
    {
        $admin = Admin::factory()->create();

        $this->service->generate($admin);

        $this->assertDatabaseHas('two_factor_codes', [
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
        ]);

        $code = TwoFactorCode::where('authenticatable_id', $admin->id)->first();
        $this->assertNotNull($code);
        $this->assertEquals(6, strlen($code->code));
        $this->assertTrue($code->expires_at->isFuture());
    }

    public function test_can_generate_2fa_code_for_parent()
    {
        $parent = ParentModel::factory()->create();

        $this->service->generate($parent);

        $this->assertDatabaseHas('two_factor_codes', [
            'authenticatable_id' => $parent->id,
            'authenticatable_type' => ParentModel::class,
        ]);
    }

    public function test_can_verify_valid_2fa_code()
    {
        $admin = Admin::factory()->create();
        
        TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $result = $this->service->verify($admin, '123456');

        $this->assertTrue($result);
        
        // コードは検証後に削除される
        $this->assertDatabaseMissing('two_factor_codes', [
            'authenticatable_id' => $admin->id,
            'code' => '123456',
        ]);
    }

    public function test_cannot_verify_invalid_2fa_code()
    {
        $admin = Admin::factory()->create();
        
        TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $result = $this->service->verify($admin, '999999');

        $this->assertFalse($result);
    }

    public function test_cannot_verify_expired_2fa_code()
    {
        $admin = Admin::factory()->create();
        
        TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '123456',
            'expires_at' => now()->subMinutes(1),
        ]);

        $result = $this->service->verify($admin, '123456');

        $this->assertFalse($result);
    }

    public function test_old_codes_are_deleted_when_generating_new_code()
    {
        $admin = Admin::factory()->create();
        
        // 古いコードを作成
        TwoFactorCode::create([
            'authenticatable_id' => $admin->id,
            'authenticatable_type' => Admin::class,
            'code' => '111111',
            'expires_at' => now()->addMinutes(10),
        ]);

        // 新しいコードを生成
        $this->service->generate($admin);

        // 古いコードが削除されていることを確認
        $this->assertDatabaseMissing('two_factor_codes', [
            'code' => '111111',
        ]);

        // 新しいコードのみが存在することを確認
        $this->assertEquals(1, TwoFactorCode::where('authenticatable_id', $admin->id)->count());
    }
}
