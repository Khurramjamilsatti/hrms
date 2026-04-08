<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id', 'certificate_number', 'issue_date',
        'expiry_date', 'certificate_file'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function enrollment()
    {
        return $this->belongsTo(TrainingEnrollment::class, 'enrollment_id');
    }
}
