<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $fillable = [
        'paypal_order_id',
        'payer_email',
        'amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}