<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'method',
        'status',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string', // Enum: approved, rejected, pending
        'method' => 'string', // Enum: credit_card, boleto, pix
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}