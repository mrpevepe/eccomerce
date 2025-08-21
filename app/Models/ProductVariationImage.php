<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'variation_id',
        'path',
    ];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}