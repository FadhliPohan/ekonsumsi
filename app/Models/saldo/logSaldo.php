<?php

namespace App\Models\saldo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class logSaldo extends Model
{
    use SoftDeletes;

    protected $table = 'log_saldos';

    protected $fillable = [
        'id_saldo',
        'saldo',
        'description',
        'created_by',
        'created_name',
        'updated_by',
        'updated_name',
        'status'
    ];

    public function saldo()
    {
        return $this->belongsTo(Saldo::class, 'id_saldo');
    }
}
