<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\ClassModel;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsenceCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $parent;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        $class = ClassModel::factory()->create(['class_id' => 'A101']);
        
        $this->student = Student::factory()->create([
            'seito_id' => '1001',
            'class_id' => 'A101',
        ]);
        
        $this->parent = ParentModel::factory()->create([
            'seito_id' => '1001',
        ]);
    }

    public function test_parent_can_list_absences()
    {
        Absence::factory()->count(3)->create([
            'seito_id' => '1001',
        ]);

        $response = $this->actingAs($this->parent, 'parent')
                         ->getJson('/api/parent/absences');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_parent_can_create_absence()
    {
        $response = $this->actingAs($this->parent, 'parent')
                         ->postJson('/api/parent/absences', [
                             'division' => '欠席',
                             'reason' => '風邪のため',
                             'scheduled_time' => null,
                             'absence_date' => '2025-12-19',
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'seito_id' => '1001',
                     'division' => '欠席',
                     'reason' => '風邪のため',
                 ]);

        $this->assertDatabaseHas('absences', [
            'seito_id' => '1001',
            'division' => '欠席',
        ]);
    }

    public function test_parent_can_update_absence()
    {
        $absence = Absence::factory()->create([
            'seito_id' => '1001',
            'division' => '欠席',
            'reason' => '風邪のため',
        ]);

        $response = $this->actingAs($this->parent, 'parent')
                         ->putJson("/api/parent/absences/{$absence->id}", [
                             'division' => '遅刻',
                             'reason' => '体調不良のため',
                             'scheduled_time' => '10:00:00',
                             'absence_date' => $absence->absence_date,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'division' => '遅刻',
                     'reason' => '体調不良のため',
                 ]);
    }

    public function test_parent_can_delete_absence()
    {
        $absence = Absence::factory()->create([
            'seito_id' => '1001',
        ]);

        $response = $this->actingAs($this->parent, 'parent')
                         ->deleteJson("/api/parent/absences/{$absence->id}");

        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('absences', [
            'id' => $absence->id,
        ]);
    }

    public function test_parent_cannot_access_other_students_absences()
    {
        $otherStudent = Student::factory()->create([
            'seito_id' => '2001',
            'class_id' => 'A101',
        ]);
        
        $absence = Absence::factory()->create([
            'seito_id' => '2001',
        ]);

        $response = $this->actingAs($this->parent, 'parent')
                         ->getJson("/api/parent/absences/{$absence->id}");

        $response->assertStatus(404);
    }
}
