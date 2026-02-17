<?php

namespace App\Models\masterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FoodLog extends Model
{
    use SoftDeletes;

    protected $table = 'food_logs';

    protected $fillable = [
        'uuid',
        'id_food',
        'type',
        'qty',
        'price_before',
        'price_after',
        'qty_before',
        'qty_after',
        'description',
        'created_by',
        'created_name',
    ];

    protected $casts = [
        'price_before' => 'decimal:2',
        'price_after' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'id_food');
    }
}
