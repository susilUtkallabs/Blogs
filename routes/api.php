<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['middleware' => 'api'], function($routes){

    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/profile', [UserController::class, 'profile']);
    Route::post('/refresh', [UserController::class, 'refresh']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/blogs', [BlogController::class, 'blogs']);
    Route::get('/blogs/{id}', [BlogController::class, 'singleblog']);
    Route::get('/search', [BlogController::class, 'search']);
    Route::post('/create', [BlogController::class, 'create']);
    Route::post('/deleteblog/{id}', [BlogController::class, 'deleteblog']);
    Route::post('/createcategory', [BlogController::class, 'createCategory']);
    Route::post('/addcategorytoblog', [BlogController::class, 'addCategoryToBlog']);

    Route::post('blogs/{id}/addfavorite', [BlogController::class, 'addFavorite']);
    Route::post('blogs/{id}/removefavorite', [BlogController::class, 'removeFavorite']);
    
});