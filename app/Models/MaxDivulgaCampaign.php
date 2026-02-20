<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaxDivulgaCampaign extends Model
{
    use HasFactory;

    protected $table = 'max_divulga_campaigns';

    protected $fillable = [
        'tenant_id',
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
        'next_run_at'
    ];

    protected $casts = [
        'channels' => 'array',
        'product_selection_rule' => 'array',
        'discount_rules' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function theme()
    {
        return $this->belongsTo(MaxDivulgaTheme::class, 'theme_id');
    }
}
