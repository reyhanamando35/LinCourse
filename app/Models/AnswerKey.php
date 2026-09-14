<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'key_text',
        'key_file',
    ];

    /**
     * Get the question that owns the answer key.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
