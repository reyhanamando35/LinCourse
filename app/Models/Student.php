<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grade',
    ];
    

    /**
     * Get the user that owns the student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function studentDetails()
    {
        return $this->hasMany(StudentDetail::class);
    }

    /**
     * Get all of the payments for the student.
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, StudentDetail::class);
    }


    /**
     * Get all of the attempts for the student.
     */
    public function attempts()
    {
        return $this->hasMany(StudentAttempt::class);
    }

    /**
     * The subjects that belong to the student.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_details');
    }

    
}
