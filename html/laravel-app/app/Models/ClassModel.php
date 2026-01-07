<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;
    protected $table = 'classes';

    protected $fillable = [
        'class_id',
        'class_name',
        'teacher_name',
        'teacher_email',
        'year_id',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }
}
