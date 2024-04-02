<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        // お気に入りの一覧を取得
        $favorites = Favorite::with('media')->get();

        // お気に入りに関連するメディア情報を処理する
        $favorites->each(function ($favorite) {
            // メディア情報が存在するかどうかを確認し、ポスターパスが存在する場合のみエンコードする
            if ($favorite->media && isset($favorite->media->poster_path)) {
                $this->encodePosterPath($favorite->media);
            }
        });

        // オーバービューが存在する場合、$mediaに追加
        // if ($media) {
        //     $favorite->media = $media;
        // }

        // 取得したデータをビューなどで使用できるように返す
        return response()->json(['favorites' => $favorites]);
    }

    public function toggleFavorite(Request $request)
    {
        $validatedData = $request->validate([
            "media_type" => 'required|string',
            "media_id" => 'required|integer',
        ]);

        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('media_type', $validatedData['media_type'])
            ->where('media_id', $validatedData['media_id'])
            ->first();

        // お気に入りが存在している場合
        if ($existingFavorite) {
            $existingFavorite->delete();
            return response()->json(["status" => "removed"]);
        } else {
            // お気に入りが存在していない場合
            Favorite::create([
                'media_type' => $validatedData['media_type'],
                'media_id' => $validatedData['media_id'],
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            return response()->json(["status" => "added"]);
        }
    }

    public function checkFavoriteStatus(Request $request)
    {
        $validatedData = $request->validate([
            "media_type" => 'required|string',
            "media_id" => 'required|integer',
        ]);

        $isFavorite = Favorite::where('user_id', Auth::id())
            ->where('media_type', $validatedData['media_type'])
            ->where('media_id', $validatedData['media_id'])
            ->exists();

        return response()->json($isFavorite);
    }

    private function encodePosterPath($media)
    {
        // 画像ファイルを読み込み
        $imagePath = storage_path('app/public/images' . $media->poster_path);
        $imageData = file_get_contents($imagePath);

        // 画像ファイルを Base64 エンコード
        $base64Image = base64_encode($imageData);

        // エンコードされたデータを poster_path にセット
        $media->poster_path = $base64Image;
    }
}
