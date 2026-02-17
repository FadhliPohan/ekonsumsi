<?php

namespace App\Models\masterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Departemen extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'uuid',
        'name',
        'code_departement',
        'location',
        'is_active',
        'description',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function saldo()
    {
        return $this->hasOne(\App\Models\saldo\Saldo::class, 'id_departemen');
    }
}
