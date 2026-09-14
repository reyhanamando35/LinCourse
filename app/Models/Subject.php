<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'picture',
        'price',
    ];

    /**
     * Get all of the modules for the subject.
     */
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function studentDetails()
    {
        return $this->hasMany(StudentDetail::class);
    }

    /**
     * The students that belong to the subject.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_details');
    }

    /**
     * The teachers that belong to the subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }
}
