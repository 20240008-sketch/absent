<?php

namespace Tests\Unit;

use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ClassValidationTest extends TestCase
{
    public function test_class_requires_all_fields()
    {
        $request = new StoreClassRequest();
        
        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('class_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('class_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('teacher_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('year_id', $validator->errors()->toArray());
    }

    public function test_class_id_must_be_unique()
    {
        $data = [
            'class_id' => 'A101',
            'class_name' => '1年A組',
            'teacher_name' => '山田太郎',
            'teacher_email' => 'teacher@example.com',
            'year_id' => 2025,
        ];

        $request = new StoreClassRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_year_must_be_integer()
    {
        $data = [
            'class_id' => 'A101',
            'class_name' => '1年A組',
            'teacher_name' => '山田太郎',
            'year_id' => 'invalid',
        ];

        $request = new StoreClassRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('year_id', $validator->errors()->toArray());
    }
}
