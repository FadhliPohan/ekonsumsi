<?php

namespace App\Models;

use App\Models\masterData\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'id_user',
        'id_departemen',
        'position',
        'phone',
        'address',
        'gender',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}
