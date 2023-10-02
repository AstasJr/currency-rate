<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $table = 'currency_rates';

    protected $primaryKey = ['currency_id', 'date'];

    public $incrementing = false;

    protected $fillable = [
        'currency_id',
        'date',
        'rate',
        'base_currency_code',
    ];

    public $timestamps = false;

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
