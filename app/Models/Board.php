<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Obtener el usuario creador de la pizarra
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
