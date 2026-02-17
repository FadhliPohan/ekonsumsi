<?php

namespace App\Models\masterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Food extends Model
{
    use SoftDeletes;

    protected $table = 'foods';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'image',
        'price',
        'qty_available',
        'is_active',
        'updated_by',
        'updated_name',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function logs()
    {
        return $this->hasMany(FoodLog::class, 'id_food')->orderBy('created_at', 'desc');
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }
}
