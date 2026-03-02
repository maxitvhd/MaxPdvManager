<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TvDoorPlayer extends Model
{
    use HasFactory;

    protected $table = 'tv_door_players';

    protected $fillable = [
        'loja_id',
        'name',
        'pairing_code',
        'device_token',
        'status',
        'last_seen_at',
        'meta_data'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'last_seen_at' => 'datetime'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function schedules()
    {
        return $this->hasMany(TvDoorSchedule::class, 'player_id');
    }
}
