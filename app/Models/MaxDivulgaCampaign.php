<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaxDivulgaCampaign extends Model
{
    use HasFactory;

    protected $table = 'max_divulga_campaigns';

    protected $fillable = [
        'parent_id',
        'tenant_id',
        'loja_id',
        'name',
        'type',
        'channels',
        'schedule_type',
        'product_selection_rule',
        'discount_rules',
        'theme_id',
        'persona',
        'format',
        'status',
        'last_run_at',
        'next_run_at',
        'copy',
        'copy_acompanhamento',
        'copy_locucao',
        'file_path',
        'is_scheduled',
        'scheduled_days',
        'scheduled_times',
        'is_active',
        'voice',
        'audio_speed',
        'noise_scale',
        'noise_w',
        'audio_file_path',
        'bg_audio',
        'bg_volume'
    ];

    protected $casts = [
        'channels' => 'array',
        'product_selection_rule' => 'array',
        'discount_rules' => 'array',
        'scheduled_days' => 'array',
        'scheduled_times' => 'array',
        'is_scheduled' => 'boolean',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(MaxDivulgaCampaign::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MaxDivulgaCampaign::class, 'parent_id');
    }

    public function theme()
    {
        return $this->belongsTo(MaxDivulgaTheme::class, 'theme_id');
    }
}
