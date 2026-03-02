<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TvDoorSchedule extends Model
{
    use HasFactory;

    protected $table = 'tv_door_schedules';

    protected $fillable = [
        'player_id',
        'schedulable_id',
        'schedulable_type',
        'content_items',
        'days',
        'start_time',
        'end_time',
        'time_slots',
        'priority',
        'is_active',
        'resolution'
    ];

    protected $casts = [
        'days' => 'array',
        'content_items' => 'array',
        'time_slots' => 'array',
        'is_active' => 'boolean'
    ];

    public function player()
    {
        return $this->belongsTo(TvDoorPlayer::class, 'player_id');
    }

    /**
     * Get the parent schedulable model (Layout, Media, or MaxDivulgaCampaign).
     */
    public function schedulable()
    {
        return $this->morphTo();
    }
}
