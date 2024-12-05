<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'part_number',
        'name',
        'description',
        'category_id',
        'brand_id',
        'minimum_stock',
        'size_id',         // Añadido
        'is_epp',          // Añadido
        
    ];

    protected $casts = [
        'is_epp' => 'boolean',
        
    ];

    // Relación con Talla
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    // Relaciones existentes
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    // Nueva relación para movimientos
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    // Accesor para obtener el stock total
    public function getTotalStockAttribute()
    {
        return $this->warehouseItems->sum('current_stock');
    }
}
