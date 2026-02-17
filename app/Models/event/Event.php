<?php

namespace App\Models\event;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    // Status constants
    const STATUS_OPEN = 1;
    const STATUS_APPROVED_VP = 2;
    const STATUS_ON_PROCESS = 3;
    const STATUS_APPROVED_VP_UMUM = 4;
    const STATUS_REJECT = 5;
    const STATUS_CLOSE_BY_UMUM = 6;
    const STATUS_CLOSE_BY_USER = 7;

    const STATUS_LABELS = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_APPROVED_VP => 'Approved VP',
        self::STATUS_ON_PROCESS => 'On Process',
        self::STATUS_APPROVED_VP_UMUM => 'Approved VP Umum',
        self::STATUS_REJECT => 'Reject',
        self::STATUS_CLOSE_BY_UMUM => 'Close by Umum',
        self::STATUS_CLOSE_BY_USER => 'Close by User',
    ];

    const STATUS_BADGES = [
        self::STATUS_OPEN => 'badge-soft-info',
        self::STATUS_APPROVED_VP => 'badge-soft-primary',
        self::STATUS_ON_PROCESS => 'badge-soft-warning',
        self::STATUS_APPROVED_VP_UMUM => 'badge-soft-success',
        self::STATUS_REJECT => 'badge-soft-danger',
        self::STATUS_CLOSE_BY_UMUM => 'badge-soft-secondary',
        self::STATUS_CLOSE_BY_USER => 'badge-soft-dark',
    ];

    protected $fillable = [
        'uuid',
        'name',
        'id_departemen',
        'name_departemen',
        'status',
        'start_date',
        'end_date',
        'location',
        'image',
        'id_user_created',
        'name_user_created',
        'description',
        'reject_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Relationships
    public function consumtions()
    {
        return $this->hasMany(Consumtion::class, 'id_event');
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class, 'id_event');
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class, 'id_event')->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function isEditable()
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_REJECT]);
    }

    public function isDeletable()
    {
        return $this->status == self::STATUS_OPEN;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS_LABELS[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS_BADGES[$this->status] ?? 'badge-soft-secondary';
    }
}
