<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TvDoorLayout extends Model
{
    use HasFactory;

    protected $table = 'tv_door_layouts';

    protected $fillable = ['loja_id', 'name', 'content', 'preview_path', 'resolution'];

    protected $casts = [
        'content' => 'array'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }
}
