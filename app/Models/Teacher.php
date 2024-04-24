<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends BaseModel
{
    use HasFactory;

    protected $table = 'teachers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'teacher_code',
        'teacher_name'
    ];

    public function class(): HasOne
    {
        return $this->hasOne(StudentClass::class, 'teacher_code', 'teacher_code');
    }
}
