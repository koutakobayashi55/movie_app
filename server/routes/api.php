<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// レビュー機能
Route::get('/reviews/{media_type}/{media_id}', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);
Route::put('/review/{review}', [ReviewController::class, 'update']);
Route::get('/review/{review}', [ReviewController::class, 'show']);

// コメント機能
Route::post('/comments', [CommentController::class, 'store']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
Route::put('/comments/{comment}', [CommentController::class, 'update']);

// お気に入り機能
Route::get('/favorites', [FavoriteController::class, 'index']);
Route::post('/favorites', [FavoriteController::class, 'toggleFavorite']);
Route::get('/favorites/status', [FavoriteController::class, 'checkFavoriteStatus']);

// 画像アップロード用のルート
Route::post('/image', [ImageController::class, 'upload']);
Route::get('/home', [ImageController::class, 'getMedias']);
Route::get('/detail/{media_type}/{media_id}', [ImageController::class, 'getMediaById']);
