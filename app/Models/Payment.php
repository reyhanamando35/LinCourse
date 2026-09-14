<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_detail_id',
        'amount',
        'month_year',
        'payment_proof',
        'status',
        'verified_by',
    ];

    public function studentDetail()
    {
        return $this->belongsTo(StudentDetail::class);
    }
    
    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }
}
