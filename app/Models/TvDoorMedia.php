<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TvDoorMedia extends Model
{
    use HasFactory;

    protected $table = 'tv_door_media';

    protected $fillable = [
        'loja_id',
        'category_id',
        'name',
        'file_path',
        'type',
        'duration'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function category()
    {
        return $this->belongsTo(TvDoorCategory::class, 'category_id');
    }
}
