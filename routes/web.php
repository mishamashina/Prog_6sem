<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Comment

Route::controller(CommentController::class)->group(function(){
    Route::post('comment', 'store');
    Route::get('comment', 'index')->name('comment.index');
    Route::get('comment/{comment}/accept', 'accept');
    Route::get('comment/{comment}/reject', 'reject');
});

// Route::get('/', function () {
//     return view('layout');
// });

Route::get('/contacts', function(){
    $contacts = [
        'univer' => 'Polytech',
        'phone' => '8(495)232-2232',
        'email' => 'mospolytech@mospolytech.ru'
    ];
    return view('main.contact', ['contacts'=>$contacts]);
});

Route::get('/', [MainController::class, 'index']);
Route::get('/full-img/{img}', [MainController::class, 'show']);

//Auth

Route::get('/signin', [AuthController::class, 'signin']);
Route::post('/registr', [AuthController::class, 'registr']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup']);
Route::get('/logout', [AuthController::class, 'logout']);

//Article

Route::resource('article', ArticleController::class)->middleware('auth:sanctum');
