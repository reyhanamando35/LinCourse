<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_id',
        'content_text',
        'content_file',
    ];

    /**
     * Get the practice that owns the question.
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    /**
     * Get the answer key for the question.
     */
    public function answerKey()
    {
        return $this->hasOne(AnswerKey::class);
    }

    /**
     * Get all of the attempts for the question.
     */
    public function attempts()
    {
        return $this->hasMany(StudentAttempt::class);
    }
}
