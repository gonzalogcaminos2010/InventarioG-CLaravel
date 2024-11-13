<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relación con warehouse_items
    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    // Relación con items a través de warehouse_items
    public function items()
    {
        return $this->belongsToMany(Item::class, 'warehouse_items')
                    ->withPivot('current_stock');
    }
}