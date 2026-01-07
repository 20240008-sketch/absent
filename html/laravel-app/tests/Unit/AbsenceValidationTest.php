<?php

namespace Tests\Unit;

use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AbsenceValidationTest extends TestCase
{
    public function test_absence_requires_required_fields()
    {
        $request = new StoreAbsenceRequest();
        
        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('seito_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('division', $validator->errors()->toArray());
        $this->assertArrayHasKey('reason', $validator->errors()->toArray());
        $this->assertArrayHasKey('absence_date', $validator->errors()->toArray());
    }

    public function test_division_must_be_valid_value()
    {
        $request = new StoreAbsenceRequest();
        
        $validData = [
            'seito_id' => 1001,
            'division' => '欠席',
            'reason' => 'テスト理由',
            'absence_date' => '2025-12-19',
        ];

        $validator = Validator::make($validData, $request->rules());
        $this->assertFalse($validator->fails());

        $invalidData = [
            'seito_id' => 1001,
            'division' => '早退',
            'reason' => 'テスト理由',
            'absence_date' => '2025-12-19',
        ];

        $validator = Validator::make($invalidData, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('division', $validator->errors()->toArray());
    }

    public function test_absence_date_must_be_valid_date()
    {
        $request = new StoreAbsenceRequest();
        
        $data = [
            'seito_id' => 1001,
            'division' => '欠席',
            'reason' => 'テスト理由',
            'absence_date' => 'invalid-date',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('absence_date', $validator->errors()->toArray());
    }
}
