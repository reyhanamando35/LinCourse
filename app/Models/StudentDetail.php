<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'status',
    ];

    /**
     * Get the student for this enrollment detail.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject for this enrollment detail.
     * INI FUNGSI YANG HILANG.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the admin who verified the payment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
