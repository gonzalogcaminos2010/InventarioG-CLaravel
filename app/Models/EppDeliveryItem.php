<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EppDeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'epp_delivery_id',
        'item_id',
        'quantity',
        'estimated_return_date'
    ];

    protected $casts = [
        'estimated_return_date' => 'date'
    ];

    public function eppDelivery()
    {
        return $this->belongsTo(EppDelivery::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
