<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'content',
        'pictures'
    ];
    
    protected $casts = [
        'pictures' => 'array', 
    ];
    
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function practices()
    {
        return $this->hasMany(Practice::class);
    }
}
