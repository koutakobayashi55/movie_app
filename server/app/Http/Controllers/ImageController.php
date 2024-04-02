<?php
namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function medias()
    {
        // Media モデルからデータを取得
        $medias = Media::all();

        // 取得したデータをビューなどで使用できるように返す
        return response()->json(['medias' => $medias]);
    }

    public function upload(Request $request)
    {
        // フォームから送信された画像を取得
        $image = $request->file('poster_path');

        // 画像を保存するディレクトリ
        $directory = 'images';

        // 画像の保存
        $path = $image->store($directory, 'public');

        // images/ を除いた部分を取得
        $cleanPath = str_replace('images', "", $path);

        // Media モデルを作成して保存
        $media = new Media();
        $media->user_id = Auth::id();
        $media->title = $request->input('title');
        $media->media_type = $request->input('media_type');
        $media->release_date = $request->input('release_date');
        $media->poster_path = $cleanPath;
        $media->created_by = Auth::id();
        $media->updated_by = Auth::id();
        $media->save();

        // 画像のパスを返す
        return response()->json(['poster_path' => $cleanPath]);
    }

    public function getMedias()
    {
        $medias = DB::table('medias')
            ->select('id', 'title', 'media_type', 'release_date', 'poster_path')
            ->get();

        // poster_path を Base64 エンコードしてデータを加工
        foreach ($medias as $media) {
            // poster_path を Base64 エンコード
            $this->encodePosterPath($media);
        }

        return response()->json($medias);
    }

    public function getMediaById($media_type, $media_id)
    {
        // if ($media_id) {
        //     return response()->json(['media_id' => 'media_idのレスポンス'], 404);
        // }

        $media = DB::table('medias')
            ->select('id', 'title', 'media_type', 'release_date', 'poster_path')
            ->where('id', $media_id)
            ->first();

        // オーバービューの取得
        $overview = DB::table('overviews')
        ->select('id', 'overview', 'media_id')
        ->where('media_id', $media_id)
        ->first();

        // オーバービューが存在する場合、$mediaに追加
        if ($overview) {
            $media->overview = $overview;
        }

        // poster_path が存在する場合のみエンコード
        if ($media && isset($media->poster_path)) {
            $this->encodePosterPath($media);
        }

        return response()->json($media);
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
