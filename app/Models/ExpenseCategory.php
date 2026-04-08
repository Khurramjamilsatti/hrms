<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'requires_receipt', 'max_amount', 'is_active',
        // New comprehensive fields
        'category_code', 'per_diem_rate', 'is_taxable', 'tax_rate',
        'requires_approval', 'approval_threshold', 'gl_account_code'
    ];

    protected $casts = [
        'requires_receipt' => 'boolean',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'per_diem_rate' => 'decimal:2',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:2',
        'requires_approval' => 'boolean',
        'approval_threshold' => 'decimal:2',
    ];

    public function claims()
    {
        return $this->hasMany(ExpenseClaim::class, 'category_id');
    }
}
