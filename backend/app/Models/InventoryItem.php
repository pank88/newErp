<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name', 'sku', 'quantity', 'location', 'cost_price', 'sale_price'
    ];
}
