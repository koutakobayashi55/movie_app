<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        "media_id",
        "media_type",
        "user_id",
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    // メディアとのリレーションを定義
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
