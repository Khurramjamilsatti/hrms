<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_claim_id', 'file_name', 'file_path', 'file_type',
        'file_size', 'description', 'uploaded_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function expenseClaim()
    {
        return $this->belongsTo(ExpenseClaim::class, 'expense_claim_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
