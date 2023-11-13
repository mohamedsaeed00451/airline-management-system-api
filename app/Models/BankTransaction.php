<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(BankTransaction::class, 'bank_id');
    }

    public function visa()
    {
        return $this->belongsTo(Visa::class, 'visa_id');
    }
}
