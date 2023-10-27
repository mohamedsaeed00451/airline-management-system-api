<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sellingVisas()
    {
        return $this->hasMany(Visa::class, 'from_company_id');
    }

    public function executionVisas()
    {
        return $this->hasMany(Visa::class, 'to_company_id');
    }
}
