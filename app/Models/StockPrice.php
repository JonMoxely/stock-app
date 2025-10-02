<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    protected $fillable = [
        'company_id',
        'date',
        'price',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
