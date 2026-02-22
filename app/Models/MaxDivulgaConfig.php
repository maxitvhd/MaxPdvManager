<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaxDivulgaConfig extends Model
{
    use HasFactory;

    protected $table = 'max_divulga_configs';

    protected $fillable = [
        'provider_ia',
        'api_key_ia',
        'model_ia',
        'provider_tts',
        'tts_host',
        'tts_api_key',
        'tts_model',
        'tts_voice',
        'tts_default_speed',
        'tts_default_noise_scale',
        'tts_default_noise_w',
        'facebook_client_id',
        'facebook_client_secret',
        'google_client_id',
        'google_client_secret'
    ];
}
