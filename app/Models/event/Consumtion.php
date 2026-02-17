<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Consumtion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'id_event',
        'id_food',
        'food_name',
        'id_departemen',
        'id_user',
        'user_name',
        'qty',
        'price',
        'total',
        'status',
        'description',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
            // Auto-calculate total
            $model->total = $model->qty * $model->price;
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'id_event');
    }

    public function food()
    {
        return $this->belongsTo(\App\Models\masterData\Food::class, 'id_food');
    }
}
