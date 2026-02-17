<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StatusLog extends Model
{
    use SoftDeletes;

    protected $table = 'event_status_logs';

    protected $fillable = [
        'uuid',
        'id_event',
        'user_id',
        'user_name',
        'status_from',
        'status_to',
        'description',
    ];

    protected $casts = [
        'status_from' => 'integer',
        'status_to' => 'integer',
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

    public function getStatusFromLabelAttribute()
    {
        return Event::STATUS_LABELS[$this->status_from] ?? '-';
    }

    public function getStatusToLabelAttribute()
    {
        return Event::STATUS_LABELS[$this->status_to] ?? '-';
    }
}
