<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'additional_price',
        'stock_quantity',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variation_id');
    }

    public function image()
    {
        return $this->hasOne(ProductVariationImage::class, 'variation_id');
    }
}