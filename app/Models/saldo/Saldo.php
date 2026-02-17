<?php

namespace App\Models\saldo;

use App\Models\masterData\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Saldo extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id_departemen',
        'uuid',
        'saldo',
        'updated_by',
        'updated_name',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function logSaldos()
    {
        return $this->hasMany(logSaldo::class, 'id_saldo');
    }
}
