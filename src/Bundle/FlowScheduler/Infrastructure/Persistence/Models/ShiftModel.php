<?php

namespace App\Bundle\FlowScheduler\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ShiftModel extends Model
{
    use HasUuids;

    protected $table = 'shifts';

    protected $fillable = [
        'id',
        'title',
        'description',
        'type',
        'required_skill',
        'start_time',
        'end_time',
        'status',
        'user_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'user_id' => 'string',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}