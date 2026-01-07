<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ClassModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_list_classes()
    {
        ClassModel::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
                         ->getJson('/api/admin/classes');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['class_id', 'class_name', '担任', '年度']
                     ]
                 ]);
    }

    public function test_admin_can_create_class()
    {
        $response = $this->actingAs($this->admin, 'admin')
                         ->postJson('/api/admin/classes', [
                             'class_id' => 'A101',
                             'class_name' => '1年A組',
                             '担任' => '山田太郎',
                             '年度' => 2025,
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'class_id' => 'A101',
                     'class_name' => '1年A組',
                 ]);

        $this->assertDatabaseHas('classes', [
            'class_id' => 'A101',
            'class_name' => '1年A組',
        ]);
    }

    public function test_admin_can_update_class()
    {
        $class = ClassModel::factory()->create([
            'class_id' => 'A101',
            'class_name' => '1年A組',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
                         ->putJson("/api/admin/classes/A101", [
                             'class_id' => 'A101',
                             'class_name' => '1年B組',
                             '担任' => '佐藤花子',
                             '年度' => 2025,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'class_name' => '1年B組',
                 ]);
    }

    public function test_admin_can_delete_class()
    {
        $class = ClassModel::factory()->create(['class_id' => 'A101']);

        $response = $this->actingAs($this->admin, 'admin')
                         ->deleteJson("/api/admin/classes/A101");

        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('classes', [
            'class_id' => 'A101',
        ]);
    }

    public function test_guest_cannot_access_classes()
    {
        $response = $this->getJson('/api/admin/classes');
        $response->assertStatus(401);
    }
}
