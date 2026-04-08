<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'calculation_type',
        'is_taxable',
        'is_mandatory',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_taxable' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function employeeSalaryComponents()
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }
}
