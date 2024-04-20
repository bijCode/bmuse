<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedData extends Model
{
    use HasFactory;
    protected $fillable = [
        'scrap_id',
        'title',
        'episode_notes',
        'audio_url',
        'image_url'
    ];
}
