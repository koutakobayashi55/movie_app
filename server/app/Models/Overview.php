<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overview extends Model
{
    use HasFactory;

    protected $fillable = [
        'overview',
        'media_id',
        'language',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    // Mediaモデルとのリレーションシップを定義
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
