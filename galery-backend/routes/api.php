<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\GalleryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(GalleryController::class)->group(function(){
    Route::get('/my-galleries', 'myGalleries')->middleware('auth');
    Route::post('create','store')->middleware('auth');
    Route::get('/','index');
    Route::get('/galleries/{id}','show')->middleware('auth');
    Route::put('edit-gallery/{id}','update')->middleware('auth');
    Route::delete('{id}','destroy')->middleware('auth');
    Route::get('/authors/{id}','getUserGalleries')->middleware('auth');
});

Route::controller(CommentsController::class)->group(function(){
    Route::post('/storecomment','store')->middleware('auth');
    Route::post('/getcomment','show')->middleware('auth');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
