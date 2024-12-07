<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EppDelivery extends Model
{
    protected $fillable = [
        'employee_id',
        'user_id',
        'warehouse_id',
        'delivery_date',
        'status',
        'comments'
    ];

    protected $casts = [
        'delivery_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(EppDeliveryItem::class);
    }
}