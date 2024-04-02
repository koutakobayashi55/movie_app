<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medias'; // テーブル名を 'medias' に変更

    protected $fillable = [
        'title',
        'media_type',
        'release_date',
        'poster_path',
        'user_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Overviewモデルとのリレーションシップ定義
    public function overviews()
    {
        return $this->hasMany(Overview::class);
    }

    // Favoriteモデルとリレーションシップ定義
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
