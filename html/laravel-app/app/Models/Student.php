<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'seito_id',
        'seito_name',
        'seito_number',
        'class_id',
        'seito_initial_email',
        'seito_email',
    ];

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }

    public function parents(): HasMany
    {
        return $this->hasMany(ParentModel::class, 'seito_id', 'seito_id');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'seito_id', 'seito_id');
    }
}
