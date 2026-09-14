<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
    ];

    /**
     * Get the module that owns the practice.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get all of the questions for the practice.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
