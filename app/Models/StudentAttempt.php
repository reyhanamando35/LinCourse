<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_detail_id',
        'question_id',
        'answer_text',
        'answer_file',
        'is_correct',
        'feedback',
    ];
    
    public function studentDetail()
    {
        return $this->belongsTo(StudentDetail::class, 'student_detail_id');
    }
    /**
     * Get the student who made the attempt.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the question that was attempted.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
