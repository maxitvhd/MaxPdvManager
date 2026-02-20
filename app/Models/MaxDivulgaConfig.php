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
        'tts_voice'
    ];
}
