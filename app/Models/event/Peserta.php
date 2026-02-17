<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Peserta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'id_event',
        'user_id',
        'user_name',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'id_event');
    }

    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? 'Hadir' : 'Tidak Hadir';
    }
}
